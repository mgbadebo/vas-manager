<?php

namespace App\Http\Controllers\Revenue;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{VASRevenue, Service, MNO, Aggregator};
use App\Services\RevenueCalculator;

class VASRevenueController extends Controller
{
    public function index()
    {
        $items = VASRevenue::with(['service','mno','aggregator','partnerShareSummary'])
            ->latest('payment_date')->latest('id')->paginate(20);

        return view('revenue.index', compact('items'));
    }

    public function create()
    {
        // Load options for selects
        $services    = Service::orderBy('name')->pluck('name','id');
        $mnos        = MNO::orderBy('name')->pluck('name','id');
        $aggregators = Aggregator::orderBy('name')->pluck('name','id');

        return view('revenue.create', compact('services','mnos','aggregators'));
    }

    public function store(Request $r, RevenueCalculator $calc)
    {
        $data = $r->validate([
            'service_id'            => 'required|exists:services,id',
            'mno_id'                => 'required|exists:mnos,id',
            'aggregator_id'         => 'required|exists:aggregators,id',
            'payment_date'          => 'nullable|date',
            'period_label'          => 'nullable|string|max:100',
            'gross_revenue_a'       => 'required|numeric|min:0',
            'aggregator_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data['aggregator_net_x'] = $data['gross_revenue_a'] * (1 - ($data['aggregator_percentage'] / 100));

        $vr = VASRevenue::create($data);

        // compute summary
        $calc->recompute($vr->id);

        return redirect()->route('revenue.show', $vr->id)->with('ok','Revenue saved and computed.');
    }

    public function show(int $id)
    {
        $vr = VASRevenue::with([
            'service','mno','aggregator',
            'mandatoryExpenses.type',
            'operationalExpenses',
            'partnerShareSummary'
        ])->findOrFail($id);

        return view('revenue.show', compact('vr'));
    }

    public function update(Request $r, int $id, RevenueCalculator $calc)
    {
        $vr = VASRevenue::findOrFail($id);

        $data = $r->validate([
            'service_id'            => 'required|exists:services,id',
            'mno_id'                => 'required|exists:mnos,id',
            'aggregator_id'         => 'required|exists:aggregators,id',
            'payment_date'          => 'nullable|date',
            'period_label'          => 'nullable|string|max:100',
            'gross_revenue_a'       => 'required|numeric|min:0',
            'aggregator_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data['aggregator_net_x'] = $data['gross_revenue_a'] * (1 - ($data['aggregator_percentage'] / 100));

        $vr->update($data);
        $calc->recompute($vr->id);

        return back()->with('ok','Updated & recomputed.');
    }

    public function recompute(int $id, RevenueCalculator $calc)
    {
        $calc->recompute($id);
        return redirect()->route('revenue.show', $id)->with('ok','Recomputed.');
    }
}
