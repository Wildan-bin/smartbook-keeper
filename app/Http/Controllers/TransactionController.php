<?php


namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Balance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        return Inertia::render('transactions/index', [
            'transactions' => Transaction::where('user_id', Auth::id())
                ->with(['category', 'balance'])
                ->latest()
                ->paginate(10),
            'balances' => Balance::where('user_id', Auth::id())
                ->where('is_active', true)
                ->get(),
            'categories' => Category::all(),
            'totalBalance' => $this->calculateTotalBalance()
        ]);
    }

    public function balances()
    {
        return Inertia::render('balances/show', [
            'balances' => Balance::where('user_id', Auth::id())
                ->where('is_active', true)
                ->get(),
            'categories' => Category::all(),
            'totalBalance' => $this->calculateTotalBalance()
        ]);
    }

    public function storeBalance(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'initial_amount' => 'nullable|numeric|min:0',
            'currency' => 'required|string|size:3',
        ]);

        // Create balance with user_id
        Balance::create([
            ...array_filter($validated), // Remove null/empty values
            'user_id' => Auth::id(),
            'initial_amount' => $validated['initial_amount'] ?? 0,
            'current_amount' => $validated['initial_amount'] ?? 0,
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

        return redirect()->back()
            ->with('success', 'Category added successfully');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'balance_id' => 'required|exists:balances,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'date' => 'required|date'
        ]);

        Transaction::create([
            ...$validated,
            'user_id' => Auth::id()
        ]);

        return redirect()->back()
            ->with('success', 'Transaction recorded successfully');
    }

    private function calculateTotalBalance(): float
    {
        return Balance::where('user_id', Auth::id())
            ->where('is_active', true)
            ->sum('current_amount');
    }
}