<script setup>
import BalanceChart from '@/components/BalanceChart.vue';
import TurnoverChart from '@/components/TurnoverChart.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, LineElement, PointElement, Title, Tooltip } from 'chart.js';
import { onMounted, onUnmounted, ref } from 'vue';

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
const isMobile = ref(false);
const isTablet = ref(false);

// Check screen size to determine chart behavior
const checkScreenSize = () => {
    const width = window.innerWidth;
    isMobile.value = width < 640;
    isTablet.value = width >= 640 && width < 1024;
};

onMounted(() => {
    isLoaded.value = true;
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize);
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

// Mobile-friendly currency format
const formatCurrencyMobile = (amount) => {
    if (amount >= 1000000) {
        return `Rp ${(amount / 1000000).toFixed(1)}M`;
    } else if (amount >= 1000) {
        return `Rp ${(amount / 1000).toFixed(0)}K`;
    }
    return formatCurrency(amount, false);
};

// Get greeting based on time
const getGreeting = () => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Selamat Pagi';
    if (hour < 15) return 'Selamat Siang';
    if (hour < 18) return 'Selamat Sore';
    return 'Selamat Malam';
};

// FIXED: Mobile-optimized chart options with stable height
const mobileChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    resizeDelay: 100, // Add delay to prevent rapid resizing
    interaction: {
        intersect: false,
        mode: 'index',
    },
    animation: {
        duration: isMobile.value ? 500 : 1000, // Faster animation on mobile
    },
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: isMobile.value ? 10 : 15,
                font: {
                    size: isMobile.value ? 10 : 11,
                },
            },
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            cornerRadius: 8,
            padding: 10,
            displayColors: false,
        },
    },
    scales: {
        x: {
            type: 'category',
            display: true,
            grid: {
                display: false,
            },
            ticks: {
                font: {
                    size: isMobile.value ? 9 : 10,
                },
                maxRotation: isMobile.value ? 45 : 45,
                minRotation: 0,
            },
        },
        y: {
            type: 'linear',
            beginAtZero: true,
            display: true,
            ticks: {
                font: {
                    size: isMobile.value ? 9 : 10,
                },
                callback: (value) => {
                    if (value >= 1000000) {
                        return `${(value / 1000000).toFixed(1)}M`;
                    } else if (value >= 1000) {
                        return `${(value / 1000).toFixed(0)}K`;
                    }
                    return formatCurrency(value, false);
                },
            },
            grid: {
                display: true,
                color: 'rgba(0, 0, 0, 0.05)',
            },
        },
    },
};

// FIXED: Mobile bar chart options with stable configuration
const mobileBarChartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    resizeDelay: 100, // Add delay to prevent rapid resizing
    indexAxis: isTablet.value ? 'x' : 'y', // Horizontal on mobile, vertical on tablet
    interaction: {
        intersect: false,
        mode: 'index',
    },
    animation: {
        duration: isMobile.value || isTablet.value ? 500 : 1000,
    },
    plugins: {
        legend: {
            display: true,
            position: 'top',
            labels: {
                usePointStyle: true,
                padding: isMobile.value ? 8 : 10,
                font: {
                    size: isMobile.value ? 10 : 11,
                },
            },
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: 'white',
            bodyColor: 'white',
            cornerRadius: 8,
            padding: 10,
            displayColors: false,
        },
    },
    scales: {
        x: {
            type: isTablet.value ? 'category' : 'linear',
            beginAtZero: !isTablet.value,
            display: true,
            ticks: {
                font: {
                    size: isMobile.value ? 9 : 10,
                },
                callback: isTablet.value ? undefined : (value) => {
                    if (value >= 1000000) {
                        return `${(value / 1000000).toFixed(1)}M`;
                    } else if (value >= 1000) {
                        return `${(value / 1000).toFixed(0)}K`;
                    }
                    return formatCurrency(value, false);
                },
                maxRotation: isTablet.value ? 45 : 0,
                minRotation: 0,
            },
            grid: {
                display: !isTablet.value,
                color: 'rgba(0, 0, 0, 0.05)',
            },
        },
        y: {
            type: isTablet.value ? 'linear' : 'category',
            beginAtZero: isTablet.value,
            display: true,
            ticks: {
                font: {
                    size: isMobile.value ? 9 : 10,
                },
                callback: isTablet.value ? (value) => {
                    if (value >= 1000000) {
                        return `${(value / 1000000).toFixed(1)}M`;
                    } else if (value >= 1000) {
                        return `${(value / 1000).toFixed(0)}K`;
                    }
                    return formatCurrency(value, false);
                } : undefined,
            },
            grid: {
                display: isTablet.value,
                color: 'rgba(0, 0, 0, 0.05)',
            },
        },
    },
};
</script>

<template>
    <MainLayout :user="user">
        <template v-if="isLoaded">
            <!-- Mobile: Responsive padding and spacing -->
            <div class="py-4 sm:py-6 px-4 sm:ps-16 sm:pe-8 space-y-4 sm:space-y-6">
                <!-- Welcome Header - Mobile Optimized -->
                <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="space-y-1 sm:space-y-2">
                        <h1 class="text-2xl sm:text-4xl lg:text-5xl font-medium tracking-tight text-gray-900">
                            {{ getGreeting() }}, {{ user.name.split(' ')[0] }}! 👋
                        </h1>
                        <p class="text-sm sm:text-base lg:text-lg text-gray-600">
                            Berikut adalah ringkasan keuangan Anda hari ini
                        </p>
                    </div>
                    <div class="flex items-center gap-2 sm:gap-3">   
                        <div class="bg-white rounded-xl sm:rounded-2xl px-3 sm:px-4 py-2 shadow-sm border border-gray-100">
                            <div class="text-xs sm:text-sm text-gray-500">Hari ini</div>
                            <div class="font-semibold text-xs sm:text-sm text-gray-900">
                                <!-- Mobile: Short date format -->
                                <span class="block sm:hidden">{{ new Date().toLocaleDateString('id-ID', { 
                                    day: '2-digit', 
                                    month: 'short' 
                                }) }}</span>
                                <!-- Desktop: Full date format -->
                                <span class="hidden sm:block">{{ new Date().toLocaleDateString('id-ID', { 
                                    weekday: 'long', 
                                    year: 'numeric', 
                                    month: 'long', 
                                    day: 'numeric' 
                                }) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Grid Layout - Mobile Responsive -->
                <div class="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-12">
                    <!-- Left Column - Mobile: Full width, Desktop: 7/12 -->
                    <div class="lg:col-span-7 space-y-4 sm:space-y-6">
                        <!-- Top Section - Balance Cards Mobile Responsive -->
                        <div class="grid grid-cols-1 gap-4 sm:gap-6 sm:grid-cols-2">
                            <!-- Total Balance Card -->
                            <div class="group relative h-44 sm:h-52 rounded-2xl sm:rounded-3xl bg-gradient-to-br from-[#007abb] via-[#1a8dd4] to-[#2FABEC] p-4 sm:p-8 text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] overflow-hidden">
                                <!-- Background Pattern - Scaled for mobile -->
                                <div class="absolute inset-0 opacity-10">
                                    <div class="absolute top-2 sm:top-4 right-2 sm:right-4 w-16 sm:w-24 h-16 sm:h-24 rounded-full border-2 border-white/20"></div>
                                    <div class="absolute bottom-4 sm:bottom-8 right-6 sm:right-12 w-10 sm:w-16 h-10 sm:h-16 rounded-full border-2 border-white/20"></div>
                                </div>
                                
                                <div class="relative z-10">
                                    <div class="flex items-center justify-between mb-3 sm:mb-4">
                                        <div class="p-2 sm:p-3 bg-white/20 backdrop-blur-sm rounded-xl sm:rounded-2xl">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-6 sm:w-8 h-6 sm:h-8"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3"
                                                />
                                            </svg>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-xs sm:text-sm opacity-80">Total</div>
                                            <div class="text-xs opacity-60 hidden sm:block">Saldo Keseluruhan</div>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-1 sm:space-y-2">
                                        <h2 class="text-sm sm:text-lg font-medium opacity-90">Saldo Saat Ini</h2>
                                        <div class="text-xl sm:text-3xl font-bold group-hover:text-2xl sm:group-hover:text-4xl transition-all duration-300">
                                            <!-- Mobile: Shortened format -->
                                            <span class="block sm:hidden">{{ formatCurrencyMobile(totalBalance) }}</span>
                                            <!-- Desktop: Full format -->
                                            <span class="hidden sm:block">{{ formatCurrency(totalBalance) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Statistics Card -->
                            <div class="group relative h-44 sm:h-52 rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] border border-gray-100">
                                <!-- Mobile: Simplified dropdown positioning -->
                                <div class="absolute top-3 sm:top-6 right-3 sm:right-6">
                                    <select
                                        v-if="availableMonths.length"
                                        :value="selectedMonth"
                                        @change="$inertia.get(route('dashboard'), { month: $event.target.value }, { preserveState: true })"
                                        class="rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm transition-all hover:border-[#007abb] focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 focus:bg-white"
                                    >
                                        <option v-for="month in availableMonths" :key="month.value" :value="month.value" class="text-xs sm:text-sm">
                                            {{ month.label }}
                                        </option>
                                    </select>
                                </div>

                                <div class="space-y-3 sm:space-y-4">
                                    <div class="flex items-center justify-between">
                                        <div class="p-2 sm:p-3 bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl sm:rounded-2xl">
                                            <svg
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-6 sm:w-8 h-6 sm:h-8 text-green-600"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <div class="space-y-2 sm:space-y-3">
                                        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                                            <h2 class="text-base sm:text-lg font-medium text-gray-800">Total Omset</h2>
                                            <span
                                                v-if="turnover?.percentageChange"
                                                :class="[
                                                    'inline-flex items-center px-2 sm:px-2.5 py-0.5 sm:py-1 rounded-full text-xs font-semibold',
                                                    turnover.percentageChange > 0 
                                                        ? 'bg-green-100 text-green-700' 
                                                        : 'bg-red-100 text-red-700',
                                                ]"
                                            >
                                                <svg 
                                                    :class="[
                                                        'w-2.5 sm:w-3 h-2.5 sm:h-3 mr-1',
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

                                        <div class="text-xl sm:text-3xl font-bold text-gray-900 group-hover:text-green-600 transition-colors duration-300">
                                            <!-- Mobile: Shortened format -->
                                            <span class="block sm:hidden">{{ formatCurrencyMobile(turnover?.income) }}</span>
                                            <!-- Desktop: Full format -->
                                            <span class="hidden sm:block">{{ formatCurrency(turnover?.income) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Section - Balance Chart Mobile Responsive -->
                        <div class="rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-8 shadow-lg border border-gray-100">
                            <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                <div>
                                    <h3 class="text-lg sm:text-2xl font-semibold text-gray-900">Grafik Saldo</h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Perkembangan saldo dari waktu ke waktu</p>
                                </div>
                                <select
                                    :value="balanceView"
                                    @change="$inertia.get(route('dashboard'), { balanceView: $event.target.value }, { preserveState: true })"
                                    class="rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 px-3 sm:px-4 py-2 text-xs sm:text-sm transition-all hover:border-[#007abb] focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 focus:bg-white w-full sm:w-auto"
                                >
                                    <option value="daily">📅 Harian</option>
                                    <option value="weekly">📊 Mingguan</option>
                                    <option value="monthly">📈 Bulanan</option>
                                </select>
                            </div>
                            <div class="bg-gradient-to-br from-blue-50/50 to-white rounded-xl sm:rounded-2xl p-2 sm:p-4">
                                <BalanceChart
                                    :labels="balanceData.labels"
                                    :values="balanceData.values"
                                    :tooltips="balanceData.tooltips"
                                    :options="mobileChartOptions"
                                    class="h-[250px] sm:h-[400px]"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Right Column - Turnover Chart Mobile: Full width, Desktop: 5/12 -->
                    <!-- FIXED: Stable height container with proper responsive behavior -->
                    <div class="lg:col-span-5 rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-8 shadow-lg border border-gray-100">
                        <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h3 class="text-lg sm:text-2xl font-semibold text-gray-900">Grafik Omset</h3>
                                <p class="text-xs sm:text-sm text-gray-500 mt-1">Performa pendapatan</p>
                            </div>
                            <select
                                :value="turnoverView"
                                @change="$inertia.get(route('dashboard'), { turnoverView: $event.target.value }, { preserveState: true })"
                                class="rounded-lg sm:rounded-xl border-gray-200 bg-gray-50 px-3 sm:px-4 py-2 text-xs sm:text-sm transition-all hover:border-[#007abb] focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 focus:bg-white w-full sm:w-auto"
                            >
                                <option value="daily">📅 Harian</option>
                                <option value="weekly">📊 Mingguan</option>
                                <option value="monthly">📈 Bulanan</option>
                            </select>
                        </div>
                        <!-- FIXED: Consistent height across all screen sizes -->
                        <div 
                            class="bg-gradient-to-br from-green-50/50 to-white rounded-xl sm:rounded-2xl p-2 sm:p-4 relative overflow-hidden"
                            :class="{
                                'h-[250px]': isMobile,
                                'h-[350px]': isTablet,
                                'h-[calc(100%-120px)]': !isMobile && !isTablet
                            }"
                        >
                            <!-- Chart Container with Fixed Dimensions -->
                            <div class="absolute inset-2 sm:inset-4">
                                <TurnoverChart
                                    :labels="turnoverData.labels"
                                    :values="turnoverData.values"
                                    :tooltips="turnoverData.tooltips"
                                    :options="mobileBarChartOptions"
                                    class="w-full h-full"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions Mobile Responsive -->
                <div class="rounded-2xl sm:rounded-3xl bg-white p-4 sm:p-8 shadow-lg border border-gray-100">
                    <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg sm:text-2xl font-semibold text-gray-900">Transaksi Terbaru</h3>
                            <p class="text-xs sm:text-sm text-gray-500 mt-1">Aktivitas keuangan terkini Anda</p>
                        </div>
                        <Link 
                            :href="route('transactions.index')" 
                            class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white rounded-lg sm:rounded-xl hover:from-[#006699] hover:to-[#1a8dd4] transition-all duration-200 transform hover:scale-105 shadow-md hover:shadow-lg text-sm font-medium w-full sm:w-auto"
                        >
                            <span>Lihat Semua</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </Link>
                    </div>

                    <!-- Mobile: Card layout, Desktop: Table layout -->
                    <div class="block sm:hidden space-y-3">
                        <!-- Mobile Transaction Cards -->
                        <div 
                            v-for="transaction in recentTransactions.slice(0, 5)" 
                            :key="transaction.id"
                            class="bg-gradient-to-r from-gray-50 to-white p-4 rounded-xl border border-gray-100 shadow-sm"
                        >
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span :class="[
                                        'inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold',
                                        transaction.type === 'income' 
                                            ? 'bg-green-100 text-green-700' 
                                            : 'bg-red-100 text-red-700'
                                    ]">
                                        {{ transaction.type === 'income' ? '📈' : '📉' }}
                                    </span>
                                    <span class="text-sm font-medium text-gray-700">{{ transaction.category.name }}</span>
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ new Date(transaction.date).toLocaleDateString('id-ID', {
                                        day: '2-digit',
                                        month: 'short'
                                    }) }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <div :class="[
                                        'font-bold text-lg',
                                        transaction.type === 'income' ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        {{ transaction.type === 'income' ? '+' : '-' }}
                                        {{ formatCurrencyMobile(transaction.amount) }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ transaction.balance.name }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-600 max-w-[120px] truncate">
                                        {{ transaction.description || 'Tidak ada deskripsi' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mobile: Empty state -->
                        <div v-if="recentTransactions.length === 0" class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <p class="text-sm text-gray-500">Belum ada transaksi</p>
                        </div>
                    </div>

                    <!-- Desktop: Table layout -->
                    <div class="hidden sm:block overflow-hidden rounded-xl sm:rounded-2xl border border-gray-100">
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                    <tr class="text-sm font-semibold text-gray-700">
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left">📅 Tanggal</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left">🏷️ Tipe</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left">📂 Kategori</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left">💰 Jumlah</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left">🏦 Saldo</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left">📝 Deskripsi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <tr 
                                        v-for="transaction in recentTransactions" 
                                        :key="transaction.id" 
                                        class="text-sm hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-transparent transition-colors duration-200"
                                    >
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 font-medium text-gray-900">
                                            {{ new Date(transaction.date).toLocaleDateString('id-ID', {
                                                day: '2-digit',
                                                month: 'short',
                                                year: 'numeric'
                                            }) }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                                            <span :class="[
                                                'inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold',
                                                transaction.type === 'income' 
                                                    ? 'bg-green-100 text-green-700' 
                                                    : 'bg-red-100 text-red-700'
                                            ]">
                                                {{ transaction.type === 'income' ? '📈 Pemasukan' : '📉 Pengeluaran' }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-700">
                                            {{ transaction.category.name }}
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                                            <span :class="[
                                                'font-bold text-base',
                                                transaction.type === 'income' ? 'text-green-600' : 'text-red-600'
                                            ]">
                                                {{ transaction.type === 'income' ? '+' : '-' }}
                                                {{ formatCurrency(transaction.amount, false) }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4">
                                            <span class="inline-flex items-center px-2 sm:px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                                {{ transaction.balance.name }}
                                            </span>
                                        </td>
                                        <td class="px-4 sm:px-6 py-3 sm:py-4 text-gray-600 max-w-xs truncate">
                                            {{ transaction.description || 'Tidak ada deskripsi' }}
                                        </td>
                                    </tr>
                                    <tr v-if="recentTransactions.length === 0">
                                        <td colspan="6" class="px-4 sm:px-6 py-8 sm:py-12 text-center text-gray-500">
                                            <div class="flex flex-col items-center gap-2">
                                                <svg class="w-8 sm:w-12 h-8 sm:h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    height: 4px;
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

/* FIXED: Stable chart container without conflicting heights */
.chart-container {
    position: relative;
    width: 100%;
    overflow: hidden;
}

/* Mobile specific adjustments */
@media (max-width: 640px) {
    /* Ensure mobile touch targets are adequate */
    button, select, input {
        min-height: 44px;
    }
    
    /* Improve readability on small screens */
    .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
    }
    
    /* Optimize spacing for mobile */
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    /* FIXED: Prevent chart resize issues on mobile */
    canvas {
        max-width: 100% !important;
        height: auto !important;
    }
}

/* FIXED: Tablet specific adjustments */
@media (min-width: 640px) and (max-width: 1024px) {
    /* Stable chart dimensions for tablet */
    canvas {
        max-width: 100% !important;
        height: auto !important;
    }
    
    /* Prevent layout shift on tablet */
    .chart-container {
        min-height: 350px;
    }
}

/* Desktop chart optimization */
@media (min-width: 1024px) {
    canvas {
        max-width: 100% !important;
        height: auto !important;
    }
}

/* Ensure proper text wrapping on mobile */
@media (max-width: 640px) {
    .truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 120px;
    }
}
</style>
