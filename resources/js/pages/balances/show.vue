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
        <div class="py-6 space-y-6 ps-16 pe-8">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-5xl font-medium tracking-tight text-gray-900 mb-2">
                        🏦 Kelola Saldo
                    </h1>
                    <p class="text-lg text-gray-600">Atur dompet dan kategori transaksi Anda</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-white rounded-2xl px-4 py-2 shadow-sm border border-gray-100">
                        <div class="text-sm text-gray-500">Total Dompet</div>
                        <div class="font-semibold text-gray-900">{{ balances.length }} item</div>
                    </div>
                </div>
            </div>

            <!-- Success Messages -->
            <div v-if="showBalanceSuccess" class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Dompet baru berhasil ditambahkan!</span>
                </div>
            </div>

            <div v-if="showCategorySuccess" class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Kategori baru berhasil ditambahkan!</span>
                </div>
            </div>

            <!-- Total Balance Card -->
            <div class="group relative h-56 rounded-3xl bg-gradient-to-br from-[#007abb] via-[#1a8dd4] to-[#2FABEC] p-8 text-white shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-[1.02] overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-6 right-6 w-32 h-32 rounded-full border-2 border-white/20"></div>
                    <div class="absolute bottom-4 right-16 w-20 h-20 rounded-full border-2 border-white/20"></div>
                    <div class="absolute top-1/2 right-4 w-16 h-16 rounded-full border-2 border-white/20"></div>
                </div>
                
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-2xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                            </svg>
                        </div>
                        <div class="text-right">
                            <div :class="[
                                'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                getBalanceStatus(totalBalance) === 'excellent' ? 'bg-emerald-100 text-emerald-700' :
                                getBalanceStatus(totalBalance) === 'good' ? 'bg-green-100 text-green-700' :
                                getBalanceStatus(totalBalance) === 'average' ? 'bg-yellow-100 text-yellow-700' :
                                'bg-red-100 text-red-700'
                            ]">
                                {{ getBalanceStatus(totalBalance) === 'excellent' ? '🎉 Excellent' :
                                   getBalanceStatus(totalBalance) === 'good' ? '👍 Good' :
                                   getBalanceStatus(totalBalance) === 'average' ? '⚠️ Average' :
                                   '🚨 Low' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <h2 class="text-lg font-medium opacity-90">Total Saldo Keseluruhan</h2>
                        <div class="text-4xl font-bold group-hover:text-5xl transition-all duration-300">
                            {{ formatCurrency(totalBalance) }}
                        </div>
                        <div class="text-sm opacity-75">
                            Dari {{ balances.length }} dompet aktif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Wallets Grid -->
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-semibold text-gray-900">💼 Dompet Anda</h3>
                    <span class="text-sm text-gray-500">{{ balances.length }} dompet tersedia</span>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
                    <div 
                        v-for="balance in balances" 
                        :key="balance.id" 
                        class="group relative rounded-3xl bg-white p-6 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] border border-gray-100 overflow-hidden"
                    >
                        <!-- Background accent -->
                        <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-[#007abb] to-[#2FABEC]"></div>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="p-2 bg-blue-50 rounded-xl">
                                    <svg class="w-6 h-6 text-[#007abb]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <div :class="[
                                    'w-3 h-3 rounded-full',
                                    getBalanceStatus(balance.current_amount) === 'excellent' ? 'bg-green-400' :
                                    getBalanceStatus(balance.current_amount) === 'good' ? 'bg-green-300' :
                                    getBalanceStatus(balance.current_amount) === 'average' ? 'bg-yellow-400' :
                                    'bg-red-400'
                                ]"></div>
                            </div>
                            
                            <div>
                                <h3 class="font-bold text-gray-900 text-lg mb-1">{{ balance.name }}</h3>
                                <p class="text-2xl font-bold text-[#007abb] group-hover:text-[#2FABEC] transition-colors duration-200">
                                    {{ formatCurrency(balance.current_amount, balance.currency) }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ balance.currency }} • Aktif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state when no balances -->
                    <div v-if="balances.length === 0" class="col-span-full">
                        <div class="text-center py-12 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-300">
                            <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada dompet</h3>
                            <p class="text-gray-500">Tambahkan dompet pertama Anda untuk mulai mengelola keuangan</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add New Wallet -->
            <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
                <div class="mb-6">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">✨ Tambahkan Dompet Baru</h3>
                    <p class="text-gray-600">Buat dompet baru untuk mengorganisir sumber saldo Anda</p>
                </div>

                <form @submit.prevent="submitBalance" class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">💼 Nama Dompet</label>
                        <input
                            type="text"
                            v-model="balanceForm.name"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                            placeholder="Contoh: Kas, Bank BCA, E-Wallet"
                            required
                        />
                        <p class="text-xs text-gray-500">Berikan nama yang mudah dikenali untuk dompet Anda</p>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="balanceForm.processing || !balanceForm.name"
                            class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <svg v-if="balanceForm.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>{{ balanceForm.processing ? 'Menambahkan...' : 'Tambah Dompet' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Add Custom Category -->
            <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
                <div class="mb-6">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">🏷️ Tambah Kategori Baru</h3>
                    <p class="text-gray-600">Buat kategori kustom untuk mengklasifikasikan transaksi</p>
                </div>

                <form @submit.prevent="submitCategory" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Category Name -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">📝 Nama Kategori</label>
                            <input
                                type="text"
                                v-model="categoryForm.name"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                placeholder="Contoh: Alat Tulis Kantor"
                                required
                            />
                        </div>

                        <!-- Type Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">📊 Tipe Kategori</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="income" 
                                        v-model="categoryForm.type" 
                                        class="sr-only peer"
                                    >
                                    <div class="w-full px-4 py-3 text-center rounded-xl border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 transition-all duration-200 hover:border-green-300">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-lg">📈</span>
                                            <span class="font-medium">Pemasukan</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="expense" 
                                        v-model="categoryForm.type" 
                                        class="sr-only peer"
                                    >
                                    <div class="w-full px-4 py-3 text-center rounded-xl border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all duration-200 hover:border-red-300">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-lg">📉</span>
                                            <span class="font-medium">Pengeluaran</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Icon Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">🎨 Pilih Icon</label>
                            <div class="grid grid-cols-10 gap-2 p-4 bg-gray-50 rounded-xl">
                                <button
                                    v-for="icon in iconOptions"
                                    :key="icon"
                                    type="button"
                                    @click="categoryForm.icon = icon"
                                    :class="[
                                        'p-2 rounded-lg text-xl hover:bg-white transition-all duration-200',
                                        categoryForm.icon === icon ? 'bg-[#007abb] text-white' : 'bg-white'
                                    ]"
                                >
                                    {{ icon }}
                                </button>
                            </div>
                            <input
                                type="text"
                                v-model="categoryForm.icon"
                                class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-white transition-all duration-200"
                                placeholder="Atau ketik emoji custom"
                            />
                        </div>

                        <!-- Color Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">🎨 Pilih Warna</label>
                            <div class="grid grid-cols-6 gap-2 p-4 bg-gray-50 rounded-xl">
                                <button
                                    v-for="color in colorOptions"
                                    :key="color"
                                    type="button"
                                    @click="categoryForm.color = color"
                                    :class="[
                                        'w-8 h-8 rounded-lg border-2 transition-all duration-200',
                                        categoryForm.color === color ? 'border-gray-800 scale-110' : 'border-gray-300'
                                    ]"
                                    :style="{ backgroundColor: color }"
                                ></button>
                            </div>
                            <input
                                type="color"
                                v-model="categoryForm.color"
                                class="w-full h-12 rounded-xl border border-gray-300 cursor-pointer"
                            />
                        </div>
                    </div>

                    <div class="flex justify-end pt-6 border-t border-gray-100">
                        <button
                            type="submit"
                            :disabled="categoryForm.processing || !categoryForm.name"
                            class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-500 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <svg v-if="categoryForm.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>{{ categoryForm.processing ? 'Menambahkan...' : 'Tambah Kategori' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Categories -->
            <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-2">📂 Kategori Saat Ini</h3>
                        <p class="text-gray-600">Daftar semua kategori yang tersedia</p>
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
                        class="group flex items-center justify-between rounded-2xl bg-gray-50 hover:bg-gray-100 p-4 transition-all duration-200 border-l-4 hover:shadow-md"
                        :style="{ borderLeftColor: category.color || '#007abb' }"
                    >
                        <div class="flex items-center gap-4">
                            <div class="flex items-center justify-center w-12 h-12 rounded-xl" :style="{ backgroundColor: category.color + '20' }">
                                <span class="text-2xl">{{ category.icon || '📝' }}</span>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">{{ category.name }}</div>
                                <div class="text-sm text-gray-500">{{ category.type === 'income' ? 'Kategori Pemasukan' : 'Kategori Pengeluaran' }}</div>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300" :style="{ backgroundColor: category.color }"></div>
                            <span
                                :class="[
                                    'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                    category.type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
                                ]"
                            >
                                {{ category.type === 'income' ? '📈 Pemasukan' : '📉 Pengeluaran' }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Empty state for categories -->
                    <div v-if="categories.length === 0" class="text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-300">
                        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-700 mb-2">Belum ada kategori</h3>
                        <p class="text-gray-500">Tambahkan kategori untuk mengorganisir transaksi Anda</p>
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
