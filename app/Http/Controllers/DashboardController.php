<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get authenticated user and request parameters
        $user = Auth::user();
        $selectedMonth = $request->get('month', 'all');
        $balanceView = $request->get('balanceView', 'daily');
        $turnoverView = $request->get('turnoverView', 'daily');

        // Initialize date variables for filtering
        $startOfMonth = null;
        $endOfMonth = null;

        // Calculate current total balance from active balances
        $totalBalance = Balance::where('user_id', $user->id)
            ->where('is_active', true)
            ->sum('current_amount');

        // Set date range if specific month is selected
        if ($selectedMonth !== 'all') {
            $date = Carbon::createFromFormat('Y-m', $selectedMonth);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();
        }

        // Build base query for transactions with date filter
        $baseQuery = Transaction::where('user_id', $user->id)
            ->when($selectedMonth !== 'all', function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereBetween('date', [$startOfMonth, $endOfMonth]);
            });

        // Calculate total turnover (income only)
        $turnover = [
            'income' => $baseQuery->clone()->where('type', 'income')->sum('amount'),
            'expense' => $baseQuery->clone()->where('type', 'expense')->sum('amount')
        ];

        // Get latest income transaction for indicator
        $latestIncome = $baseQuery->clone()
            ->where('type', 'income')
            ->latest('date')
            ->first();

        // Calculate percentage contribution of latest income to total turnover
        $percentageChange = 0;
        if ($latestIncome && $turnover['income'] > 0) {
            $totalWithoutLatest = $turnover['income'] - $latestIncome->amount;
            $percentageChange = ($latestIncome->amount / ($totalWithoutLatest ?: 1)) * 100;
        }

        // Get all transactions for charts
        $transactions = $baseQuery->clone()
            ->orderBy('date')
            ->get();

        // Calculate running balance for each transaction
        $runningBalance = 0;
        $transactions = $transactions->map(function ($transaction) use (&$runningBalance) {
            $runningBalance += $transaction->type === 'income' ? $transaction->amount : -$transaction->amount;
            return [
                'id' => $transaction->id,
                'date' => $transaction->date,
                'amount' => $transaction->amount,
                'type' => $transaction->type,
                'running_balance' => $runningBalance
            ];
        });

        // Prepare balance data based on view type
        $balanceData = [];
        if ($balanceView === 'daily') {
            $balanceData = $transactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction['date'])->format('Y-m-d');
            })->map(function ($dayTransactions) {
                return $dayTransactions->last()['running_balance'];
            })->sortKeys();
        } elseif ($balanceView === 'weekly') {
            $balanceData = $transactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction['date'])->endOfWeek()->format('Y-m-d');
            })->map(function ($weekTransactions) {
                return $weekTransactions->last()['running_balance'];
            })->sortKeys();
        } else {
            $balanceData = $transactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction['date'])->format('Y-m');
            })->map(function ($monthTransactions) {
                return $monthTransactions->last()['running_balance'];
            })->sortKeys();
        }

        // Prepare turnover data based on view type
        $turnoverData = [];
        if ($turnoverView === 'daily') {
            $turnoverData = $transactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction['date'])->format('Y-m-d');
            })->map(function ($dayTransactions) {
                return $dayTransactions->sum(function ($t) {
                    return $t['type'] === 'income' ? $t['amount'] : 0;
                });
            })->sortKeys();
        } elseif ($turnoverView === 'weekly') {
            $turnoverData = $transactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction['date'])->endOfWeek()->format('Y-m-d');
            })->map(function ($weekTransactions) {
                return $weekTransactions->sum(function ($t) {
                    return $t['type'] === 'income' ? $t['amount'] : 0;
                });
            })->sortKeys();
        } else {
            $turnoverData = $transactions->groupBy(function ($transaction) {
                return Carbon::parse($transaction['date'])->format('Y-m');
            })->map(function ($monthTransactions) {
                return $monthTransactions->sum(function ($t) {
                    return $t['type'] === 'income' ? $t['amount'] : 0;
                });
            })->sortKeys();
        }

        // Helper function for currency formatting
        $formatCurrency = function ($value) {
            if ($value >= 1000000000) {
                return number_format($value / 1000000000, 1) . 'M';
            } elseif ($value >= 1000000) {
                return number_format($value / 1000000, 1) . 'Jt';
            } elseif ($value >= 1000) {
                return number_format($value / 1000, 1) . 'Rb';
            }
            return number_format($value, 0);
        };

        // Helper function for date formatting
        $formatDate = function ($date, $view) {
            $date = Carbon::parse($date);
            switch ($view) {
                case 'daily':
                    return $date->format('d/m');
                case 'weekly':
                    return 'W' . $date->week;
                case 'monthly':
                    return $date->format('M');
                default:
                    return $date->format('d/m');
            }
        };

        // Helper for formatting the display values in tooltips
        $formatTooltip = function ($value) {
            if ($value >= 1000000000) {
                return number_format($value / 1000000000, 1) . 'M';
            } elseif ($value >= 1000000) {
                return number_format($value / 1000000, 1) . 'Jt';
            } elseif ($value >= 1000) {
                return number_format($value / 1000, 1) . 'Rb';
            }
            return number_format($value, 0);
        };

        // Format balance data for chart
        $formattedBalanceData = [
            'labels' => collect($balanceData->keys())->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            })->toArray(),
            'values' => $balanceData->values()->map(function ($amount) {
                return (float) $amount;
            })->toArray(),
            'tooltips' => $balanceData->values()->map(function ($amount) use ($formatTooltip) {
                return $formatTooltip($amount);
            })->toArray()
        ];

        // Format turnover data for chart
        $formattedTurnoverData = [
            'labels' => collect($turnoverData->keys())->map(function ($date) {
                return Carbon::parse($date)->format('d/m');
            })->toArray(),
            'values' => $turnoverData->values()->map(function ($amount) {
                return (float) $amount;
            })->toArray(),
            'tooltips' => $turnoverData->values()->map(function ($amount) use ($formatTooltip) {
                return $formatTooltip($amount);
            })->toArray()
        ];

        // Get available months for filter
        $availableMonths = Transaction::where('user_id', $user->id)
            ->selectRaw('DISTINCT DATE_FORMAT(date, "%Y-%m") as month')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month);
                return [
                    'value' => $item->month,
                    'label' => $date->isoFormat('MMMM Y')
                ];
            });

        $availableMonths->prepend([
            'value' => 'all',
            'label' => 'Semua'
        ]);

        // Get 5 latest transactions
        $recentTransactions = Transaction::with(['category', 'balance'])
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        return Inertia::render('Dashboard', [
            'totalBalance' => (float) $totalBalance,
            'turnover' => [
                'income' => $turnover['income'],
                'expense' => $turnover['expense'],
                'total' => $turnover['income'] - $turnover['expense'],
                'percentageChange' => round($percentageChange, 1) // Round to 1 decimal place
            ],
            'balanceData' => $formattedBalanceData,
            'turnoverData' => $formattedTurnoverData,
            'selectedMonth' => $selectedMonth,
            'availableMonths' => $availableMonths,
            'balanceView' => $balanceView,
            'turnoverView' => $turnoverView,
            'recentTransactions' => $recentTransactions
        ]);
    }
}
