<?php

namespace Database\Seeders;

use App\Models\Balance;
use App\Models\User;
use Illuminate\Database\Seeder;

class BalanceSeeder extends Seeder
{
    public function run(): void
    {
        // Make sure we have at least one user
        $user = User::first();
        
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
            ]);
        }

        // Create default wallets for the user
        $wallets = [
            [
                'name' => 'Cash',
                // 'initial_amount' => 1000000,
                'current_amount' => 0,
                'currency' => 'IDR',
                'is_active' => true
            ],
            [
                'name' => 'Bank BRI',
                // 'initial_amount' => 5000000,
                'current_amount' => 0,
                'currency' => 'IDR',
                'is_active' => true
            ],
            [
                'name' => 'E-Wallet',
                // 'initial_amount' => 500000,
                'current_amount' => 0,
                'currency' => 'IDR',
                'is_active' => true
            ]
        ];

        foreach ($wallets as $wallet) {
            Balance::create(array_merge($wallet, [
                'user_id' => $user->id
            ]));
        }
    }
}