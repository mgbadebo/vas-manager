<?php

namespace App\Http\Controllers\Revenue;

use App\Http\Controllers\Controller;
use App\Models\{
    Aggregator,
    Bank,
    ExpenseRecipient,
    KeyStakeholder,
    MandatoryExpenseType,
    MNO,
    OperationalCategory,
    Service,
    ServicePartnerShare,
    VASRevenue
};
use App\Services\RevenueCalculator;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VASRevenueController extends Controller
{
    public function index()
    {
        $items = VASRevenue::with(['service','mno','aggregator','partnerShareSummary','bank'])
            ->latest('payment_date')->latest('id')->paginate(20);

        return view('revenue.index', compact('items'));
    }

    public function create()
    {
        // Load options for selects
        $services    = Service::orderBy('name')->pluck('name','id');
        $mnos        = MNO::orderBy('name')->pluck('name','id');
        $aggregators = Aggregator::orderBy('name')->pluck('name','id');
        $banks       = Bank::orderBy('name')->pluck('name','id');

        return view('revenue.create', compact('services','mnos','aggregators','banks'));
    }

    public function store(Request $r, RevenueCalculator $calc)
    {
        $data = $r->validate([
            'service_id'            => 'required|exists:services,id',
            'mno_id'                => 'required|exists:mnos,id',
            'aggregator_id'         => 'required|exists:aggregators,id',
            'bank_id'               => 'required|exists:banks,id',
            'payment_date'          => 'nullable|date',
            'payment_period_month'  => 'required|integer|min:1|max:12',
            'payment_period_year'   => 'required|integer|min:2000|max:'.(date('Y') + 5),
            'period_label'          => 'nullable|string|max:100',
            'gross_revenue_a'       => 'required|numeric|min:0',
            'aggregator_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data['aggregator_net_x'] = $data['gross_revenue_a'] * (1 - ($data['aggregator_percentage'] / 100));

        $paymentDate = $this->resolvePaymentDate($data);
        $data['period_label'] = ($data['period_label'] ?? null) ?: sprintf('%04d-%02d', $data['payment_period_year'], $data['payment_period_month']);
        $share = $this->resolveServiceShares($data['service_id'], $paymentDate);
        $data['dr_share_pct'] = $share['dr_share_pct'];
        $data['aj_share_pct'] = $share['aj_share_pct'];
        $data['tj_share_pct'] = $share['tj_share_pct'];

        $vr = VASRevenue::create($data);

        // compute summary
        $calc->recompute($vr->id);

        return redirect()->route('revenue.show', $vr->id)->with('ok','Revenue saved and computed.');
    }

    public function show(int $id)
    {
        $vr = VASRevenue::with([
            'service',
            'mno',
            'aggregator',
            'bank',
            'mandatoryExpenses.type',
            'operationalExpenses.operationalCategory',
            'operationalExpenses.expenseRecipient',
            'partnerShareSummary'
        ])->findOrFail($id);

        $mandatoryTypes = MandatoryExpenseType::orderBy('name')->get();
        $operationalCategories = OperationalCategory::orderBy('name')->get();
        $expenseRecipients = ExpenseRecipient::with('operationalCategory')->orderBy('name')->get();

        return view('revenue.show', compact(
            'vr',
            'mandatoryTypes',
            'operationalCategories',
            'expenseRecipients'
        ));
    }

    public function update(Request $r, int $id, RevenueCalculator $calc)
    {
        $vr = VASRevenue::findOrFail($id);

        $data = $r->validate([
            'service_id'            => 'required|exists:services,id',
            'mno_id'                => 'required|exists:mnos,id',
            'aggregator_id'         => 'required|exists:aggregators,id',
            'bank_id'               => 'required|exists:banks,id',
            'payment_date'          => 'nullable|date',
            'payment_period_month'  => 'required|integer|min:1|max:12',
            'payment_period_year'   => 'required|integer|min:2000|max:'.(date('Y') + 5),
            'period_label'          => 'nullable|string|max:100',
            'gross_revenue_a'       => 'required|numeric|min:0',
            'aggregator_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data['aggregator_net_x'] = $data['gross_revenue_a'] * (1 - ($data['aggregator_percentage'] / 100));

        $paymentDate = $this->resolvePaymentDate($data);
        if ($vr->service_id !== (int) $data['service_id'] || $vr->payment_date?->toDateString() !== $paymentDate->toDateString()) {
            $share = $this->resolveServiceShares($data['service_id'], $paymentDate);
            $data['dr_share_pct'] = $share['dr_share_pct'];
            $data['aj_share_pct'] = $share['aj_share_pct'];
            $data['tj_share_pct'] = $share['tj_share_pct'];
        }

        $data['period_label'] = ($data['period_label'] ?? null) ?: sprintf('%04d-%02d', $data['payment_period_year'], $data['payment_period_month']);
        $vr->update($data);
        $calc->recompute($vr->id);

        return back()->with('ok','Updated & recomputed.');
    }

    public function recompute(int $id, RevenueCalculator $calc)
    {
        $calc->recompute($id);
        return redirect()->route('revenue.show', $id)->with('ok','Recomputed.');
    }

    private function resolveServiceShares(int $serviceId, Carbon $effectiveDate): array
    {
        $share = ServicePartnerShare::where('service_id', $serviceId)
            ->whereDate('effective_from', '<=', $effectiveDate->toDateString())
            ->orderByDesc('effective_from')
            ->first();

        $dr = (float) ($share->dr_share ?? env('RS_SPLIT_DR', 50));
        $aj = (float) ($share->aj_share ?? env('RS_SPLIT_AJ', 30));
        $tj = (float) ($share->tj_share ?? env('RS_SPLIT_TJ', 20));

        $total = $dr + $aj + $tj;
        if ($total == 0) {
            $dr = 50; $aj = 30; $tj = 20;
        }

        return [
            'dr_share_pct' => $dr,
            'aj_share_pct' => $aj,
            'tj_share_pct' => $tj,
        ];
    }

    private function resolvePaymentDate(array $data): Carbon
    {
        if (!empty($data['payment_date'])) {
            return Carbon::parse($data['payment_date']);
        }

        if (!empty($data['payment_period_year']) && !empty($data['payment_period_month'])) {
            return Carbon::createFromDate(
                (int) $data['payment_period_year'],
                (int) $data['payment_period_month'],
                1
            );
        }

        return now();
    }
}
