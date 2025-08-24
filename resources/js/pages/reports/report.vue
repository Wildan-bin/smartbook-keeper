<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { useForm, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';

// Responsive state
const isMobile = ref(false);
const isTablet = ref(false);

const checkScreenSize = () => {
    const width = window.innerWidth;
    isMobile.value = width < 640;
    isTablet.value = width >= 640 && width < 1024;
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize);
});

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

// Update functions to use new routes
const exportToPDF = () => {
    // Open PDF preview in new tab
    const url = route('reports.pdf-preview', { month: selectedMonth.value });
    window.open(url, '_blank');
};

const printReport = () => {
    // Open PDF preview with print parameter
    const url = route('reports.pdf-preview', { month: selectedMonth.value, print: 'true' });
    window.open(url, '_blank');
};
</script>

<template>
    <MainLayout :user="user">
        <!-- Main Visible Content -->
        <div 
            class="py-4 sm:py-6 space-y-4 sm:space-y-6"
            :class=" [
                isMobile ? 'px-4' : isTablet ? 'px-6' : 'ps-16 pe-8'
            ]"
        >
            <!-- Header with Title and Actions -->
            <div 
                class="flex items-start justify-between gap-4"
                :class=" [
                    isMobile ? 'flex-col' : 'flex-row items-center'
                ]"
            >
                <h1 
                    class="font-medium tracking-tight text-gray-900"
                    :class=" [
                        isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-5xl'
                    ]"
                >
                    📊 Laporan Keuangan
                </h1>
                <div 
                    class="flex gap-2 sm:gap-3"
                    :class=" [
                        isMobile ? 'w-full flex-col' : 'flex-row'
                    ]"
                >
                    <select 
                        v-model="selectedMonth" 
                        @change="filterByMonth" 
                        class="border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        :class=" [
                            isMobile ? 'rounded-lg px-3 py-2 text-sm w-full' : 'rounded-md'
                        ]"
                    >
                        <option v-for="month in availableMonths" :key="month.value" :value="month.value">
                            {{ month.label }}
                        </option>
                    </select>
                    <button 
                        @click="exportToPDF" 
                        class="flex items-center justify-center gap-2 bg-blue-500 hover:bg-blue-600 text-white shadow-sm transition-colors"
                        :class=" [
                            isMobile ? 'px-3 py-2 rounded-lg text-sm w-full' : 'px-4 py-2 rounded-md'
                        ]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span :class="[isMobile ? 'text-sm' : '']">Export PDF</span>
                    </button>
                    <!-- <button 
                        @click="printReport" 
                        class="flex items-center justify-center gap-2 bg-gray-500 hover:bg-gray-600 text-white shadow-sm transition-colors"
                        :class=" [
                            isMobile ? 'px-3 py-2 rounded-lg text-sm w-full' : 'px-4 py-2 rounded-md'
                        ]"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        <span :class="[isMobile ? 'text-sm' : '']">Cetak</span>
                    </button> -->
                </div>
            </div>

            <!-- Header Laporan -->
            <div 
                class="bg-white shadow-md border border-gray-100"
                :class=" [
                    isMobile ? 'rounded-xl p-4' : isTablet ? 'rounded-2xl p-5' : 'rounded-3xl p-6'
                ]"
            >
                <div 
                    class=""
                    :class=" [
                        isMobile ? 'mb-3' : 'mb-4'
                    ]"
                >
                    <h2 
                        class="font-bold text-gray-800"
                        :class=" [
                            isMobile ? 'text-lg mb-1' : isTablet ? 'text-xl mb-2' : 'text-2xl mb-2'
                        ]"
                    >
                        Laporan Keuangan {{ monthLabel }}
                    </h2>
                    <p 
                        class="text-gray-600"
                        :class=" [
                            isMobile ? 'text-sm' : 'text-base'
                        ]"
                    >
                        Periode: {{ periodStart }} - {{ periodEnd }}
                    </p>
                    <p 
                        class="text-gray-600"
                        :class=" [
                            isMobile ? 'text-sm' : 'text-base'
                        ]"
                    >
                        Pengguna: {{ user.name }}
                    </p>
                </div>
            </div>

            <!-- Ringkasan Keuangan -->
            <div 
                class="grid gap-4 sm:gap-6"
                :class=" [
                    isMobile ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-3'
                ]"
            >
                <!-- Total Pemasukan -->
                <div 
                    class="bg-white shadow-md border border-gray-100"
                    :class=" [
                        isMobile ? 'rounded-xl p-4' : isTablet ? 'rounded-2xl p-5' : 'rounded-3xl p-6'
                    ]"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div 
                                class="bg-green-100 rounded-full flex items-center justify-center"
                                :class=" [
                                    isMobile ? 'w-6 h-6' : 'w-8 h-8'
                                ]"
                            >
                                <svg 
                                    class="text-green-600"
                                    :class=" [
                                        isMobile ? 'w-4 h-4' : 'w-5 h-5'
                                    ]"
                                    fill="currentColor" viewBox="0 0 20 20"
                                >
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div 
                            class="w-0 flex-1"
                            :class=" [
                                isMobile ? 'ml-3' : 'ml-5'
                            ]"
                        >
                            <dt 
                                class="font-medium text-gray-500 truncate"
                                :class=" [
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                Total Pemasukan
                            </dt>
                            <dd 
                                class="font-medium text-gray-900"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-lg'
                                ]"
                            >
                                {{ formatCurrency(summary.totalIncome) }}
                            </dd>
                            <dd 
                                v-if="summary.incomeChange !== 0"
                                class="text-green-600"
                                :class=" [
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                {{ summary.incomeChange > 0 ? '+' : '' }}{{ summary.incomeChange }}% dari bulan lalu
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Total Pengeluaran -->
                <div 
                    class="bg-white shadow-md border border-gray-100"
                    :class=" [
                        isMobile ? 'rounded-xl p-4' : isTablet ? 'rounded-2xl p-5' : 'rounded-3xl p-6'
                    ]"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div 
                                class="bg-red-100 rounded-full flex items-center justify-center"
                                :class=" [
                                    isMobile ? 'w-6 h-6' : 'w-8 h-8'
                                ]"
                            >
                                <svg 
                                    class="text-red-600"
                                    :class=" [
                                        isMobile ? 'w-4 h-4' : 'w-5 h-5'
                                    ]"
                                    fill="currentColor" viewBox="0 0 20 20"
                                >
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div 
                            class="w-0 flex-1"
                            :class=" [
                                isMobile ? 'ml-3' : 'ml-5'
                            ]"
                        >
                            <dt 
                                class="font-medium text-gray-500 truncate"
                                :class=" [
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                Total Pengeluaran
                            </dt>
                            <dd 
                                class="font-medium text-gray-900"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-lg'
                                ]"
                            >
                                {{ formatCurrency(summary.totalExpense) }}
                            </dd>
                            <dd 
                                v-if="summary.expenseChange !== 0"
                                class="text-red-600"
                                :class=" [
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                {{ summary.expenseChange > 0 ? '+' : '' }}{{ summary.expenseChange }}% dari bulan lalu
                            </dd>
                        </div>
                    </div>
                </div>

                <!-- Saldo Bersih -->
                <div 
                    class="bg-white shadow-md border border-gray-100"
                    :class=" [
                        isMobile ? 'rounded-xl p-4' : isTablet ? 'rounded-2xl p-5' : 'rounded-3xl p-6'
                    ]"
                >
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div 
                                class="bg-blue-100 rounded-full flex items-center justify-center"
                                :class=" [
                                    isMobile ? 'w-6 h-6' : 'w-8 h-8'
                                ]"
                            >
                                <svg 
                                    class="text-blue-600"
                                    :class=" [
                                        isMobile ? 'w-4 h-4' : 'w-5 h-5'
                                    ]"
                                    fill="currentColor" viewBox="0 0 20 20"
                                >
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        <div 
                            class="w-0 flex-1"
                            :class=" [
                                isMobile ? 'ml-3' : 'ml-5'
                            ]"
                        >
                            <dt 
                                class="font-medium text-gray-500 truncate"
                                :class=" [
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                Saldo Bersih
                            </dt>
                            <dd 
                                class="font-medium"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-lg',
                                    summary.netBalance >= 0 ? 'text-green-600' : 'text-red-600'
                                ]"
                            >
                                {{ formatCurrency(summary.netBalance) }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Pemasukan -->
            <div 
                class="bg-white shadow-md border border-gray-100"
                :class=" [
                    isMobile ? 'rounded-xl p-4' : isTablet ? 'rounded-2xl p-5' : 'rounded-3xl p-6'
                ]"
            >
                <h3 
                    class="font-medium text-gray-900"
                    :class=" [
                        isMobile ? 'text-base mb-3' : 'text-lg mb-4'
                    ]"
                >
                    📈 Daftar Pemasukan
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th 
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                    @click="sortBy('date', 'income')"
                                >
                                    Tanggal
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </th>
                                <th 
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                >
                                    Kategori
                                </th>
                                <th 
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                    @click="sortBy('amount', 'income')"
                                >
                                    Jumlah
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </th>
                                <th 
                                    v-if="!isMobile"
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider"
                                    :class=" [
                                        isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                >
                                    Sumber Saldo
                                </th>
                                <th 
                                    v-if="!isMobile"
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider"
                                    :class=" [
                                        isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                >
                                    Deskripsi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in sortedIncomeTransactions" :key="transaction.id">
                                <td 
                                    class="whitespace-nowrap text-gray-900"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatDate(transaction.date) }}
                                </td>
                                <td 
                                    class="whitespace-nowrap text-gray-900"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ transaction.category?.name || '-' }}
                                </td>
                                <td 
                                    class="whitespace-nowrap font-medium text-green-600"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatCurrency(transaction.amount) }}
                                </td>
                                <td 
                                    v-if="!isMobile"
                                    class="whitespace-nowrap text-gray-900"
                                    :class=" [
                                        isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ transaction.balance?.name || '-' }}
                                </td>
                                <td 
                                    v-if="!isMobile"
                                    class="text-gray-900"
                                    :class=" [
                                        isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ transaction.description || '-' }}
                                </td>
                            </tr>
                            <tr v-if="incomeTransactions.length === 0">
                                <td 
                                    class="text-center text-gray-500"
                                    :class=" [
                                        isMobile ? 'px-3 py-4 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                    :colspan="isMobile ? 3 : 5"
                                >
                                    Tidak ada data pemasukan
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tabel Pengeluaran -->
            <div 
                class="bg-white shadow-md border border-gray-100"
                :class=" [
                    isMobile ? 'rounded-xl p-4' : isTablet ? 'rounded-2xl p-5' : 'rounded-3xl p-6'
                ]"
            >
                <h3 
                    class="font-medium text-gray-900"
                    :class=" [
                        isMobile ? 'text-base mb-3' : 'text-lg mb-4'
                    ]"
                >
                    📉 Daftar Pengeluaran
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th 
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                    @click="sortBy('date', 'expense')"
                                >
                                    Tanggal
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </th>
                                <th 
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                >
                                    Kategori
                                </th>
                                <th 
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                    @click="sortBy('amount', 'expense')"
                                >
                                    Jumlah
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path>
                                    </svg>
                                </th>
                                <th 
                                    v-if="!isMobile"
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider"
                                    :class=" [
                                        isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                >
                                    Sumber Saldo
                                </th>
                                <th 
                                    v-if="!isMobile"
                                    class="text-left font-medium text-gray-500 uppercase tracking-wider"
                                    :class=" [
                                        isTablet ? 'px-4 py-3 text-xs' : 'px-6 py-3 text-xs'
                                    ]"
                                >
                                    Deskripsi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="transaction in sortedExpenseTransactions" :key="transaction.id">
                                <td 
                                    class="whitespace-nowrap text-gray-900"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatDate(transaction.date) }}
                                </td>
                                <td 
                                    class="whitespace-nowrap text-gray-900"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ transaction.category?.name || '-' }}
                                </td>
                                <td 
                                    class="whitespace-nowrap font-medium text-red-600"
                                    :class=" [
                                        isMobile ? 'px-3 py-2 text-xs' : isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ formatCurrency(transaction.amount) }}
                                </td>
                                <td 
                                    v-if="!isMobile"
                                    class="whitespace-nowrap text-gray-900"
                                    :class=" [
                                        isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ transaction.balance?.name || '-' }}
                                </td>
                                <td 
                                    v-if="!isMobile"
                                    class="text-gray-900"
                                    :class=" [
                                        isTablet ? 'px-4 py-3 text-sm' : 'px-6 py-4 text-sm'
                                    ]"
                                >
                                    {{ transaction.description || '-' }}
                                </td>
                            </tr>
                            <tr v-if="expenseTransactions.length === 0">
                                <td 
                                    class="text-center text-gray-500"
                                    :class=" [
                                        isMobile ? 'px-3 py-4 text-xs' : 'px-6 py-4 text-sm'
                                    ]"
                                    :colspan="isMobile ? 3 : 5"
                                >
                                    Tidak ada data pengeluaran
                                </td>
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

/* Custom scrollbar for mobile */
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