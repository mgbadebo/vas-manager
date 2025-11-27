<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentItem extends Model
{
    protected $fillable = [
        'vas_revenue_id',
        'payment_type',
        'recipient_name',
        'amount',
        'status',
        'comment',
        'operational_expense_id',
        'mandatory_expense_id',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function vasRevenue(): BelongsTo
    {
        return $this->belongsTo(VasRevenue::class);
    }

    public function operationalExpense(): BelongsTo
    {
        return $this->belongsTo(OperationalExpense::class);
    }

    public function mandatoryExpense(): BelongsTo
    {
        return $this->belongsTo(MandatoryExpense::class);
    }
}
