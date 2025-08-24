<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    selectedMonth: String,
    monthLabel: String,
    periodStart: String,
    periodEnd: String,
    summary: Object,
    incomeTransactions: Array,
    expenseTransactions: Array,
    user: Object,
    generatedAt: String,
})

// Responsive detection
const isMobile = ref(false)
const isTablet = ref(false)

const checkScreenSize = () => {
    const width = window.innerWidth
    isMobile.value = width < 640
    isTablet.value = width >= 640 && width < 1024
}

onMounted(() => {
    checkScreenSize()
    window.addEventListener('resize', checkScreenSize)
    
    // Auto print dialog untuk preview
    setTimeout(() => {
        if (window.location.search.includes('print=true')) {
            window.print()
        }
    }, 1000)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize)
})

// Format currency
const formatCurrency = (amount) => {
    if (amount === undefined || amount === null) return 'Rp 0'
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0,
    }).format(amount)
}

// Format date
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: '2-digit', 
        year: 'numeric'
    })
}

// Download PDF
const downloadPdf = () => {
    const downloadUrl = route('reports.download-pdf', { month: props.selectedMonth })
    window.open(downloadUrl, '_blank')
}

// Print page
const printPage = () => {
    window.print()
}

// Close preview
const closePreview = () => {
    if (window.history.length > 1) {
        window.history.back()
    } else {
        window.close()
    }
}
</script>

<template>
    <div class="min-h-screen bg-white">
        <!-- Print Controls - Hidden on print -->
        <div 
            class="no-print sticky top-0 bg-white border-b border-gray-200 shadow-sm z-10"
            :class="[
                isMobile ? 'p-3' : 'p-4'
            ]"
        >
            <div 
                class="flex items-center justify-between gap-4"
                :class="[
                    isMobile ? 'flex-col' : 'flex-row'
                ]"
            >
                <div class="flex items-center gap-2">
                    <button 
                        @click="closePreview"
                        class="flex items-center gap-2 text-gray-600 hover:text-gray-800 transition-colors"
                        :class="[
                            isMobile ? 'text-sm' : 'text-base'
                        ]"
                    >
                        <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span>Kembali</span>
                    </button>
                    <span class="text-gray-400">|</span>
                    <h1 
                        class="font-semibold text-gray-900"
                        :class="[
                            isMobile ? 'text-sm' : 'text-lg'
                        ]"
                    >
                        Preview Laporan {{ monthLabel }}
                    </h1>
                </div>
                
                <div 
                    class="flex gap-2"
                    :class="[
                        isMobile ? 'w-full' : ''
                    ]"
                >
                    <button 
                        @click="printPage"
                        class="flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors"
                        :class="[
                            isMobile ? 'px-3 py-2 text-sm flex-1' : 'px-4 py-2'
                        ]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span>Cetak</span>
                    </button>
                    <button 
                        @click="downloadPdf"
                        class="flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors"
                        :class="[
                            isMobile ? 'px-3 py-2 text-sm flex-1' : 'px-4 py-2'
                        ]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Download PDF</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- PDF Content -->
        <div 
            class="max-w-4xl mx-auto bg-white"
            :class="[
                isMobile ? 'p-4' : isTablet ? 'p-6' : 'p-8'
            ]"
        >
            <!-- Header -->
            <div 
                class="text-center border-b-4 border-blue-600 bg-gradient-to-r from-blue-600 to-blue-800 text-white rounded-t-lg"
                :class="[
                    isMobile ? 'p-4 mb-6' : isTablet ? 'p-6 mb-8' : 'p-8 mb-10'
                ]"
            >
                <div 
                    class="font-bold mb-2"
                    :class="[
                        isMobile ? 'text-xl' : isTablet ? 'text-2xl' : 'text-3xl'
                    ]"
                >
                    📊 SmartBook Keeper
                </div>
                <div 
                    class="font-semibold mb-3"
                    :class="[
                        isMobile ? 'text-lg' : isTablet ? 'text-xl' : 'text-2xl'
                    ]"
                >
                    Laporan Keuangan {{ monthLabel }}
                </div>
                <div 
                    class="text-blue-100"
                    :class="[
                        isMobile ? 'text-xs space-y-1' : 'text-sm space-y-1'
                    ]"
                >
                    <div><strong>Periode:</strong> {{ periodStart }} - {{ periodEnd }}</div>
                    <div><strong>Pengguna:</strong> {{ user.name }}</div>
                    <div><strong>Dicetak pada:</strong> {{ generatedAt }}</div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div 
                class="grid gap-4 mb-8"
                :class="[
                    isMobile ? 'grid-cols-1' : 'grid-cols-3'
                ]"
            >
                <!-- Total Income -->
                <div 
                    class="bg-green-50 border-l-4 border-green-500 rounded-lg shadow-sm"
                    :class="[
                        isMobile ? 'p-4' : 'p-6'
                    ]"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div 
                                class="bg-green-100 rounded-full flex items-center justify-center"
                                :class="[
                                    isMobile ? 'w-8 h-8' : 'w-10 h-10'
                                ]"
                            >
                                <span 
                                    class=""
                                    :class="[
                                        isMobile ? 'text-lg' : 'text-xl'
                                    ]"
                                >
                                    📈
                                </span>
                            </div>
                        </div>
                        <div 
                            class="flex-1"
                            :class="[
                                isMobile ? 'ml-3' : 'ml-4'
                            ]"
                        >
                            <div 
                                class="font-medium text-gray-500 uppercase tracking-wide"
                                :class="[
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                Total Pemasukan
                            </div>
                            <div 
                                class="font-bold text-green-600"
                                :class="[
                                    isMobile ? 'text-lg' : 'text-xl'
                                ]"
                            >
                                {{ formatCurrency(summary.totalIncome) }}
                            </div>
                            <div 
                                v-if="summary.incomeChange !== 0"
                                class="text-green-600"
                                :class="[
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                {{ summary.incomeChange > 0 ? '▲' : '▼' }} {{ Math.abs(summary.incomeChange) }}% dari bulan lalu
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Expense -->
                <div 
                    class="bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm"
                    :class="[
                        isMobile ? 'p-4' : 'p-6'
                    ]"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div 
                                class="bg-red-100 rounded-full flex items-center justify-center"
                                :class="[
                                    isMobile ? 'w-8 h-8' : 'w-10 h-10'
                                ]"
                            >
                                <span 
                                    class=""
                                    :class="[
                                        isMobile ? 'text-lg' : 'text-xl'
                                    ]"
                                >
                                    📉
                                </span>
                            </div>
                        </div>
                        <div 
                            class="flex-1"
                            :class="[
                                isMobile ? 'ml-3' : 'ml-4'
                            ]"
                        >
                            <div 
                                class="font-medium text-gray-500 uppercase tracking-wide"
                                :class="[
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                Total Pengeluaran
                            </div>
                            <div 
                                class="font-bold text-red-600"
                                :class="[
                                    isMobile ? 'text-lg' : 'text-xl'
                                ]"
                            >
                                {{ formatCurrency(summary.totalExpense) }}
                            </div>
                            <div 
                                v-if="summary.expenseChange !== 0"
                                class="text-red-600"
                                :class="[
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                {{ summary.expenseChange > 0 ? '▲' : '▼' }} {{ Math.abs(summary.expenseChange) }}% dari bulan lalu
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Net Balance -->
                <div 
                    class="bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow-sm"
                    :class="[
                        isMobile ? 'p-4' : 'p-6'
                    ]"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div 
                                class="bg-blue-100 rounded-full flex items-center justify-center"
                                :class="[
                                    isMobile ? 'w-8 h-8' : 'w-10 h-10'
                                ]"
                            >
                                <span 
                                    class=""
                                    :class="[
                                        isMobile ? 'text-lg' : 'text-xl'
                                    ]"
                                >
                                    🏦
                                </span>
                            </div>
                        </div>
                        <div 
                            class="flex-1"
                            :class="[
                                isMobile ? 'ml-3' : 'ml-4'
                            ]"
                        >
                            <div 
                                class="font-medium text-gray-500 uppercase tracking-wide"
                                :class="[
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                Saldo Bersih
                            </div>
                            <div 
                                class="font-bold"
                                :class="[
                                    isMobile ? 'text-lg' : 'text-xl',
                                    summary.netBalance >= 0 ? 'text-green-600' : 'text-red-600'
                                ]"
                            >
                                {{ formatCurrency(summary.netBalance) }}
                            </div>
                            <div 
                                class=""
                                :class="[
                                    isMobile ? 'text-xs' : 'text-sm',
                                    summary.netBalance >= 0 ? 'text-green-600' : 'text-red-600'
                                ]"
                            >
                                {{ summary.netBalance >= 0 ? '✅ Surplus' : '⚠️ Defisit' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Income Transactions -->
            <div 
                v-if="incomeTransactions.length > 0"
                class="mb-8"
            >
                <h3 
                    class="font-bold text-gray-800 mb-4 flex items-center gap-2"
                    :class="[
                        isMobile ? 'text-lg' : 'text-xl'
                    ]"
                >
                    <span>📈</span>
                    Daftar Pemasukan ({{ incomeTransactions.length }} transaksi)
                </h3>
                <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-green-50">
                            <tr>
                                <th 
                                    class="text-left font-semibold text-gray-700 uppercase tracking-wider"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-3 text-sm'
                                    ]"
                                >
                                    Tanggal
                                </th>
                                <th 
                                    class="text-left font-semibold text-gray-700 uppercase tracking-wider"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-3 text-sm'
                                    ]"
                                >
                                    Kategori
                                </th>
                                <th 
                                    class="text-right font-semibold text-gray-700 uppercase tracking-wider"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-3 text-sm'
                                    ]"
                                >
                                    Jumlah
                                </th>
                                <th 
                                    v-if="!isMobile"
                                    class="text-left font-semibold text-gray-700 uppercase tracking-wider px-6 py-3 text-sm"
                                >
                                    Sumber
                                </th>
                                <th 
                                    v-if="!isMobile"
                                    class="text-left font-semibold text-gray-700 uppercase tracking-wider px-6 py-3 text-sm"
                                >
                                    Deskripsi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in incomeTransactions" :key="transaction.id">
                                <td 
                                    class="whitespace-nowrap text-gray-900"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatDate(transaction.date) }}
                                </td>
                                <td 
                                    class="whitespace-nowrap text-gray-900"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ transaction.category?.name || '-' }}
                                </td>
                                <td 
                                    class="whitespace-nowrap font-semibold text-green-600 text-right"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatCurrency(transaction.amount) }}
                                </td>
                                <td 
                                    v-if="!isMobile"
                                    class="whitespace-nowrap text-gray-900 px-6 py-4 text-sm"
                                >
                                    {{ transaction.balance?.name || '-' }}
                                </td>
                                <td 
                                    v-if="!isMobile"
                                    class="text-gray-900 px-6 py-4 text-sm"
                                >
                                    {{ transaction.description || '-' }}
                                </td>
                            </tr>
                            <tr class="bg-green-50 font-bold">
                                <td 
                                    class="text-center"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                    :colspan="isMobile ? 2 : 4"
                                >
                                    TOTAL PEMASUKAN
                                </td>
                                <td 
                                    class="text-right font-bold text-green-600"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatCurrency(summary.totalIncome) }}
                                </td>
                                <td v-if="!isMobile"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Expense Transactions -->
            <div v-if="expenseTransactions.length > 0">
                <h3 
                    class="font-bold text-gray-800 mb-4 flex items-center gap-2"
                    :class="[
                        isMobile ? 'text-lg' : 'text-xl'
                    ]"
                >
                    <span>📉</span>
                    Daftar Pengeluaran ({{ expenseTransactions.length }} transaksi)
                </h3>
                <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-red-50">
                            <tr>
                                <th 
                                    class="text-left font-semibold text-gray-700 uppercase tracking-wider"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-3 text-sm'
                                    ]"
                                >
                                    Tanggal
                                </th>
                                <th 
                                    class="text-left font-semibold text-gray-700 uppercase tracking-wider"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-3 text-sm'
                                    ]"
                                >
                                    Kategori
                                </th>
                                <th 
                                    class="text-right font-semibold text-gray-700 uppercase tracking-wider"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-3 text-sm'
                                    ]"
                                >
                                    Jumlah
                                </th>
                                <th 
                                    v-if="!isMobile"
                                    class="text-left font-semibold text-gray-700 uppercase tracking-wider px-6 py-3 text-sm"
                                >
                                    Sumber
                                </th>
                                <th 
                                    v-if="!isMobile"
                                    class="text-left font-semibold text-gray-700 uppercase tracking-wider px-6 py-3 text-sm"
                                >
                                    Deskripsi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in expenseTransactions" :key="transaction.id">
                                <td 
                                    class="whitespace-nowrap text-gray-900"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatDate(transaction.date) }}
                                </td>
                                <td 
                                    class="whitespace-nowrap text-gray-900"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ transaction.category?.name || '-' }}
                                </td>
                                <td 
                                    class="whitespace-nowrap font-semibold text-red-600 text-right"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatCurrency(transaction.amount) }}
                                </td>
                                <td 
                                    v-if="!isMobile"
                                    class="whitespace-nowrap text-gray-900 px-6 py-4 text-sm"
                                >
                                    {{ transaction.balance?.name || '-' }}
                                </td>
                                <td 
                                    v-if="!isMobile"
                                    class="text-gray-900 px-6 py-4 text-sm"
                                >
                                    {{ transaction.description || '-' }}
                                </td>
                            </tr>
                            <tr class="bg-red-50 font-bold">
                                <td 
                                    class="text-center"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                    :colspan="isMobile ? 2 : 4"
                                >
                                    TOTAL PENGELUARAN
                                </td>
                                <td 
                                    class="text-right font-bold text-red-600"
                                    :class="[
                                        isMobile ? 'px-3 py-2 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatCurrency(summary.totalExpense) }}
                                </td>
                                <td v-if="!isMobile"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="incomeTransactions.length === 0 && expenseTransactions.length === 0" class="text-center py-12">
                <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Tidak Ada Data</h3>
                <p class="text-gray-500">Belum ada transaksi untuk periode {{ monthLabel }}</p>
            </div>

            <!-- Footer -->
            <div 
                class="mt-12 pt-6 border-t border-gray-200 text-center text-gray-500"
                :class="[
                    isMobile ? 'text-xs' : 'text-sm'
                ]"
            >
                <div><strong>SmartBook Keeper</strong> - Sistem Pengelolaan Keuangan Digital</div>
                <div class="mt-1">© {{ new Date().getFullYear() }} SmartBook Keeper. Dokumen ini bersifat rahasia dan hanya untuk penggunaan internal.</div>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white !important;
        font-size: 10px !important;
    }
    
    .page-break {
        page-break-after: always;
    }
    
    /* Ensure colors print properly */
    * {
        -webkit-print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
}

/* Responsive table scrolling for mobile */
@media (max-width: 640px) {
    .overflow-x-auto::-webkit-scrollbar {
        height: 3px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, #007abb, #2FABEC);
        border-radius: 10px;
    }
}
</style>