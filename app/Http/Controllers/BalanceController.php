<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class BalanceController extends Controller
{
    public function index()
    {
        return Inertia::render('balances/show', [
            'balances' => Balance::where('user_id', Auth::id())->get(),
            'categories' => Category::all(),
        ]);
    }

    public function storeBalance(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'initial_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        $balance = Balance::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            // 'initial_amount' => $validated['initial_amount'] ?? 0,
            'current_amount' => $validated['initial_amount'] ?? 0,
            'currency' => $validated['currency'],
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Wallet added successfully');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:income,expense',
            'icon' => 'nullable|string|max:10',
            'color' => 'nullable|string|max:7',
        ]);

        Category::create($validated);

        return redirect()->back()->with('success', 'Category added successfully');
    }
}