<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeyStakeholder extends Model
{
    use HasFactory;

    protected $table = 'key_stakeholders';

    protected $fillable = ['name', 'service_id', 'share_percentage'];

    protected function casts(): array
    {
        return [
            'share_percentage' => 'decimal:4',
        ];
    }
}

