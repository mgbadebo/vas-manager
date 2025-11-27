<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePartnerShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'dr_share',
        'aj_share',
        'tj_share',
        'effective_from',
    ];

    protected function casts(): array
    {
        return [
            'dr_share' => 'decimal:2',
            'aj_share' => 'decimal:2',
            'tj_share' => 'decimal:2',
            'effective_from' => 'date',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}


