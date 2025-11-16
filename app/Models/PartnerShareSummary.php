<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartnerShareSummary extends Model
{
    use HasFactory;

    protected $table = 'partner_share_summaries';

    protected $fillable = [
        'vas_revenue_id',
        'mandatory_total_me',
        'ra_after_mandatory',
        'operational_total_oe',
        'rs_share_pool',
        'dr_share_50',
        'aj_share_30',
        'tj_share_20',
        'computed_on',
    ];

    protected function casts(): array
    {
        return [
            'mandatory_total_me' => 'decimal:2',
            'ra_after_mandatory' => 'decimal:2',
            'operational_total_oe' => 'decimal:2',
            'rs_share_pool' => 'decimal:2',
            'dr_share_50' => 'decimal:2',
            'aj_share_30' => 'decimal:2',
            'tj_share_20' => 'decimal:2',
            'computed_on' => 'datetime',
        ];
    }

    public function vasRevenue(): BelongsTo
    {
        return $this->belongsTo(VasRevenue::class, 'vas_revenue_id');
    }
}

