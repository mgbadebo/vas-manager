<?php

namespace App\Services;

use App\Models\PaymentItem;
use App\Models\VasRevenue;

class PaymentItemGenerator
{
    /**
     * Generate or update payment items for a revenue entry
     */
    public function generateForRevenue(VasRevenue $vasRevenue): void
    {
        $summary = $vasRevenue->partnerShareSummary;
        if (!$summary) {
            return;
        }

        // Load relationships
        $vasRevenue->load(['operationalExpenses.expenseRecipient']);

        // Delete existing payment items for this revenue
        PaymentItem::where('vas_revenue_id', $vasRevenue->id)->delete();

        // Create partner share payments
        if ($summary->dr_share_50 > 0) {
            PaymentItem::create([
                'vas_revenue_id' => $vasRevenue->id,
                'payment_type' => 'partner_share_dr',
                'recipient_name' => 'DR',
                'amount' => $summary->dr_share_50,
                'status' => 'not_paid',
            ]);
        }

        if ($summary->aj_share_30 > 0) {
            PaymentItem::create([
                'vas_revenue_id' => $vasRevenue->id,
                'payment_type' => 'partner_share_aj',
                'recipient_name' => 'AJ',
                'amount' => $summary->aj_share_30,
                'status' => 'not_paid',
            ]);
        }

        if ($summary->tj_share_20 > 0) {
            PaymentItem::create([
                'vas_revenue_id' => $vasRevenue->id,
                'payment_type' => 'partner_share_tj',
                'recipient_name' => 'TJ',
                'amount' => $summary->tj_share_20,
                'status' => 'not_paid',
            ]);
        }

        // Create operational expense payments
        foreach ($vasRevenue->operationalExpenses as $opExpense) {
            if ($opExpense->final_amount > 0) {
                $recipientName = $opExpense->expenseRecipient
                    ? $opExpense->expenseRecipient->name
                    : 'Misc';
                
                PaymentItem::create([
                    'vas_revenue_id' => $vasRevenue->id,
                    'payment_type' => 'operational_expense',
                    'recipient_name' => $recipientName,
                    'amount' => $opExpense->final_amount,
                    'status' => 'not_paid',
                    'operational_expense_id' => $opExpense->id,
                ]);
            }
        }

        // Note: Mandatory expenses are handled separately (accumulated annually)
    }
}

