<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationalExpense extends Model
{
    use HasFactory;

    protected $table = 'operational_expenses';

    protected $fillable = [
        'vas_revenue_id',
        'operational_category_id',
        'expense_recipient_id',
        'percentage',
        'fixed_amount',
        'final_amount',
    ];

    protected function casts(): array
    {
        return [
            'percentage' => 'decimal:4',
            'fixed_amount' => 'decimal:2',
            'final_amount' => 'decimal:2',
        ];
    }

    public function vasRevenue(): BelongsTo
    {
        return $this->belongsTo(VasRevenue::class, 'vas_revenue_id');
    }

    public function operationalCategory(): BelongsTo
    {
        return $this->belongsTo(OperationalCategory::class, 'operational_category_id');
    }

    public function expenseRecipient(): BelongsTo
    {
        return $this->belongsTo(ExpenseRecipient::class, 'expense_recipient_id');
    }
}

