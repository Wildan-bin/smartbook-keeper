<?php

namespace App\Services;

use App\Models\Balance;
use App\Models\User;
use Carbon\Carbon;

class FinancialService
{
    public function getUserBalances($userId, $filter = null)
    {
        $query = Balance::where('user_id', $userId);

        if ($filter) {
            $date = Carbon::parse($filter);
            $query->whereHas('transactions', function ($q) use ($date) {
                $q->whereMonth('date', $date->month)
                  ->whereYear('date', $date->year);
            });
        }

        return $query->with(['transactions' => function ($q) {
            $q->latest('date');
        }])->get();
    }

    public function getUserTotalBalance($userId)
    {
        $user = User::find($userId);
        $totalBalance = Balance::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('current_amount');

        return [
            'name' => $user->name,
            'balance' => (string) $totalBalance
        ];
    }

    public function createBalance(int $userId, array $data): Balance
    {
        // Check if balance name already exists for this user
        $existingBalance = Balance::where('user_id', $userId)
            ->where('name', $data['name'])
            ->where('is_active', true)
            ->first();

        if ($existingBalance) {
            throw new \Exception('A wallet with this name already exists');
        }

        return Balance::create([
            ...$data,
            'user_id' => $userId,
            // 'initial_amount' => $data['initial_amount'] ?? 0,
            'current_amount' => $data['initial_amount'] ?? 0,
            'is_active' => true,
        ]);
    }

    public function getBalance(int $userId, int $balanceId): ?Balance
    {
        return Balance::where('id', $balanceId)
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->first();
    }

    public function updateBalance(int $userId, int $balanceId, array $data): ?Balance
    {
        $balance = $this->getBalance($userId, $balanceId);
        
        if (!$balance) {
            return null;
        }

        // Check if balance name already exists for this user (excluding current)
        $existingBalance = Balance::where('user_id', $userId)
            ->where('name', $data['name'])
            ->where('is_active', true)
            ->where('id', '!=', $balance->id)
            ->first();

        if ($existingBalance) {
            throw new \Exception('A wallet with this name already exists');
        }

        $balance->update($data);
        return $balance->fresh();
    }

    public function deleteBalance(int $userId, int $balanceId): bool
    {
        $balance = $this->getBalance($userId, $balanceId);
        
        if (!$balance) {
            return false;
        }

        // Check if balance has transactions
        if ($balance->transactions()->count() > 0) {
            throw new \Exception('Cannot delete wallet that has transactions');
        }

        $balance->update(['is_active' => false]);
        return true;
    }
}