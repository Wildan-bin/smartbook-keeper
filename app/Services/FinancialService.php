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
}