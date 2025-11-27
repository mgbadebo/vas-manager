<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'account_number',
        'currency',
    ];

    protected function casts(): array
    {
        return [
            'name' => 'string',
            'account_number' => 'string',
            'currency' => 'string',
        ];
    }

    public function vasRevenues(): HasMany
    {
        return $this->hasMany(VasRevenue::class);
    }
}
