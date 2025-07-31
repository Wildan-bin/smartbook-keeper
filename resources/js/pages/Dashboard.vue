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
            <div class="py-2 ps-16 pe-8">
                <h1 class="mt-2 mb-6 text-5xl font-medium tracking-tight">Beranda</h1>
                <!-- <h1 class="text-lg font-normal mb-8 text-black/60 ">Mempermudah Urusan Financial Anda </h1> -->

                <!-- Main Grid Layout -->
                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">
                    <!-- Left Column -->
                    <div class="col-span-7 space-y-3">
                        <!-- Top Section - Balance Cards -->
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <!-- Total Balance Card -->
                            <div class="h-44 rounded-3xl bg-linear-to-b from-[#007abb] to-[#2FABEC] p-6 text-white shadow-md inset-shadow-sm">
                                <div class="">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-9"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3"
                                        />
                                    </svg>
                                    <br />
                                    <h2 class="text-lg opacity-90">Saldo Saat Ini</h2>
                                </div>
                                <div class="text-4xl font-medium ">
                                    {{ formatCurrency(totalBalance) }}
                                </div>
                            </div>

                            <!-- Statistics Card -->
                            <div class="relative h-44 rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                                <div class="absolute top-6 right-3">
                                    <select
                                        v-if="availableMonths.length"
                                        :value="selectedMonth"
                                        @change="$inertia.get(route('dashboard'), { month: $event.target.value }, { preserveState: true })"
                                        class="rounded-xl border px-2 py-1 text-sm transition-colors hover:border-gray-400 focus:border-[#007abb] focus:ring-2 focus:ring-blue-200"
                                    >
                                        <option v-for="month in availableMonths" :key="month.value" :value="month.value" class="text-sm">
                                            {{ month.label }}
                                        </option>
                                    </select>
                                </div>

                                <div class="">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="size-9"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"
                                        />
                                    </svg>

                                    <br />
                                    <h2 class="text-lg opacity-90">Total Omset <span
                                    v-if="turnover?.percentageChange"
                                    :class="[
                                        'ml-2 text-base font-medium',
                                        turnover.percentageChange > 0 ? 'text-green-500' : 'text-red-500',
                                    ]"
                                >
                                    {{ turnover.percentageChange > 0 ? '↑' : '↓' }}
                                    {{ Math.abs(turnover.percentageChange) }}%
                                </span></h2>
                                </div>

                                <div class="text-4xl font-medium  text-black">
                                    {{ formatCurrency(turnover?.income) }}
                                </div>
                                
                            </div>
                        </div>

                        <!-- Bottom Section - Balance Chart -->
                        <div class="rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                            <div class="mb-6 flex items-center justify-between">
                                <h3 class="text-xl font-medium">Total Saldo</h3>
                                <select
                                    :value="balanceView"
                                    @change="$inertia.get(route('dashboard'), { balanceView: $event.target.value }, { preserveState: true })"
                                    class="rounded-xl border px-2 py-1 text-sm transition-colors hover:border-gray-400 focus:border-[#007abb] focus:ring-2 focus:ring-blue-200"
                                >
                                    <option value="daily">Harian</option>
                                    <option value="weekly">Mingguan</option>
                                    <option value="monthly">Bulanan</option>
                                </select>
                            </div>
                            <div class="">
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
                    <div class="col-span-5 rounded-3xl bg-white p-6 py-8 shadow-md inset-shadow-sm">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-xl font-medium">Total Omset</h3>
                            <select
                                :value="turnoverView"
                                @change="$inertia.get(route('dashboard'), { turnoverView: $event.target.value }, { preserveState: true })"
                                class="rounded-xl border px-2 py-1 text-sm transition-colors hover:border-gray-400 focus:border-[#007abb] focus:ring-2 focus:ring-blue-200"
                            >
                                <option value="daily">Harian</option>
                                <option value="weekly">Mingguan</option>
                                <option value="monthly">Bulanan</option>
                            </select>
                        </div>
                        <div class="mt-12 h-[calc(100%-88px)]">
                            <TurnoverChart
                                :labels="turnoverData.labels"
                                :values="turnoverData.values"
                                :tooltips="turnoverData.tooltips"
                                class="h-[100%]"
                            />
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="mt-6 mb-6 rounded-3xl bg-white p-6 shadow-md inset-shadow-sm">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-xl font-medium">Catatan Transaksi Terbaru</h3>
                        <Link :href="route('transactions.index')" class="text-sm font-medium text-blue-600 hover:text-blue-800">
                            Lihat lebih banyak →
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="border-b">
                                <tr class="text-md text-gray-600">
                                    <th class="p-2 text-left">Tanggal</th>
                                    <th class="p-2 text-left">Tipe</th>
                                    <th class="p-2 text-left">Kategori</th>
                                    <th class="p-2 text-left">Jumlah</th>
                                    <th class="p-2 text-left">Saldo</th>
                                    <th class="p-2 text-left">Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="">
                                <tr v-for="transaction in recentTransactions" :key="transaction.id" class="text-md hover">
                                    <td class="p-2">
                                        {{ new Date(transaction.date).toLocaleDateString('id-ID') }}
                                    </td>
                                    <td class="p-2">
                                        {{ transaction.type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                    </td>
                                    <td class="p-2">
                                        {{ transaction.category.name }}
                                    </td>
                                    <td :class="['p-2 font-medium', transaction.type === 'income' ? 'text-green-600' : 'text-red-600']">
                                        {{ transaction.type === 'income' ? '+' : '-' }}
                                        {{ formatCurrency(transaction.amount, false) }}
                                    </td>
                                    <td class="p-2">{{ transaction.balance.name }}</td>
                                    <td class="p-2 text-gray-600">{{ transaction.description }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </MainLayout>
</template>
