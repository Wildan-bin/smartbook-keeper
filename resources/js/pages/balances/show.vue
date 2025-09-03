<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3'
import { ref } from 'vue';

// Add default values to props
const props = defineProps({
    balances: {
        type: Array,
        default: () => [],
    },
    totalBalance: {
        type: Number,
        default: 0,
    },
    categories: {   
        type: Array,
        default: () => [],
    },
});

const balanceForm = useForm({
    name: '',
    initial_amount: '',
    currency: 'IDR',
});

const categoryForm = useForm({
    name: '',
    type: 'income',
    icon: '📝',
    color: '#007abb',
});

// Success message states
const showBalanceSuccess = ref(false);
const showCategorySuccess = ref(false);

const submitBalance = () => {
    balanceForm.post(route('balances.store'), {
        onSuccess: () => {
            balanceForm.reset();
            showBalanceSuccess.value = true;
            setTimeout(() => {
                showBalanceSuccess.value = false;
            }, 3000);
        },
    });
};

const submitCategory = () => {
    categoryForm.post(route('balances.category.store'), {
        onSuccess: () => {
            categoryForm.reset();
            showCategorySuccess.value = true;
            setTimeout(() => {
                showCategorySuccess.value = false;
            }, 3000);
        },
    });
};

// Helper function for safe number formatting
const formatCurrency = (amount, currency = 'IDR') => {
    if (amount === undefined || amount === null) return 'Rp 0';
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: currency,
        minimumFractionDigits: 0,
    }).format(amount);
};

// Get balance status for visual indicators
const getBalanceStatus = (amount) => {
    if (amount > 1000000) return 'excellent';
    if (amount > 500000) return 'good';
    if (amount > 100000) return 'average';
    return 'low';
};

// Icon options for categories
const iconOptions = [
    '💰', '🏦', '💼', '🏠', '🚗', '🍕', '🛒', '⚡', '📱', '🎮',
    '👕', '💊', '✈️', '🎬', '📚', '🏃', '🎵', '🌟', '🎨', '📝'
];

// Predefined colors
const colorOptions = [
    '#007abb', '#2FABEC', '#22c55e', '#ef4444', '#f59e0b', '#8b5cf6',
    '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1', '#14b8a6'
];

const user = usePage().props.auth.user;
</script>

<template>
    <MainLayout :user="user">
        <div class="px-4 sm:px-6 lg:ps-16 lg:pe-8 py-4 sm:py-6 space-y-4 sm:space-y-6 text-black">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-medium tracking-tight text-gray-900 mb-2">🏦 Kelola Saldo</h1>
                    <p class="text-base sm:text-lg text-gray-600">Atur dompet dan kategori transaksi Anda</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-white rounded-2xl px-4 py-2 shadow-sm border border-gray-100">
                        <div class="text-sm text-gray-500">Total Dompet</div>
                        <div class="font-semibold text-gray-900">{{ balances.length }} item</div>
                    </div>
                </div>
            </div>

            <!-- Success Messages -->
            <div v-if="showBalanceSuccess" class="bg-green-50 border border-green-200 text-green-700 px-4 sm:px-6 py-3 sm:py-4 rounded-2xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-sm sm:text-base">Dompet baru berhasil ditambahkan!</span>
                </div>
            </div>

            <div v-if="showCategorySuccess" class="bg-green-50 border border-green-200 text-green-700 px-4 sm:px-6 py-3 sm:py-4 rounded-2xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium text-sm sm:text-base">Kategori baru berhasil ditambahkan!</span>
                </div>
            </div>

            <!-- Total Balance Card -->
            <div class="group relative bg-gradient-to-br from-[#007abb] via-[#1a8dd4] to-[#2FABEC] text-white h-40 sm:h-48 lg:h-56 rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-[1.02] overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-3 right-3 sm:top-4 sm:right-4 lg:top-6 lg:right-6 w-16 h-16 sm:w-24 sm:h-24 lg:w-32 lg:h-32 border-2 border-white/20 rounded-full"></div>
                    <div class="absolute bottom-2 right-8 sm:bottom-3 sm:right-12 lg:bottom-4 lg:right-16 w-8 h-8 sm:w-12 sm:h-12 lg:w-20 lg:h-20 border-2 border-white/20 rounded-full"></div>
                </div>
                
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="bg-white/20 backdrop-blur-sm p-2 sm:p-2.5 lg:p-3 rounded-xl sm:rounded-2xl">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 lg:w-8 lg:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                            </svg>
                        </div>
                        <div class="text-right">
                            <div 
                                :class=" [
                                    'inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold',
                                    getBalanceStatus(totalBalance) === 'excellent' ? 'bg-emerald-100 text-emerald-700' :
                                    getBalanceStatus(totalBalance) === 'good' ? 'bg-green-100 text-green-700' :
                                    getBalanceStatus(totalBalance) === 'average' ? 'bg-yellow-100 text-yellow-700' :
                                    'bg-red-100 text-red-700'
                                ]"
                            >
                                {{ getBalanceStatus(totalBalance) === 'excellent' ? '🎉 Excellent' :
                                   getBalanceStatus(totalBalance) === 'good' ? '👍 Good' :
                                   getBalanceStatus(totalBalance) === 'average' ? '⚠️ Average' :
                                   '🚨 Low' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-1">
                        <h2 class="text-sm sm:text-base lg:text-lg font-medium opacity-90">Total Saldo Keseluruhan</h2>
                        <div class="text-xl sm:text-3xl lg:text-4xl font-bold group-hover:scale-105 transition-all duration-300">
                            {{ formatCurrency(totalBalance) }}
                        </div>
                        <div class="text-xs sm:text-sm opacity-75">Dari {{ balances.length }} dompet aktif</div>
                    </div>
                </div>
            </div>

            <!-- Current Wallets Grid -->
            <div class="space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <h3 class="text-xl sm:text-2xl font-semibold text-gray-900">💼 Dompet Anda</h3>
                    <span class="text-sm text-gray-500">{{ balances.length }} dompet tersedia</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="balance in balances" :key="balance.id" class="group relative bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-5 lg:p-6 shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 hover:scale-[1.02] overflow-hidden">
                        <!-- Background accent -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#007abb] to-[#2FABEC]"></div>
                        
                        <div class="space-y-3 sm:space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="bg-blue-50 p-1.5 sm:p-2 rounded-xl">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#007abb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div 
                                    :class=" [
                                        'w-2.5 h-2.5 sm:w-3 sm:h-3 rounded-full',
                                        getBalanceStatus(balance.current_amount) === 'excellent' ? 'bg-green-400' :
                                        getBalanceStatus(balance.current_amount) === 'good' ? 'bg-green-300' :
                                        getBalanceStatus(balance.current_amount) === 'average' ? 'bg-yellow-400' :
                                        'bg-red-400'
                                    ]"
                                ></div>
                            </div>
                            
                            <div>
                                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-1">{{ balance.name }}</h3>
                                <p class="text-lg sm:text-xl lg:text-2xl font-bold text-[#007abb] group-hover:text-[#2FABEC] transition-colors duration-200">
                                    {{ formatCurrency(balance.current_amount, balance.currency) }}
                                </p>
                                <p class="text-xs sm:text-sm text-gray-500 mt-1">{{ balance.currency }} • Aktif</p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state when no balances -->
                    <div v-if="balances.length === 0" class="col-span-full">
                        <div class="text-center py-8 sm:py-12 bg-gray-50 border-2 border-dashed border-gray-300 rounded-2xl sm:rounded-3xl">
                            <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <h3 class="text-base sm:text-lg font-medium text-gray-700 mb-2">Belum ada dompet</h3>
                            <p class="text-sm sm:text-base text-gray-500">Tambahkan dompet pertama Anda untuk mulai mengelola keuangan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add New Wallet -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 shadow-lg border border-gray-100">
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">✨ Tambahkan Dompet Baru</h3>
                    <p class="text-sm sm:text-base text-gray-600">Buat dompet baru untuk mengorganisir sumber saldo Anda</p>
                </div>

                <form @submit.prevent="submitBalance" class="space-y-4 sm:space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">💼 Nama Dompet</label>
                        <input
                            type="text"
                            v-model="balanceForm.name"
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200 text-sm sm:text-base"
                            placeholder="Contoh: Kas, Bank BCA, E-Wallet"
                            required
                        />
                        <p class="text-xs text-gray-500">Berikan nama yang mudah dikenali untuk dompet Anda</p>
                    </div>

                    <div class="flex justify-end pt-4 sm:pt-6 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="balanceForm.processing || !balanceForm.name"
                            class="inline-flex items-center px-6 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none text-sm sm:text-base"
                        >
                            <svg v-if="balanceForm.processing" class="animate-spin w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg v-else class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>{{ balanceForm.processing ? 'Menambahkan...' : 'Tambah Dompet' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Add Custom Category -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 shadow-lg border border-gray-100">
                <div class="mb-4 sm:mb-6">
                    <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">🏷️ Tambah Kategori Baru</h3>
                    <p class="text-sm sm:text-base text-gray-600">Buat kategori kustom untuk mengklasifikasikan transaksi</p>
                </div>

                <form @submit.prevent="submitCategory" class="space-y-4 sm:space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Category Name -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">📝 Nama Kategori</label>
                            <input
                                type="text"
                                v-model="categoryForm.name"
                                class="w-full px-3 sm:px-4 py-2.5 sm:py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200 text-sm sm:text-base"
                                placeholder="Contoh: Alat Tulis Kantor"
                                required
                            />
                        </div>

                        <!-- Type Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">📊 Tipe Kategori</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" value="income" v-model="categoryForm.type" class="sr-only peer">
                                    <div class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-center rounded-xl border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 transition-all duration-200 hover:border-green-300">
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <span class="text-base sm:text-lg">📈</span>
                                            <span class="font-medium text-xs sm:text-sm">Pemasukan</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input type="radio" value="expense" v-model="categoryForm.type" class="sr-only peer">
                                    <div class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-center rounded-xl border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all duration-200 hover:border-red-300">
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <span class="text-base sm:text-lg">📉</span>
                                            <span class="font-medium text-xs sm:text-sm">Pengeluaran</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <!-- Icon Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">🎨 Pilih Icon</label>
                            <div class="grid grid-cols-8 sm:grid-cols-10 gap-1 sm:gap-2 p-3 sm:p-4 bg-gray-50 rounded-xl">
                                <button
                                    v-for="icon in iconOptions"
                                    :key="icon"
                                    type="button"
                                    @click="categoryForm.icon = icon"
                                    :class=" [
                                        'p-1.5 sm:p-2 rounded-lg text-lg sm:text-xl hover:bg-white transition-all duration-200',
                                        categoryForm.icon === icon ? 'bg-[#007abb] text-white' : 'bg-white'
                                    ]"
                                >
                                    {{ icon }}
                                </button>
                            </div>
                            <input
                                type="text"
                                v-model="categoryForm.icon"
                                class="w-full px-3 sm:px-4 py-2 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-white transition-all duration-200 text-sm sm:text-base"
                                placeholder="Atau ketik emoji custom"
                            />
                        </div>

                        <!-- Color Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">🎨 Pilih Warna</label>
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-2 p-3 sm:p-4 bg-gray-50 rounded-xl">
                                <button
                                    v-for="color in colorOptions"
                                    :key="color"
                                    type="button"
                                    @click="categoryForm.color = color"
                                    :class=" [
                                        'w-6 h-6 sm:w-8 sm:h-8 rounded-lg border-2 transition-all duration-200',
                                        categoryForm.color === color ? 'border-gray-800 scale-110' : 'border-gray-300'
                                    ]"
                                    :style="{ backgroundColor: color }"
                                ></button>
                            </div>
                            <input
                                type="color"
                                v-model="categoryForm.color"
                                class="w-full h-10 sm:h-12 rounded-xl border border-gray-300 cursor-pointer"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end pt-4 sm:pt-6 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="categoryForm.processing || !categoryForm.name"
                            class="inline-flex items-center px-6 sm:px-8 py-2.5 sm:py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none text-sm sm:text-base"
                        >
                            <svg v-if="categoryForm.processing" class="animate-spin w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg v-else class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>{{ categoryForm.processing ? 'Menambahkan...' : 'Tambah Kategori' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Categories -->
            <div class="bg-white rounded-2xl sm:rounded-3xl p-4 sm:p-6 lg:p-8 shadow-lg border border-gray-100">
                <div class="mb-4 sm:mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <h3 class="text-xl sm:text-2xl font-semibold text-gray-900 mb-2">📂 Kategori Saat Ini</h3>
                        <p class="text-sm sm:text-base text-gray-600">Daftar semua kategori yang tersedia</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <span>{{ categories.length }} kategori</span>
                    </div>
                </div>

                <div class="grid gap-3">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="group flex items-center justify-between p-3 sm:p-4 bg-gray-50 rounded-xl sm:rounded-2xl hover:bg-gray-100 transition-all duration-200 border-l-4 hover:shadow-md"
                        :style="{ borderLeftColor: category.color || '#007abb' }"
                    >
                        <div class="flex items-center gap-3">
                            <div 
                                class="w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center rounded-xl" 
                                :style="{ backgroundColor: category.color + '20' }"
                            >
                                <span class="text-xl sm:text-2xl">{{ category.icon || '📝' }}</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900 text-sm sm:text-base">{{ category.name }}</div>
                                <div class="text-xs sm:text-sm text-gray-500">
                                    {{ category.type === 'income' ? 'Kategori Pemasukan' : 'Kategori Pengeluaran' }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-2">
                            <div class="w-3 h-3 sm:w-4 sm:h-4 rounded-full border-2 border-gray-300" :style="{ backgroundColor: category.color }"></div>
                            <span
                                :class=" [
                                    'inline-flex items-center px-2 sm:px-3 py-1 rounded-full text-xs font-semibold',
                                    category.type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                                ]"
                            >
                                {{ category.type === 'income' ? '📈 Pemasukan' : '📉 Pengeluaran' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Empty state for categories -->
                    <div v-if="categories.length === 0" class="text-center py-8 sm:py-12 bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl sm:rounded-2xl">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 sm:w-8 sm:h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-base sm:text-lg font-medium text-gray-700 mb-2">Belum ada kategori</h3>
                        <p class="text-sm sm:text-base text-gray-500">Tambahkan kategori untuk mengorganisir transaksi Anda</p>
                    </div>
                </div>
            </div>
        </div>
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

/* Radio button animation */
input[type="radio"]:checked + div {
    transform: scale(1.02);
}

/* Form field focus animation */
input:focus, select:focus {
    transform: translateY(-1px);
}

/* Icon selection animation */
button:active {
    transform: scale(0.95);
}
</style>
