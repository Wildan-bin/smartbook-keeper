<script setup>
import BalanceChart from '@/components/BalanceChart.vue';
import TurnoverChart from '@/components/TurnoverChart.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, LineElement, PointElement, Title, Tooltip } from 'chart.js';
import { onMounted, ref } from 'vue';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, Title, Tooltip, Legend);

// Add default values and make props non-required
const props = defineProps({
    totalBalance: {
        type: Number,
        default: 0,
    },
    turnover: {
        type: Object,
        default: () => ({
            income: 0,
            latestIncome: null,
        }),
    },
    balanceData: {
        type: Object,
        default: () => ({
            labels: [],
            datasets: [],
        }),
    },
    turnoverData: {
        type: Object,
        default: () => ({
            labels: [],
            datasets: [],
        }),
    },
    selectedMonth: {
        type: String,
        default: 'all',
    },
    availableMonths: {
        type: Array,
        default: () => [],
    },
    balanceView: {
        type: String,
        default: 'daily',
    },
    turnoverView: {
        type: String,
        default: 'daily',
    },
    recentTransactions: {
        type: Array,
        default: () => [],
    },
});

const user = usePage().props.auth.user;
const isLoaded = ref(false);

onMounted(() => {
    isLoaded.value = true;
});

// Format currency helper function
const formatCurrency = (amount, withSymbol = true) => {
    const formatted = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);

    return withSymbol ? formatted : formatted.replace('Rp', '');
};

// Get greeting based on time
const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
};

// Chart options for line chart
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'top',
        },
    },
    scales: {
        x: {
            type: 'category',
            display: true,
            grid: {
                display: false,
            },
        },
        y: {
            type: 'linear',
            beginAtZero: true,
            display: true,
            ticks: {
                callback: (value) => formatCurrency(value),
            },
            grid: {
                display: false,
            },
        },
    },
};

// Chart options for bar chart
const barChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: 'y',
    plugins: {
        legend: {
            display: true,
            position: 'top',
        },
    },
    scales: {
        x: {
            type: 'linear',
            beginAtZero: true,
            display: true,
            ticks: {
                callback: (value) => formatCurrency(value),
            },
            grid: {
                display: false,
            },
        },
        y: {
            type: 'category',
            display: true,
            grid: {
                display: false,
            },
        },
    },
};
</script>

<template>
    <MainLayout :user="user">
        <template v-if="isLoaded">
            <div class="py-6 ps-16 pe-8 space-y-6">
                <!-- Welcome Header -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-5xl font-medium tracking-tight text-gray-900 mb-2">
                            {{ getGreeting() }}, {{ user.name.split(' ')[0] }}! 👋
                        </h1>
                        <p class="text-lg text-gray-600">Berikut adalah ringkasan keuangan Anda hari ini</p>
                    </div>
                    <div class="flex items-center gap-3">   
                        <div class="bg-white rounded-2xl px-4 py-2 shadow-sm border border-gray-100">
                            <div class="text-sm text-gray-500">Hari ini</div>
                            <div class="font-semibold text-gray-900">{{ new Date().toLocaleDateString('id-ID', { 
                                weekday: 'long', 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric' 
                            }) }}</div>
                        </div>
                    </div>
                </div>

                <!-- Main Grid Layout -->
                <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">
                    <!-- Left Column -->
                    <div class="col-span-7 space-y-6">
                        <!-- Top Section - Balance Cards -->
                        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                            <!-- Total Balance Card -->
                            <div class="group relative h-52 rounded-3xl bg-gradient-to-br from-[#007abb] via-[#1a8dd4] to-[#2FABEC] p-8 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] overflow-hidden">
                                <!-- Background Pattern -->
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-4 right-4 w-24 h-24 rounded-full border-2 border-white/20"></div>
                                    <div class="absolute bottom-8 right-12 w-16 h-16 rounded-full border-2 border-white/20"></div>
                                </div>
                                
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-2xl">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-8 h-8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3"
                                                />
                                            </svg>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm opacity-80">Total</div>
                                            <div class="text-xs opacity-60">Saldo Keseluruhan</div>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2">
                                        <h2 class="text-lg font-medium opacity-90">Saldo Saat Ini</h2>
                                        <div class="text-3xl font-bold group-hover:text-4xl transition-all duration-300">
                                            {{ formatCurrency(totalBalance) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics Card -->
                            <div class="group relative h-52 rounded-3xl bg-white p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] border border-gray-100">
                                <div class="absolute top-6 right-6">
                                    <select
                                        v-if="availableMonths.length"
                                        :value="selectedMonth"
                                        @change="$inertia.get(route('dashboard'), { month: $event.target.value }, { preserveState: true })"
                                        class="rounded-xl border-gray-200 bg-gray-50 px-3 py-2 text-sm transition-all hover:border-[#007abb] focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 focus:bg-white"
                                    >
                                        <option v-for="month in availableMonths" :key="month.value" :value="month.value" class="text-sm">
                                            {{ month.label }}
                                        </option>
                                    </select>
                                </div>

                                <div class="space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="p-3 bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-8 h-8 text-green-600"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            <h2 class="text-lg font-medium text-gray-800">Total Omset</h2>
                                            <span
                                                v-if="turnover?.percentageChange"
                                                :class=" [
                                                    'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold',
                                                    turnover.percentageChange > 0 
                                                        ? 'bg-green-100 text-green-700' 
                                                        : 'bg-red-100 text-red-700',
                                                ]"
                                            >
                                                <svg 
                                                    :class=" [
                                                        'w-3 h-3 mr-1',
                                                        turnover.percentageChange > 0 ? 'rotate-0' : 'rotate-180'
                                                    ]" 
                                                    fill="currentColor" 
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L6.707 7.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                                </svg>
                                                {{ Math.abs(turnover.percentageChange) }}%
                                            </span>
                                        </div>

                                        <div class="text-3xl font-bold text-gray-900 group-hover:text-green-600 transition-colors duration-300">
                                            {{ formatCurrency(turnover?.income) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section - Balance Chart -->
                        <div class="rounded-3xl bg-white p-8 shadow-lg border border-gray-100">
                            <div class="mb-6 flex items-center justify-between">
                                <div>
                                    <h3 class="text-2xl font-semibold text-gray-900">Grafik Saldo</h3>
                                    <p class="text-sm text-gray-500 mt-1">Perkembangan saldo dari waktu ke waktu</p>
                                </div>
                                <select
                                    :value="balanceView"
                                    @change="$inertia.get(route('dashboard'), { balanceView: $event.target.value }, { preserveState: true })"
                                    class="rounded-xl border-gray-200 bg-gray-50 px-4 py-2 text-sm transition-all hover:border-[#007abb] focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 focus:bg-white"
                                >
                                    <option value="daily">📅 Harian</option>
                                    <option value="weekly">📊 Mingguan</option>
                                    <option value="monthly">📈 Bulanan</option>
                                </select>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50/50 to-white rounded-2xl p-4">
                                <BalanceChart
                                    :labels="balanceData.labels"
                                    :values="balanceData.values"
                                    :tooltips="balanceData.tooltips"
                                    class="h-[400px]"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Turnover Chart -->
                    <div class="col-span-5 rounded-3xl bg-white p-8 shadow-lg border border-gray-100">
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-semibold text-gray-900">Grafik Omset</h3>
                                <p class="text-sm text-gray-500 mt-1">Performa pendapatan</p>
                            </div>
                            <select
                                :value="turnoverView"
                                @change="$inertia.get(route('dashboard'), { turnoverView: $event.target.value }, { preserveState: true })"
                                class="rounded-xl border-gray-200 bg-gray-50 px-4 py-2 text-sm transition-all hover:border-[#007abb] focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 focus:bg-white"
                            >
                                <option value="daily">📅 Harian</option>
                                <option value="weekly">📊 Mingguan</option>
                                <option value="monthly">📈 Bulanan</option>
                            </select>
                        </div>
                        <div class="bg-gradient-to-br from-green-50/50 to-white rounded-2xl p-4 h-[calc(100%-120px)]">
                            <TurnoverChart
                                :labels="turnoverData.labels"
                                :values="turnoverData.values"
                                :tooltips="turnoverData.tooltips"
                                class="h-full"
                            />
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="rounded-3xl bg-white p-8 shadow-lg border border-gray-100">
                    <div class="mb-6 flex items-center justify-between">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900">Transaksi Terbaru</h3>
                            <p class="text-sm text-gray-500 mt-1">Aktivitas keuangan terkini Anda</p>
                        </div>
                        <Link 
                            :href="route('transactions.index')" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white rounded-xl hover:from-[#006699] hover:to-[#1a8dd4] transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg"
                        >
                            <span class="text-sm font-medium">Lihat Semua</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>

                    <div class="overflow-hidden rounded-2xl border border-gray-100">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr class="text-sm font-semibold text-gray-700">
                                        <th class="px-6 py-4 text-left">📅 Tanggal</th>
                                        <th class="px-6 py-4 text-left">🏷️ Tipe</th>
                                        <th class="px-6 py-4 text-left">📂 Kategori</th>
                                        <th class="px-6 py-4 text-left">💰 Jumlah</th>
                                        <th class="px-6 py-4 text-left">🏦 Saldo</th>
                                        <th class="px-6 py-4 text-left">📝 Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr 
                                        v-for="transaction in recentTransactions" 
                                        :key="transaction.id" 
                                        class="text-sm hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-transparent transition-colors duration-200"
                                    >
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ new Date(transaction.date).toLocaleDateString('id-ID', {
                                                day: '2-digit',
                                                month: 'short',
                                                year: 'numeric'
                                            }) }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span :class=" [
                                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                                transaction.type === 'income' 
                                                    ? 'bg-green-100 text-green-700' 
                                                    : 'bg-red-100 text-red-700'
                                            ]">
                                                {{ transaction.type === 'income' ? '📈 Pemasukan' : '📉 Pengeluaran' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-700">
                                            {{ transaction.category.name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span :class=" [
                                                'font-bold text-base',
                                                transaction.type === 'income' ? 'text-green-600' : 'text-red-600'
                                            ]">
                                                {{ transaction.type === 'income' ? '+' : '-' }}
                                                {{ formatCurrency(transaction.amount, false) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                                {{ transaction.balance.name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600 max-w-xs truncate">
                                            {{ transaction.description || 'Tidak ada deskripsi' }}
                                        </td>
                                    </tr>
                                    <tr v-if="recentTransactions.length === 0">
                                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-sm">Belum ada transaksi</p>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </MainLayout>
</template>

<style scoped>
/* Add subtle animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.group {
    animation: fadeInUp 0.6s ease-out;
}

/* Enhance hover effects */
.group:hover {
    transform: translateY(-2px);
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg, #007abb, #2FABEC);
    border-radius: 10px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(90deg, #006699, #1a8dd4);
}
</style>
