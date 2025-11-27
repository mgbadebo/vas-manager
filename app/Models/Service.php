<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'service_type_id'];

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function partnerShares(): HasMany
    {
        return $this->hasMany(ServicePartnerShare::class);
    }

    public function latestPartnerShare(): HasOne
    {
        return $this->hasOne(ServicePartnerShare::class)->latestOfMany('effective_from');
    }
}

