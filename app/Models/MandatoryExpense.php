<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MandatoryExpense extends Model
{
    use HasFactory;

    protected $table = 'mandatory_expenses';

    protected $fillable = [
        'vas_revenue_id',
        'mandatory_expense_type_id',
        'key_stakeholder_id',
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

    public function type(): BelongsTo
    {
        return $this->belongsTo(MandatoryExpenseType::class, 'mandatory_expense_type_id');
    }

    public function keyStakeholder(): BelongsTo
    {
        return $this->belongsTo(KeyStakeholder::class, 'key_stakeholder_id');
    }
}

