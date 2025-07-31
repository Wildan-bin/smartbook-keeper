<script setup>
import { router } from '@inertiajs/vue3';
import { BarElement, CategoryScale, Chart as ChartJS, Legend, LineElement, LinearScale, PointElement, Title, Tooltip } from 'chart.js';
import { Bar, Line } from 'vue-chartjs';

// Register ALL required Chart.js components
ChartJS.register(Title, Tooltip, Legend, LineElement, LinearScale, CategoryScale, PointElement, BarElement);

// Props definition
const props = defineProps({
    balanceData: {
        type: Object,
        required: true,
    },
    turnoverData: {
        type: Object,
        required: true,
    },
    balanceView: {
        type: String,
        default: 'daily',
    },
    turnoverView: {
        type: String,
        default: 'daily',
    },
});

// Common chart options
const commonOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: false,
            position: 'bottom',
        },
    },
};

// Line chart options for balance
const lineChartOptions = {
    ...commonOptions,
    plugins: {
        legend: {
            display: false,
            position: 'bottom',
        },
    },
    scales: {
        x: {
            grid: {
                display: true,
            }
        },
        y: {
            beginAtZero: true,
            ticks: {
                callback: (value) => {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                    }).format(value);
                },
            },
            grid: {
                display: true,
            },
        },
    },
    datasets: {
        line: {
            borderColor: '#3b82f6', // Warna garis
            backgroundColor: 'rgba(59, 130, 246, 0.1)', // Warna area di bawah garis
            borderWidth: 2, // Ketebalan garis
            tension: 0.4, // Membuat garis lebih halus
        },
    },
};

// Bar chart options for turnover
const barChartOptions = {
    ...commonOptions,
    indexAxis: 'y',
    scales: {
        x: {
            beginAtZero: true,
            ticks: {
                callback: (value) => {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                    }).format(value);
                },
            },
            grid: {
                display: false,
            },
        },
        y: {
            grid: {
                display: false,
            },
        },
    },
    barThickness: 'flex',
    maxBarThickness: 30,
};

const changeBalanceView = (type) => {
    router.get(route('dashboard'), { balanceView: type }, { preserveScroll: true, preserveState: true });
};

const changeTurnoverView = (type) => {
    router.get(route('dashboard'), { turnoverView: type }, { preserveScroll: true, preserveState: true });
};
</script>

<template>
    <div class="w-full space-y-4">
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Balance Chart (Line) -->
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Total Saldo</h3>
                    <div class="flex space-x-2">
                        <button
                            v-for="type in ['daily', 'weekly', 'monthly']"
                            :key="type"
                            @click="changeBalanceView(type)"
                            class="rounded-lg px-3 py-1 text-sm transition-colors"
                            :class="balanceView === type ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200'"
                        >
                            {{ type === 'daily' ? 'Harian' : type === 'weekly' ? 'Mingguan' : 'Bulanan' }}
                        </button>
                    </div>
                </div>
                <div class="h-[400px]">
                    <Line v-if="balanceData?.datasets?.length" :data="balanceData" :options="lineChartOptions" />
                </div>
            </div>

            <!-- Turnover Chart (Bar) -->
            <div class="rounded-3xl bg-white p-6 shadow">
                <div class="mb-6 flex items-center justify-between">
                    <h3 class="text-lg font-semibold">Total Omset</h3>
                    <div class="flex space-x-2">
                        <button
                            v-for="type in ['daily', 'weekly', 'monthly']"
                            :key="type"
                            @click="changeTurnoverView(type)"
                            class="rounded-lg px-3 py-1 text-sm transition-colors"
                            :class="turnoverView === type ? 'bg-blue-600 text-white' : 'bg-gray-100 hover:bg-gray-200'"
                        >
                            {{ type === 'daily' ? 'Harian' : type === 'weekly' ? 'Mingguan' : 'Bulanan' }}
                        </button>
                    </div>
                </div>
                <div class="h-[400px]">
                    <Bar v-if="turnoverData?.datasets?.length" :data="turnoverData" :options="barChartOptions" />
                </div>
            </div>
        </div>
    </div>
</template>
