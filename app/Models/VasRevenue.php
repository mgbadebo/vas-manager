<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class VasRevenue extends Model
{
    use HasFactory;

    protected $table = 'vas_revenues';

    protected $fillable = [
        'service_id',
        'mno_id',
        'aggregator_id',
        'payment_date',
        'period_label',
        'gross_revenue_a',
        'aggregator_percentage',
        'aggregator_net_x',
    ];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'gross_revenue_a' => 'decimal:2',
            'aggregator_percentage' => 'decimal:4',
            'aggregator_net_x' => 'decimal:2',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function mno(): BelongsTo
    {
        return $this->belongsTo(Mno::class);
    }

    public function aggregator(): BelongsTo
    {
        return $this->belongsTo(Aggregator::class);
    }

    public function mandatoryExpenses(): HasMany
    {
        return $this->hasMany(MandatoryExpense::class, 'vas_revenue_id');
    }

    public function operationalExpenses(): HasMany
    {
        return $this->hasMany(OperationalExpense::class, 'vas_revenue_id');
    }

    public function partnerShareSummary(): HasOne
    {
        return $this->hasOne(PartnerShareSummary::class, 'vas_revenue_id');
    }
}

