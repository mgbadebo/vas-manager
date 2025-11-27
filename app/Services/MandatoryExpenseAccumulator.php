<?php

namespace App\Services;

use App\Models\MandatoryExpenseAccumulation;
use App\Models\VasRevenue;
use Illuminate\Support\Facades\DB;

class MandatoryExpenseAccumulator
{
    /**
     * Recalculate mandatory expense accumulations for all years
     */
    public function recalculate(): void
    {
        // Get all revenues with mandatory expenses that have been calculated
        $revenues = VasRevenue::with(['mandatoryExpenses.type', 'service', 'partnerShareSummary'])
            ->whereHas('mandatoryExpenses')
            ->whereHas('partnerShareSummary')
            ->get();

        // Group by service, type, and year
        $accumulations = [];

        foreach ($revenues as $revenue) {
            $year = $revenue->payment_period_year ?? $revenue->payment_date?->year ?? now()->year;
            
            foreach ($revenue->mandatoryExpenses as $mandatoryExpense) {
                if (!$mandatoryExpense->type) {
                    continue;
                }

                $key = $revenue->service_id . '_' . $mandatoryExpense->mandatory_expense_type_id . '_' . $year;

                if (!isset($accumulations[$key])) {
                    $accumulations[$key] = [
                        'service_id' => $revenue->service_id,
                        'mandatory_expense_type_id' => $mandatoryExpense->mandatory_expense_type_id,
                        'year' => $year,
                        'total_amount' => 0,
                    ];
                }

                $accumulations[$key]['total_amount'] += $mandatoryExpense->final_amount ?? 0;
            }
        }

        // Update or create accumulations
        foreach ($accumulations as $key => $data) {
            MandatoryExpenseAccumulation::updateOrCreate(
                [
                    'service_id' => $data['service_id'],
                    'mandatory_expense_type_id' => $data['mandatory_expense_type_id'],
                    'year' => $data['year'],
                ],
                [
                    'total_amount' => $data['total_amount'],
                ]
            );
        }
    }
}

