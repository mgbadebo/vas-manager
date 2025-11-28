<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOperationalExpenseRequest;
use App\Models\OperationalExpense;
use App\Models\VasRevenue;
use App\Services\RevenueCalculator;

class OperationalExpenseController extends Controller
{
    public function store(StoreOperationalExpenseRequest $request, VasRevenue $vasRevenue, RevenueCalculator $calculator)
    {
        $data = $request->validated();
        $data['final_amount'] = $data['fixed_amount'] ?? 0;

        $vasRevenue->operationalExpenses()->create($data);

        $calculator->recompute($vasRevenue->id);

        return redirect()
            ->route('revenue.show', $vasRevenue->id)
            ->with('ok', 'Operational expense added.');
    }

    public function destroy(VasRevenue $vasRevenue, OperationalExpense $operationalExpense, RevenueCalculator $calculator)
    {
        abort_if($operationalExpense->vas_revenue_id !== $vasRevenue->id, 404);

        $operationalExpense->delete();
        $calculator->recompute($vasRevenue->id);

        return redirect()
            ->route('revenue.show', $vasRevenue->id)
            ->with('ok', 'Operational expense removed.');
    }
}


