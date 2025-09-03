<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Balance;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class TransactionApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Transaction::where('user_id', Auth::id())
                ->with(['category', 'balance']);

            // Apply filters
            if ($request->has('date')) {
                $query->whereDate('date', $request->date);
            }

            if ($request->has('month')) {
                $monthStart = Carbon::createFromFormat('Y-m', $request->month)->startOfMonth();
                $monthEnd = Carbon::createFromFormat('Y-m', $request->month)->endOfMonth();
                $query->whereBetween('date', [$monthStart, $monthEnd]);
            }

            if ($request->has('type') && $request->type !== 'all') {
                $query->where('type', $request->type);
            }

            if ($request->has('category_id')) {
                // Validate category belongs to user
                $categoryExists = Category::where('id', $request->category_id)
                    ->where('user_id', Auth::id())
                    ->exists();
                
                if ($categoryExists) {
                    $query->where('category_id', $request->category_id);
                }
            }

            if ($request->has('balance_id')) {
                $query->where('balance_id', $request->balance_id);
            }

            $perPage = $request->get('per_page', 20);
            $transactions = $query->latest('date')
                ->latest('created_at')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'balance_id' => 'required|exists:balances,id',
                'category_id' => 'required|exists:categories,id',
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255',
                'date' => 'required|date'
            ]);

            // Check if balance belongs to current user
            $balance = Balance::where('id', $validated['balance_id'])
                ->where('user_id', Auth::id())
                ->where('is_active', true)
                ->first();

            if (!$balance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected wallet is not valid'
                ], 400);
            }

            // Check if category belongs to current user
            $category = Category::where('id', $validated['category_id'])
                ->where('user_id', Auth::id())
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected category is not valid'
                ], 400);
            }

            // Check if category type matches transaction type
            if ($category->type !== $validated['type']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category type does not match transaction type'
                ], 400);
            }

            // For expenses, check if balance has enough funds
            if ($validated['type'] === 'expense' && $balance->current_amount < $validated['amount']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance in selected wallet'
                ], 400);
            }

            // Create transaction
            $transaction = Transaction::create([
                ...$validated,
                'user_id' => Auth::id()
            ]);

            // Load relationships
            $transaction->load(['category', 'balance']);

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $transaction = Transaction::where('id', $id)
                ->where('user_id', Auth::id())
                ->with(['category', 'balance'])
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $transaction
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $transaction = Transaction::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            $validated = $request->validate([
                'balance_id' => 'required|exists:balances,id',
                'category_id' => 'required|exists:categories,id',
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string|max:255',
                'date' => 'required|date'
            ]);

            // Check if balance belongs to current user
            $newBalance = Balance::where('id', $validated['balance_id'])
                ->where('user_id', Auth::id())
                ->where('is_active', true)
                ->first();

            if (!$newBalance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected wallet is not valid'
                ], 400);
            }

            // Check if category belongs to current user
            $category = Category::where('id', $validated['category_id'])
                ->where('user_id', Auth::id())
                ->first();

            if (!$category) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected category is not valid'
                ], 400);
            }

            // Check if category type matches transaction type
            if ($category->type !== $validated['type']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Category type does not match transaction type'
                ], 400);
            }

            // Load old balance dengan fresh data
            $oldBalance = $transaction->balance()->lockForUpdate()->first();

            // Reverse original transaction effect
            if ($transaction->type === 'income') {
                $oldBalance->decrement('current_amount', $transaction->amount);
            } else {
                $oldBalance->increment('current_amount', $transaction->amount);
            }

            // For expenses with new balance, check if balance has enough funds
            if ($validated['type'] === 'expense' && $newBalance->current_amount < $validated['amount']) {
                // Restore original transaction effect before return error
                if ($transaction->type === 'income') {
                    $oldBalance->increment('current_amount', $transaction->amount);
                } else {
                    $oldBalance->decrement('current_amount', $transaction->amount);
                }
                
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient balance in selected wallet'
                ], 400);
            }

            // Apply new transaction effect
            if ($validated['type'] === 'income') {
                $newBalance->increment('current_amount', $validated['amount']);
            } else {
                $newBalance->decrement('current_amount', $validated['amount']);
            }

            // Update transaction
            $transaction->update($validated);
            $transaction->load(['category', 'balance']);

            return response()->json([
                'success' => true,
                'message' => 'Transaction updated successfully',
                'data' => $transaction
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $transaction = Transaction::where('id', $id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction not found'
                ], 404);
            }

            // Reverse transaction effect on balance
            $balance = $transaction->balance()->lockForUpdate()->first();
            
            if ($transaction->type === 'income') {
                $balance->decrement('current_amount', $transaction->amount);
            } else {
                $balance->increment('current_amount', $transaction->amount);
            }

            $transaction->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaction deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transaction',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function recent(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            
            $transactions = Transaction::where('user_id', Auth::id())
                ->with(['category', 'balance'])
                ->latest('date')
                ->latest('created_at')
                ->take($limit)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $transactions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recent transactions',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}