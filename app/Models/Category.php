<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // Scope to filter by user
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
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

    // Scope for user's income categories
    public function scopeUserIncome($query, $userId)
    {
        return $query->where('user_id', $userId)->where('type', 'income');
    }

    // Scope for user's expense categories
    public function scopeUserExpense($query, $userId)
    {
        return $query->where('user_id', $userId)->where('type', 'expense');
    }
}
