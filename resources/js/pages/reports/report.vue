<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Props dari controller
const props = defineProps({
    selectedMonth: {
        type: String,
        default: () => new Date().toISOString().slice(0, 7),
    },
    monthLabel: {
        type: String,
        default: '',
    },
    periodStart: {
        type: String,
        default: '',
    },
    periodEnd: {
        type: String,
        default: '',
    },
    summary: {
        type: Object,
        default: () => ({
            totalIncome: 0,
            totalExpense: 0,
            netBalance: 0,
            incomeChange: 0,
            expenseChange: 0,
        }),
    },
    incomeTransactions: {
        type: Array,
        default: () => [],
    },
    expenseTransactions: {
        type: Array,
        default: () => [],
    },
    availableMonths: {
        type: Array,
        default: () => [],
    },
});

// Reactive data
const selectedMonth = ref(props.selectedMonth);
const incomeSortField = ref('date');
const incomeSortDirection = ref('desc');
const expenseSortField = ref('date');
const expenseSortDirection = ref('desc');

// Get user from page props
const user = usePage().props.auth.user;

// Computed properties untuk sorting
const sortedIncomeTransactions = computed(() => {
    const sorted = [...props.incomeTransactions].sort((a, b) => {
        const aValue = a[incomeSortField.value];
        const bValue = b[incomeSortField.value];
        
        if (incomeSortDirection.value === 'asc') {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });
    return sorted;
});

const sortedExpenseTransactions = computed(() => {
    const sorted = [...props.expenseTransactions].sort((a, b) => {
        const aValue = a[expenseSortField.value];
        const bValue = b[expenseSortField.value];
        
        if (expenseSortDirection.value === 'asc') {
            return aValue > bValue ? 1 : -1;
        } else {
            return aValue < bValue ? 1 : -1;
        }
    });
    return sorted;
});

// Helper functions
const formatCurrency = (amount) => {
    if (amount === undefined || amount === null) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount);
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
};

// Functions
const filterByMonth = () => {
    router.get(route('reports.index'), {
        month: selectedMonth.value
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const sortBy = (field, type) => {
    if (type === 'income') {
        if (incomeSortField.value === field) {
            incomeSortDirection.value = incomeSortDirection.value === 'asc' ? 'desc' : 'asc';
        } else {
            incomeSortField.value = field;
            incomeSortDirection.value = 'desc';
        }
    } else {
        if (expenseSortField.value === field) {
            expenseSortDirection.value = expenseSortDirection.value === 'asc' ? 'desc' : 'asc';
        } else {
            expenseSortField.value = field;
            expenseSortDirection.value = 'desc';
        }
    }
};

// Export PDF dari hidden section
const exportToPDF = () => {
    try {
        // Get the hidden report element
        const hiddenReport = document.getElementById('hidden-report');
        
        if (!hiddenReport) {
            alert('Terjadi kesalahan: Element laporan tidak ditemukan');
            return;
        }

        // Create print window
        const printWindow = window.open('', '_blank', 'width=1000,height=1000');
        
        // Get the HTML content from hidden report
        const reportContent = hiddenReport.innerHTML;
        
        // Write to print window
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Keuangan ${props.monthLabel}</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        margin: 20px;
                        line-height: 1.4;
                        color: #333;
                    }
                    .header {
                        text-align: center;
                        border-bottom: 2px solid #333;
                        padding-bottom: 20px;
                        margin-bottom: 30px;
                    }
                    .company-name {
                        font-size: 24px;
                        font-weight: bold;
                        margin-bottom: 5px;
                    }
                    .report-title {
                        font-size: 20px;
                        margin-bottom: 10px;
                    }
                    .report-period {
                        font-size: 14px;
                        color: #666;
                    }
                    .summary-section {
                        margin: 30px 0;
                        padding: 20px;
                        background-color: #f8f9fa;
                        border-radius: 8px;
                    }
                    .summary-title {
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 15px;
                        text-align: center;
                    }
                    .summary-grid {
                        display: grid;
                        grid-template-columns: 1fr 1fr 1fr;
                        gap: 20px;
                        text-align: center;
                    }
                    .summary-item {
                        padding: 15px;
                        background: white;
                        border-radius: 6px;
                        border: 1px solid #ddd;
                    }
                    .summary-label {
                        font-size: 12px;
                        color: #666;
                        text-transform: uppercase;
                        margin-bottom: 5px;
                    }
                    .summary-value {
                        font-size: 16px;
                        font-weight: bold;
                    }
                    .summary-value.income { color: #22c55e; }
                    .summary-value.expense { color: #ef4444; }
                    .summary-value.balance.positive { color: #22c55e; }
                    .summary-value.balance.negative { color: #ef4444; }
                    
                    .table-section {
                        margin: 30px 0;
                        page-break-inside: avoid;
                    }
                    .table-title {
                        font-size: 16px;
                        font-weight: bold;
                        margin-bottom: 15px;
                        color: #333;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    th, td {
                        border: 1px solid #ddd;
                        padding: 8px;
                        text-align: left;
                        font-size: 12px;
                    }
                    th {
                        background-color: #f8f9fa;
                        font-weight: bold;
                        text-align: center;
                    }
                    .amount-income {
                        color: #22c55e;
                        font-weight: bold;
                        text-align: right;
                    }
                    .amount-expense {
                        color: #ef4444;
                        font-weight: bold;
                        text-align: right;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .footer {
                        margin-top: 50px;
                        text-align: right;
                        font-size: 12px;
                        color: #666;
                    }
                    @media print {
                        body { margin: 0; }
                        .page-break { page-break-after: always; }
                    }
                </style>
            </head>
            <body>
                ${reportContent}
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        
        // Print after content is loaded
        setTimeout(() => {
            printWindow.print();
            setTimeout(() => {
                printWindow.close();
            }, 100);
        }, 500);
        
    } catch (error) {
        console.error('Error exporting PDF:', error);
        alert('Terjadi kesalahan saat mengekspor PDF. Silakan coba lagi.');
    }
};

const printReport = () => {
    exportToPDF(); // Use same function for print
};
</script>

<template>
    <MainLayout :user="user">
        <!-- Main Visible Content -->
        <div class="py-6 space-y-6 ps-16 pe-8">
            <!-- Header with Title and Actions -->
            <div class="flex justify-between items-center">
                <h1 class="text-5xl font-medium tracking-tight">
                    Laporan Keuangan
                </h1>
                <div class="flex gap-3">
                    <select 
                        v-model="selectedMonth" 
                        @change="filterByMonth" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    >
                        <option v-for="month in availableMonths" :key="month.value" :value="month.value">
                            {{ month.label }}
                        </option>
                    </select>
                    <button 
                        @click="exportToPDF" 
                        class="flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Export PDF
                    </button>
                    <button 
                        @click="printReport" 
                        class="flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md shadow-sm"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Cetak
                    </button>
                </div>
            </div>

            <!-- Header Laporan -->
            <div class="rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                <div class="mb-4">
                    <h2 class="text-2xl font-bold text-gray-800">Laporan Keuangan {{ monthLabel }}</h2>
                    <p class="text-gray-600">Periode: {{ periodStart }} - {{ periodEnd }}</p>
                    <p class="text-gray-600">Pengguna: {{ user.name }}</p>
                </div>
            </div>

            <!-- Ringkasan Keuangan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Total Pemasukan -->
                <div class="rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pemasukan</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ formatCurrency(summary.totalIncome) }}</dd>
                            <dd class="text-sm text-green-600" v-if="summary.incomeChange !== 0">
                                {{ summary.incomeChange > 0 ? '+' : '' }}{{ summary.incomeChange }}% dari bulan lalu
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Total Pengeluaran -->
                <div class="rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Pengeluaran</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ formatCurrency(summary.totalExpense) }}</dd>
                            <dd class="text-sm text-red-600" v-if="summary.expenseChange !== 0">
                                {{ summary.expenseChange > 0 ? '+' : '' }}{{ summary.expenseChange }}% dari bulan lalu
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Saldo Bersih -->
                <div class="rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt class="text-sm font-medium text-gray-500 truncate">Saldo Bersih</dt>
                            <dd class="text-lg font-medium" :class="summary.netBalance >= 0 ? 'text-green-600' : 'text-red-600'">
                                {{ formatCurrency(summary.netBalance) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pemasukan (Display Version) -->
            <div class="rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Pemasukan</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('date', 'income')">
                                    Tanggal
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('amount', 'income')">
                                    Jumlah
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber Saldo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in sortedIncomeTransactions" :key="transaction.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(transaction.date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ transaction.category?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                    {{ formatCurrency(transaction.amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ transaction.balance?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ transaction.description || '-' }}
                                </td>
                            </tr>
                            <tr v-if="incomeTransactions.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data pemasukan</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Pengeluaran (Display Version) -->
            <div class="rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Pengeluaran</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('date', 'expense')">
                                    Tanggal
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" @click="sortBy('amount', 'expense')">
                                    Jumlah
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sumber Saldo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in sortedExpenseTransactions" :key="transaction.id">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ formatDate(transaction.date) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ transaction.category?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                    {{ formatCurrency(transaction.amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ transaction.balance?.name || '-' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ transaction.description || '-' }}
                                </td>
                            </tr>
                            <tr v-if="expenseTransactions.length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">Tidak ada data pengeluaran</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Hidden Report Section for PDF Export -->
        <div id="hidden-report" class="hidden">
            <!-- Header Report -->
            <div class="header">
                <div class="company-name">SmartBook Keeper</div>
                <div class="report-title">Laporan Keuangan {{ monthLabel }}</div>
                <div class="report-period">
                    Periode: {{ periodStart }} - {{ periodEnd }}<br>
                    Pengguna: {{ user.name }}<br>
                    Dicetak pada: {{ new Date().toLocaleDateString('id-ID') }}
                </div>
            </div>

            <!-- Summary Section -->
            <div class="summary-section">
                <div class="summary-title">Ringkasan Keuangan</div>
                <div class="summary-grid">
                    <div class="summary-item">
                        <div class="summary-label">Total Pemasukan</div>
                        <div class="summary-value income">{{ formatCurrency(summary.totalIncome) }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Total Pengeluaran</div>
                        <div class="summary-value expense">{{ formatCurrency(summary.totalExpense) }}</div>
                    </div>
                    <div class="summary-item">
                        <div class="summary-label">Saldo Bersih</div>
                        <div class="summary-value balance" :class="summary.netBalance >= 0 ? 'positive' : 'negative'">
                            {{ formatCurrency(summary.netBalance) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Income Transactions Table -->
            <div class="table-section" v-if="incomeTransactions.length > 0">
                <div class="table-title">Daftar Pemasukan</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Sumber Saldo</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="transaction in sortedIncomeTransactions" :key="transaction.id">
                            <td class="text-center">{{ formatDate(transaction.date) }}</td>
                            <td>{{ transaction.category?.name || '-' }}</td>
                            <td class="amount-income">{{ formatCurrency(transaction.amount) }}</td>
                            <td>{{ transaction.balance?.name || '-' }}</td>
                            <td>{{ transaction.description || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Page Break -->
            <div class="page-break" v-if="incomeTransactions.length > 0 && expenseTransactions.length > 0"></div>

            <!-- Expense Transactions Table -->
            <div class="table-section" v-if="expenseTransactions.length > 0">
                <div class="table-title">Daftar Pengeluaran</div>
                <table>
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Jumlah</th>
                            <th>Sumber Saldo</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="transaction in sortedExpenseTransactions" :key="transaction.id">
                            <td class="text-center">{{ formatDate(transaction.date) }}</td>
                            <td>{{ transaction.category?.name || '-' }}</td>
                            <td class="amount-expense">{{ formatCurrency(transaction.amount) }}</td>
                            <td>{{ transaction.balance?.name || '-' }}</td>
                            <td>{{ transaction.description || '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="footer">
                <div>SmartBook Keeper - Sistem Pengelolaan Keuangan</div>
                <div>Generated on {{ new Date().toLocaleString('id-ID') }}</div>
            </div>
        </div>
    </MainLayout>
</template>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    .print-break {
        page-break-after: always;
    }
}

.hidden {
    display: none !important;
}
</style>