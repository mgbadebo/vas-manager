<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mno extends Model
{
    use HasFactory;

    protected $table = 'mnos';

    protected $fillable = ['name'];
}

