<?php

namespace App\Services;

use App\Models\PartnerShareSummary;
use App\Models\VasRevenue;

class RevenueCalculator
{
    /**
     * Recompute partner share summary for a VAS revenue record.
     *
     * @param int $vrId The VAS revenue ID
     * @return PartnerShareSummary
     */
    public function recompute(int $vrId): PartnerShareSummary
    {
        // Load VASRevenue with mandatoryExpenses.type and operationalExpenses
        $vasRevenue = VasRevenue::with([
            'mandatoryExpenses.type',
            'operationalExpenses',
        ])->findOrFail($vrId);

        // A = gross_revenue_a
        $A = $vasRevenue->gross_revenue_a;

        // pA = aggregator_percentage
        $pA = $vasRevenue->aggregator_percentage;

        // X = aggregator_net_x (manual for now)
        $X = $vasRevenue->aggregator_net_x;

        // ME = sum of mandatory_expenses
        $ME = 0;
        foreach ($vasRevenue->mandatoryExpenses as $mandatoryExpense) {
            $amount = 0;

            // if fixed_amount present, use it
            if ($mandatoryExpense->fixed_amount !== null) {
                $amount = $mandatoryExpense->fixed_amount;
            }
            // else if type.rule_type == 'Percent_of_X', base = X, amount = base * (percentage/100)
            elseif ($mandatoryExpense->type && $mandatoryExpense->type->rule_type === 'Percent_of_X' && $mandatoryExpense->percentage !== null) {
                $base = $X;
                $amount = $base * ($mandatoryExpense->percentage / 100);
            }

            $ME += $amount;
        }

        // RA = X - ME
        $RA = $X - $ME;

        // OE = sum of operational_expenses
        $OE = 0;
        foreach ($vasRevenue->operationalExpenses as $operationalExpense) {
            $amount = 0;

            // if fixed_amount present, use it
            if ($operationalExpense->fixed_amount !== null) {
                $amount = $operationalExpense->fixed_amount;
            }
            // else base = RA, amount = base * (percentage/100)
            elseif ($operationalExpense->percentage !== null) {
                $base = $RA;
                $amount = $base * ($operationalExpense->percentage / 100);
            }

            $OE += $amount;
        }

        // RS = RA - OE
        $RS = $RA - $OE;

        // Split RS by env defaults
        $splitDR = (int) env('RS_SPLIT_DR', 50);
        $splitAJ = (int) env('RS_SPLIT_AJ', 30);
        $splitTJ = (int) env('RS_SPLIT_TJ', 20);

        $drShare = $RS * ($splitDR / 100);
        $ajShare = $RS * ($splitAJ / 100);
        $tjShare = $RS * ($splitTJ / 100);

        // Upsert PartnerShareSummary
        $partnerShareSummary = PartnerShareSummary::updateOrCreate(
            ['vas_revenue_id' => $vrId],
            [
                'mandatory_total_me' => $ME,
                'ra_after_mandatory' => $RA,
                'operational_total_oe' => $OE,
                'rs_share_pool' => $RS,
                'dr_share_50' => $drShare,
                'aj_share_30' => $ajShare,
                'tj_share_20' => $tjShare,
                'computed_on' => now(),
            ]
        );

        return $partnerShareSummary;
    }
}

