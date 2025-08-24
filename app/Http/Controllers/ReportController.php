<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        
        // Parse selected month
        $date = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get transactions for the selected month with categories
        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with(['balance', 'category'])
            ->orderBy('date', 'desc')
            ->get();

        // Calculate summary
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Get previous month for comparison
        $previousMonth = $date->copy()->subMonth();
        $prevStartOfMonth = $previousMonth->startOfMonth();
        $prevEndOfMonth = $previousMonth->endOfMonth();
        
        $prevTransactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$prevStartOfMonth, $prevEndOfMonth])
            ->get();
        
        $prevTotalIncome = $prevTransactions->where('type', 'income')->sum('amount');
        $prevTotalExpense = $prevTransactions->where('type', 'expense')->sum('amount');

        // Calculate percentage changes
        $incomeChange = $prevTotalIncome > 0 ? (($totalIncome - $prevTotalIncome) / $prevTotalIncome) * 100 : 0;
        $expenseChange = $prevTotalExpense > 0 ? (($totalExpense - $prevTotalExpense) / $prevTotalExpense) * 100 : 0;

        // Separate income and expense transactions
        $incomeTransactions = $transactions->where('type', 'income')->values();
        $expenseTransactions = $transactions->where('type', 'expense')->values();

        // Get available months for filter
        $availableMonths = Transaction::where('user_id', $user->id)
            ->selectRaw('DISTINCT DATE_FORMAT(date, "%Y-%m") as month')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                $date = Carbon::createFromFormat('Y-m', $item->month);
                return [
                    'value' => $item->month,
                    'label' => $date->format('F Y')
                ];
            });

        return inertia('reports/report', [
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $date->format('F Y'),
            'periodStart' => $startOfMonth->format('d F Y'),
            'periodEnd' => $endOfMonth->format('d F Y'),
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'netBalance' => $netBalance,
                'incomeChange' => round($incomeChange, 1),
                'expenseChange' => round($expenseChange, 1),
            ],
            'incomeTransactions' => $incomeTransactions,
            'expenseTransactions' => $expenseTransactions,
            'availableMonths' => $availableMonths,
            'user' => $user,
        ]);
    }

    // Route untuk menampilkan halaman PDF Vue
    public function pdfPreview(Request $request)
    {
        $user = Auth::user();
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        
        // Parse selected month
        $date = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        // Get transactions for the selected month with categories
        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with(['balance', 'category'])
            ->orderBy('date', 'desc')
            ->get();

        // Calculate summary
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Get previous month for comparison
        $previousMonth = $date->copy()->subMonth();
        $prevStartOfMonth = $previousMonth->startOfMonth();
        $prevEndOfMonth = $previousMonth->endOfMonth();
        
        $prevTransactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$prevStartOfMonth, $prevEndOfMonth])
            ->get();
        
        $prevTotalIncome = $prevTransactions->where('type', 'income')->sum('amount');
        $prevTotalExpense = $prevTransactions->where('type', 'expense')->sum('amount');

        // Calculate percentage changes
        $incomeChange = $prevTotalIncome > 0 ? (($totalIncome - $prevTotalIncome) / $prevTotalIncome) * 100 : 0;
        $expenseChange = $prevTotalExpense > 0 ? (($totalExpense - $prevTotalExpense) / $prevTotalExpense) * 100 : 0;

        // Separate income and expense transactions
        $incomeTransactions = $transactions->where('type', 'income')->values();
        $expenseTransactions = $transactions->where('type', 'expense')->values();

        return inertia('reports/pdf-preview', [
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $date->format('F Y'),
            'periodStart' => $startOfMonth->format('d F Y'),
            'periodEnd' => $endOfMonth->format('d F Y'),
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'netBalance' => $netBalance,
                'incomeChange' => round($incomeChange, 1),
                'expenseChange' => round($expenseChange, 1),
            ],
            'incomeTransactions' => $incomeTransactions,
            'expenseTransactions' => $expenseTransactions,
            'user' => $user,
            'generatedAt' => now()->format('d F Y H:i:s'),
        ]);
    }

    // Route untuk download PDF dengan data dari Vue
    public function downloadPdf(Request $request)
    {
        // Ambil data yang sama dengan pdfPreview
        $user = Auth::user();
        $selectedMonth = $request->get('month', now()->format('Y-m'));
        
        $date = Carbon::createFromFormat('Y-m', $selectedMonth);
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $transactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->with(['balance', 'category'])
            ->orderBy('date', 'desc')
            ->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $previousMonth = $date->copy()->subMonth();
        $prevStartOfMonth = $previousMonth->startOfMonth();
        $prevEndOfMonth = $previousMonth->endOfMonth();
        
        $prevTransactions = Transaction::where('user_id', $user->id)
            ->whereBetween('date', [$prevStartOfMonth, $prevEndOfMonth])
            ->get();
        
        $prevTotalIncome = $prevTransactions->where('type', 'income')->sum('amount');
        $prevTotalExpense = $prevTransactions->where('type', 'expense')->sum('amount');

        $incomeChange = $prevTotalIncome > 0 ? (($totalIncome - $prevTotalIncome) / $prevTotalIncome) * 100 : 0;
        $expenseChange = $prevTotalExpense > 0 ? (($totalExpense - $prevTotalExpense) / $prevTotalExpense) * 100 : 0;

        $incomeTransactions = $transactions->where('type', 'income')->values();
        $expenseTransactions = $transactions->where('type', 'expense')->values();

        $data = [
            'selectedMonth' => $selectedMonth,
            'monthLabel' => $date->format('F Y'),
            'periodStart' => $startOfMonth->format('d F Y'),
            'periodEnd' => $endOfMonth->format('d F Y'),
            'summary' => [
                'totalIncome' => $totalIncome,
                'totalExpense' => $totalExpense,
                'netBalance' => $netBalance,
                'incomeChange' => round($incomeChange, 1),
                'expenseChange' => round($expenseChange, 1),
            ],
            'incomeTransactions' => $incomeTransactions,
            'expenseTransactions' => $expenseTransactions,
            'user' => $user,
            'generatedAt' => now()->format('d F Y H:i:s'),
        ];

        // Generate PDF menggunakan template blade sederhana
        $pdf = Pdf::loadView('reports.pdf-simple', $data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'dpi' => 150,
            'defaultFont' => 'sans-serif'
        ]);

        $filename = 'laporan-keuangan-' . $selectedMonth . '-' . str_replace(' ', '-', $user->name) . '.pdf';
        
        return $pdf->download($filename);
    }
}
