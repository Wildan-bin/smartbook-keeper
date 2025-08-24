<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\Balance;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        // Validate filter parameters
        $filters = $request->validate([
            'date' => 'nullable|date',
            'month' => 'nullable|date_format:Y-m',
            'type' => 'nullable|in:all,income,expense',
            'category' => 'nullable|exists:categories,id',
            'balance' => 'nullable|exists:balances,id',
        ]);

        // Start building query
        $query = Transaction::where('user_id', Auth::id())
            ->with(['category', 'balance']);

        // Apply filters
        if (!empty($filters['date'])) {
            $query->whereDate('date', $filters['date']);
        }

        if (!empty($filters['month'])) {
            $monthStart = Carbon::createFromFormat('Y-m', $filters['month'])->startOfMonth();
            $monthEnd = Carbon::createFromFormat('Y-m', $filters['month'])->endOfMonth();
            $query->whereBetween('date', [$monthStart, $monthEnd]);
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['balance'])) {
            $query->where('balance_id', $filters['balance']);
        }

        // Get filtered transactions
        $transactions = $query->latest('date')
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString(); // Preserve query parameters in pagination

        return Inertia::render('transactions/index', [
            'transactions' => $transactions,
            'balances' => Balance::where('user_id', Auth::id())
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'categories' => Category::orderBy('name')->get(),
            'totalBalance' => $this->calculateTotalBalance(),
            'filters' => array_filter($filters), // Only send non-empty filters
        ]);
    }

    public function balances()
    {
        return Inertia::render('balances/show', [
            'balances' => Balance::where('user_id', Auth::id())
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'categories' => Category::orderBy('name')->get(),
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

        // Check if balance name already exists for this user
        $existingBalance = Balance::where('user_id', Auth::id())
            ->where('name', $validated['name'])
            ->where('is_active', true)
            ->first();

        if ($existingBalance) {
            return redirect()->back()
                ->withErrors(['name' => 'A wallet with this name already exists.']);
        }

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

        // Check if category name already exists for this type
        $existingCategory = Category::where('name', $validated['name'])
            ->where('type', $validated['type'])
            ->first();

        if ($existingCategory) {
            return redirect()->back()
                ->withErrors(['name' => 'A category with this name already exists for this type.']);
        }

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
            'amount' => 'required|numeric|min:0.01', // Minimum amount
            'description' => 'nullable|string|max:255',
            // 'date' => 'required|date|before_or_equal:today' // Can't be future date
            'date' => 'required|date' // Can't be future date
        ]);

        // Check if balance belongs to current user
        $balance = Balance::where('id', $validated['balance_id'])
            ->where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if (!$balance) {
            return redirect()->back()
                ->withErrors(['balance_id' => 'Selected wallet is not valid.']);
        }

        // Check if category type matches transaction type
        $category = Category::find($validated['category_id']);
        if ($category->type !== $validated['type']) {
            return redirect()->back()
                ->withErrors(['category_id' => 'Category type does not match transaction type.']);
        }

        // For expenses, check if balance has enough funds
        if ($validated['type'] === 'expense' && $balance->current_amount < $validated['amount']) {
            return redirect()->back()
                ->withErrors(['amount' => 'Insufficient balance in selected wallet.']);
        }

        // Create transaction
        $transaction = Transaction::create([
            ...$validated,
            'user_id' => Auth::id()
        ]);

        // tidak digunakan karena sudah diatur di model
        // Update balance amount
        // if ($validated['type'] === 'income') {
        //     $balance->increment('current_amount', $validated['amount']);
        // } else {
        //     $balance->decrement('current_amount', $validated['amount']);
        // }

        return redirect()->back()
            ->with('success', 'Transaction recorded successfully');
    }

    public function destroy(Transaction $transaction)
    {
        // Check if transaction belongs to current user
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // PERBAIKAN: Load balance relation untuk memastikan data fresh
        // $balance = $transaction->balance()->lockForUpdate()->first();
        
        // if (!$balance) {
        //     return redirect()->back()
        //         ->withErrors(['error' => 'Balance not found for this transaction.']);
        // }

        // Reverse the balance change dengan logika yang benar
        // if ($transaction->type === 'income') {
        //     // Jika income dihapus, saldo harus berkurang
        //     $balance->decrement('current_amount', $transaction->amount);
        // } else {
        //     // Jika expense dihapus, saldo harus bertambah
        //     $balance->increment('current_amount', $transaction->amount);
        // }

        // Delete transaction setelah balance diupdate
        $transaction->delete();

        return redirect()->back()
            ->with('success', 'Transaction deleted successfully');
    }

    public function edit(Transaction $transaction)
    {
        // Check if transaction belongs to current user
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('transactions/edit', [
            'transaction' => $transaction->load(['category', 'balance']),
            'balances' => Balance::where('user_id', Auth::id())
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        // Check if transaction belongs to current user
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'balance_id' => 'required|exists:balances,id',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            // 'date' => 'required|date|before_or_equal:today'
            'date' => 'required|date'
        ]);

        // Check if balance belongs to current user
        $newBalance = Balance::where('id', $validated['balance_id'])
            ->where('user_id', Auth::id())
            ->where('is_active', true)
            ->first();

        if (!$newBalance) {
            return redirect()->back()
                ->withErrors(['balance_id' => 'Selected wallet is not valid.']);
        }

        // Check if category type matches transaction type
        $category = Category::find($validated['category_id']);
        if ($category->type !== $validated['type']) {
            return redirect()->back()
                ->withErrors(['category_id' => 'Category type does not match transaction type.']);
        }

        // PERBAIKAN: Load old balance dengan fresh data
        $oldBalance = $transaction->balance()->lockForUpdate()->first();

        // Reverse original transaction effect dengan logika yang benar
        if ($transaction->type === 'income') {
            // Batalkan income lama (kurangi saldo)
            $oldBalance->decrement('current_amount', $transaction->amount);
        } else {
            // Batalkan expense lama (tambah saldo)
            $oldBalance->increment('current_amount', $transaction->amount);
        }

        // For expenses with new balance, check if balance has enough funds
        if ($validated['type'] === 'expense' && $newBalance->current_amount < $validated['amount']) {
            // Restore original transaction effect sebelum return error
            if ($transaction->type === 'income') {
                $oldBalance->increment('current_amount', $transaction->amount);
            } else {
                $oldBalance->decrement('current_amount', $transaction->amount);
            }
            
            return redirect()->back()
                ->withErrors(['amount' => 'Insufficient balance in selected wallet.']);
        }

        // Apply new transaction effect dengan logika yang benar
        if ($validated['type'] === 'income') {
            // Terapkan income baru (tambah saldo)
            $newBalance->increment('current_amount', $validated['amount']);
        } else {
            // Terapkan expense baru (kurangi saldo)
            $newBalance->decrement('current_amount', $validated['amount']);
        }

        // Update transaction
        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaction updated successfully');
    }

    private function calculateTotalBalance(): float
    {
        return Balance::where('user_id', Auth::id())
            ->where('is_active', true)
            ->sum('current_amount');
    }

    // Additional helper method for getting transaction statistics
    public function getStats(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        $startDate = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $endDate = match($period) {
            'day' => now()->endOfDay(),
            'week' => now()->endOfWeek(),
            'month' => now()->endOfMonth(),
            'year' => now()->endOfYear(),
            default => now()->endOfMonth(),
        };

        $transactions = Transaction::where('user_id', Auth::id())
            ->whereBetween('date', [$startDate, $endDate])
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $transactionCount = $transactions->count();

        return response()->json([
            'period' => $period,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_income' => $totalIncome - $totalExpense,
            'transaction_count' => $transactionCount,
            'date_range' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
        ]);
    }
}