<?php

namespace App\Http\Controllers;

use App\Models\PaymentItem;
use App\Models\VasRevenue;
use App\Models\MandatoryExpenseAccumulation;
use App\Services\PaymentItemGenerator;
use App\Services\MandatoryExpenseAccumulator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentsController extends Controller
{
    public function index(PaymentItemGenerator $generator, MandatoryExpenseAccumulator $accumulator)
    {
        // Recalculate mandatory expense accumulations
        $accumulator->recalculate();

        // Generate payment items for all revenues that have been calculated
        $revenues = VasRevenue::with(['partnerShareSummary', 'service', 'mno', 'aggregator', 'bank'])
            ->whereHas('partnerShareSummary')
            ->orderByDesc('payment_date')
            ->orderByDesc('id')
            ->get();

        // Ensure payment items exist for all revenues
        foreach ($revenues as $revenue) {
            $hasItems = PaymentItem::where('vas_revenue_id', $revenue->id)->exists();
            if (!$hasItems) {
                $generator->generateForRevenue($revenue);
            }
        }

        // Get revenue entries with payment summaries
        $revenuePayments = VasRevenue::with([
            'service',
            'mno',
            'aggregator',
            'bank',
            'partnerShareSummary',
            'paymentItems' => function($query) {
                $query->where('payment_type', '!=', 'mandatory_expense');
            }
        ])
        ->whereHas('partnerShareSummary')
        ->get()
        ->map(function($revenue) {
            $totalExpenses = $revenue->paymentItems->sum('amount');
            $balance = ($revenue->gross_revenue_a ?? 0) - $totalExpenses;
            
            return [
                'id' => $revenue->id,
                'payment_period' => $revenue->payment_period_month && $revenue->payment_period_year
                    ? \Carbon\Carbon::create($revenue->payment_period_year, $revenue->payment_period_month)->format('F Y')
                    : ($revenue->period_label ?? 'N/A'),
                'aggregator_name' => $revenue->aggregator->name ?? 'N/A',
                'amount_received' => $revenue->gross_revenue_a ?? 0,
                'mno' => $revenue->mno->name ?? 'N/A',
                'service' => $revenue->service->name ?? 'N/A',
                'payment_date' => $revenue->payment_date?->format('Y-m-d') ?? 'N/A',
                'bank' => $revenue->bank->name ?? 'N/A',
                'total_expenses' => $totalExpenses,
                'balance' => $balance,
            ];
        });

        // Get mandatory expense accumulations by year
        $mandatoryExpenses = MandatoryExpenseAccumulation::with(['service', 'mandatoryExpenseType', 'bank'])
            ->orderBy('year')
            ->orderBy('service_id')
            ->get()
            ->groupBy('year');

        $banks = \App\Models\Bank::orderBy('name')->get();

        return view('payments.index', compact('revenuePayments', 'mandatoryExpenses', 'banks'));
    }

    public function show(VasRevenue $vasRevenue, PaymentItemGenerator $generator)
    {
        // Ensure payment items exist
        $hasItems = PaymentItem::where('vas_revenue_id', $vasRevenue->id)->exists();
        if (!$hasItems) {
            $generator->generateForRevenue($vasRevenue);
        }

        $vasRevenue->load([
            'service',
            'mno',
            'aggregator',
            'bank',
            'partnerShareSummary',
            'paymentItems' => function($query) {
                $query->where('payment_type', '!=', 'mandatory_expense')
                    ->orderBy('payment_type')
                    ->orderBy('recipient_name');
            }
        ]);

        return view('payments.show', compact('vasRevenue'));
    }

    public function update(Request $request, PaymentItem $paymentItem)
    {
        $data = $request->validate([
            'status' => 'required|in:paid,not_paid',
            'comment' => 'nullable|string|max:1000',
        ]);

        $paymentItem->update($data);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Payment item updated.']);
        }

        return back()->with('ok', 'Payment item updated.');
    }

    public function mandatory()
    {
        $accumulations = MandatoryExpenseAccumulation::with(['service', 'mandatoryExpenseType', 'bank'])
            ->orderBy('year')
            ->orderBy('service_id')
            ->get()
            ->groupBy('year');

        return view('payments.mandatory', compact('accumulations'));
    }

    public function updateMandatoryAccumulation(Request $request, MandatoryExpenseAccumulation $accumulation)
    {
        $data = $request->validate([
            'bank_id' => 'nullable|exists:banks,id',
            'moved_to_bank_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $accumulation->update($data);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Mandatory expense accumulation updated.']);
        }

        return back()->with('ok', 'Mandatory expense accumulation updated.');
    }
}
