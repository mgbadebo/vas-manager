<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationalCategory extends Model
{
    use HasFactory;

    protected $table = 'operational_categories';

    protected $fillable = ['name'];
}

