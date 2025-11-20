<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMandatoryExpenseRequest;
use App\Models\MandatoryExpense;
use App\Models\VASRevenue;
use App\Services\RevenueCalculator;

class MandatoryExpenseController extends Controller
{
    public function store(StoreMandatoryExpenseRequest $request, VASRevenue $vasRevenue, RevenueCalculator $calculator)
    {
        $data = $request->validated();
        $data['final_amount'] = $data['fixed_amount'] ?? 0;

        $vasRevenue->mandatoryExpenses()->create($data);

        $calculator->recompute($vasRevenue->id);

        return redirect()
            ->route('revenue.show', $vasRevenue->id)
            ->with('ok', 'Mandatory expense added.');
    }

    public function destroy(VASRevenue $vasRevenue, MandatoryExpense $mandatoryExpense, RevenueCalculator $calculator)
    {
        abort_if($mandatoryExpense->vas_revenue_id !== $vasRevenue->id, 404);

        $mandatoryExpense->delete();
        $calculator->recompute($vasRevenue->id);

        return redirect()
            ->route('revenue.show', $vasRevenue->id)
            ->with('ok', 'Mandatory expense removed.');
    }
}


