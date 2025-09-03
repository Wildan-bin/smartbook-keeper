<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Balance;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get current month data
            $currentMonth = now()->startOfMonth();
            $nextMonth = now()->addMonth()->startOfMonth();
            
            // Get transactions for current month
            $currentMonthTransactions = Transaction::where('user_id', $user->id)
                ->whereBetween('date', [$currentMonth, $nextMonth])
                ->with(['category', 'balance'])
                ->get();

            // Calculate monthly summary
            $monthlyIncome = $currentMonthTransactions->where('type', 'income')->sum('amount');
            $monthlyExpense = $currentMonthTransactions->where('type', 'expense')->sum('amount');
            $netIncome = $monthlyIncome - $monthlyExpense;

            // Get total balance
            $totalBalance = Balance::where('user_id', $user->id)
                ->where('is_active', true)
                ->sum('current_amount');

            // Get expense by category for current month
            $expensesByCategory = Transaction::where('user_id', $user->id)
                ->where('type', 'expense')
                ->whereBetween('date', [$currentMonth, $nextMonth])
                ->with('category')
                ->get()
                ->groupBy('category_id')
                ->map(function ($transactions) {
                    return [
                        'category' => $transactions->first()->category,
                        'total' => $transactions->sum('amount'),
                        'count' => $transactions->count()
                    ];
                })
                ->sortByDesc('total')
                ->take(5)
                ->values();

            // Get income by category for current month
            $incomesByCategory = Transaction::where('user_id', $user->id)
                ->where('type', 'income')
                ->whereBetween('date', [$currentMonth, $nextMonth])
                ->with('category')
                ->get()
                ->groupBy('category_id')
                ->map(function ($transactions) {
                    return [
                        'category' => $transactions->first()->category,
                        'total' => $transactions->sum('amount'),
                        'count' => $transactions->count()
                    ];
                })
                ->sortByDesc('total')
                ->take(5)
                ->values();

            // Get recent transactions
            $recentTransactions = $currentMonthTransactions
                ->sortByDesc('date')
                ->take(10)
                ->values();

            // Get all balances for user
            $balances = Balance::where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('current_amount', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'summary' => [
                        'total_balance' => $totalBalance,
                        'monthly_income' => $monthlyIncome,
                        'monthly_expense' => $monthlyExpense,
                        'net_income' => $netIncome,
                    ],
                    'recent_transactions' => $recentTransactions,
                    'expenses_by_category' => $expensesByCategory,
                    'incomes_by_category' => $incomesByCategory,
                    'balances' => $balances,
                    'user' => $user,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dashboard data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function summary(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get current month data
            $currentMonth = now()->startOfMonth();
            $nextMonth = now()->addMonth()->startOfMonth();
            
            // Get transactions for current month
            $currentMonthTransactions = Transaction::where('user_id', $user->id)
                ->whereBetween('date', [$currentMonth, $nextMonth])
                ->get();

            // Calculate monthly summary
            $monthlyIncome = $currentMonthTransactions->where('type', 'income')->sum('amount');
            $monthlyExpense = $currentMonthTransactions->where('type', 'expense')->sum('amount');
            $netIncome = $monthlyIncome - $monthlyExpense;

            // Get total balance
            $totalBalance = Balance::where('user_id', $user->id)
                ->where('is_active', true)
                ->sum('current_amount');

            return response()->json([
                'success' => true,
                'data' => [
                    'total_balance' => $totalBalance,
                    'monthly_income' => $monthlyIncome,
                    'monthly_expense' => $monthlyExpense,
                    'net_income' => $netIncome,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function charts(Request $request)
    {
        try {
            $user = Auth::user();
            
            // Get weekly data for chart (last 4 weeks)
            $weeklyData = [];
            for ($i = 3; $i >= 0; $i--) {
                $weekStart = now()->subWeeks($i)->startOfWeek();
                $weekEnd = now()->subWeeks($i)->endOfWeek();
                
                $weekTransactions = Transaction::where('user_id', $user->id)
                    ->whereBetween('date', [$weekStart, $weekEnd])
                    ->get();
                
                $weeklyData[] = [
                    'week' => $weekStart->format('M d') . ' - ' . $weekEnd->format('M d'),
                    'income' => $weekTransactions->where('type', 'income')->sum('amount'),
                    'expense' => $weekTransactions->where('type', 'expense')->sum('amount'),
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'weekly_data' => $weeklyData
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get chart data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}