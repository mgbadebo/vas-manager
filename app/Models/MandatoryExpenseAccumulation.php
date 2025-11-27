<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MandatoryExpenseAccumulation extends Model
{
    protected $table = 'mandatory_expense_accumulations';

    protected $fillable = [
        'service_id',
        'mandatory_expense_type_id',
        'year',
        'total_amount',
        'bank_id',
        'moved_to_bank_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'moved_to_bank_date' => 'date',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function mandatoryExpenseType(): BelongsTo
    {
        return $this->belongsTo(MandatoryExpenseType::class);
    }

    public function bank(): BelongsTo
    {
        return $this->belongsTo(Bank::class);
    }
}
