<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'balance_id',
        'category_id',
        'type',
        'amount',
        'description',
        'date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // 'deleted_at' => 'datetime',
    ];

    // Relationships
    public function Balance(): BelongsTo
    {
        return $this->belongsTo(Balance::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Scope to filter income transactions
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    // Scope to filter expense transactions
    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    // Scope to filter by date range
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    // Hook to update balance after transaction
    protected static function booted()
    {
        static::created(function ($transaction) {
            $balance = $transaction->balance;
            $amount = $transaction->type === 'income' ? $transaction->amount : -$transaction->amount;
            $balance->current_amount += $amount;
            $balance->save();
        });

        static::deleted(function ($transaction) {
            if (!$transaction->isForceDeleting()) {
                $balance = $transaction->balance;
                $amount = $transaction->type === 'income' ? -$transaction->amount : $transaction->amount;
                $balance->current_amount += $amount;
                $balance->save();
            }
        });
    }
}
