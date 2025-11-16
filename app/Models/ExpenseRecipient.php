<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseRecipient extends Model
{
    use HasFactory;

    protected $table = 'expense_recipients';

    protected $fillable = ['name', 'operational_category_id'];

    public function operationalCategory(): BelongsTo
    {
        return $this->belongsTo(OperationalCategory::class);
    }
}

