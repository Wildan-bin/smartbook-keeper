<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // Income Categories
        $incomeCategories = [
            ['name' => 'Penjualan Barang', 'type' => 'income', 'icon' => '🏪', 'color' => '#3F51B5'],
            ['name' => 'Penjualan jasa', 'type' => 'income', 'icon' => '☎️', 'color' => '#36ad54'],
            ['name' => 'Sewa Barang', 'type' => 'income', 'icon' => '🪪', 'color' => '#b3303b'],
        ];

        // Expense Categories
        $expenseCategories = [
            ['name' => 'Biaya Operasional', 'type' => 'expense', 'icon' => '🛍️', 'color' => '#795548'],
            ['name' => 'Biaya Produksi', 'type' => 'expense', 'icon' => '📄', 'color' => '#607D8B'],
            ['name' => 'Biaya Pemasaran', 'type' => 'expense', 'icon' => '📸', 'color' => '#E91E63'],
            ['name' => 'Biaya Akomodasi', 'type' => 'expense', 'icon' => '🚗', 'color' => '#3F51B5'],
            ['name' => 'Biaya Upah Karyawan', 'type' => 'expense', 'icon' => '💸', 'color' => '#b3303b'],
        ];

        foreach (array_merge($incomeCategories, $expenseCategories) as $category) {
            Category::create(array_merge($category, [
                'user_id' => 1,
            ]));
        }
    }
}