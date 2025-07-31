<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',  // income/expense
        'icon',
        'color'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scope to filter income categories
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    // Scope to filter expense categories
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }
}
