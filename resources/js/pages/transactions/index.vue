<script setup>
import { ref, computed } from 'vue'
import { useForm } from '@inertiajs/vue3'
import MainLayout from '@/layouts/MainLayout.vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    transactions: Object,
    balances: Array,
    categories: Array,
    totalBalance: Number
})

const form = useForm({
    balance_id: '',
    category_id: '',
    type: 'income',
    amount: '',
    description: '',
    date: new Date().toISOString().substr(0, 10)
})

// Filter kategori berdasarkan tipe transaksi
const filteredCategories = computed(() => {
    return props.categories.filter(category => category.type === form.type)
})

const submit = () => {
    form.post(route('transactions.store'), {
        onSuccess: () => {
            form.reset()
            showSuccessMessage.value = true
            setTimeout(() => {
                showSuccessMessage.value = false
            }, 3000)
        }
    })
}

const handleBlur = () => {
    if (!form.description) {
        form.description = '-'
    }
}

const user = usePage().props.auth.user

// Helper functions
const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount)
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })
}

// State for success message
const showSuccessMessage = ref(false)

// Get balance percentage for visual indicator
const getBalanceStatus = () => {
    if (props.totalBalance > 1000000) return 'excellent'
    if (props.totalBalance > 500000) return 'good'
    if (props.totalBalance > 100000) return 'average'
    return 'low'
}
</script>

<template>
    <MainLayout :user="user">
        <div class="ps-16 pe-8 py-6 space-y-6">
            <!-- Header Section -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-5xl font-medium tracking-tight text-gray-900 mb-2">
                        💰 Transaksi
                    </h1>
                    <p class="text-lg text-gray-600">Kelola pemasukan dan pengeluaran Anda</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="bg-white rounded-2xl px-4 py-2 shadow-sm border border-gray-100">
                        <div class="text-sm text-gray-500">Total Transaksi</div>
                        <div class="font-semibold text-gray-900">{{ transactions.data.length }} item</div>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            <div v-if="showSuccessMessage" class="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-2xl shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Transaksi berhasil disimpan!</span>
                </div>
            </div>

            <!-- Total Balance Card -->
            <div class="group relative h-48 rounded-3xl bg-gradient-to-br from-[#007abb] via-[#1a8dd4] to-[#2FABEC] p-8 text-white shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-[1.02] overflow-hidden">
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
                                getBalanceStatus() === 'excellent' ? 'bg-emerald-100 text-emerald-700' :
                                getBalanceStatus() === 'good' ? 'bg-green-100 text-green-700' :
                                getBalanceStatus() === 'average' ? 'bg-yellow-100 text-yellow-700' :
                                'bg-red-100 text-red-700'
                            ]">
                                {{ getBalanceStatus() === 'excellent' ? '🎉 Excellent' :
                                   getBalanceStatus() === 'good' ? '👍 Good' :
                                   getBalanceStatus() === 'average' ? '⚠️ Average' :
                                   '🚨 Low' }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <h2 class="text-lg font-medium opacity-90">Total Saldo Saat Ini</h2>
                        <div class="text-4xl font-bold group-hover:text-5xl transition-all duration-300">
                            {{ formatCurrency(totalBalance) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Form -->
            <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
                <div class="mb-6">
                    <h3 class="text-2xl font-semibold text-gray-900 mb-2">✨ Tambah Transaksi Baru</h3>
                    <p class="text-gray-600">Catat setiap pemasukan dan pengeluaran Anda</p>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">📊 Tipe Transaksi</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="income" 
                                        v-model="form.type" 
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
                                        v-model="form.type" 
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

                        <!-- Balance Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">🏦 Pilih Dompet</label>
                            <select 
                                v-model="form.balance_id" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                required
                            >
                                <option value="" disabled>Pilih dompet...</option>
                                <option v-for="balance in balances" :key="balance.id" :value="balance.id">
                                    {{ balance.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Category Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">📂 Kategori</label>
                            <select 
                                v-model="form.category_id" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                required
                            >
                                <option value="" disabled>Pilih kategori...</option>
                                <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
                                    {{ category.icon }} {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Amount Input -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">💰 Jumlah</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                                <input 
                                    type="number" 
                                    v-model="form.amount" 
                                    class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                    placeholder="0"
                                    min="0"
                                    step="1000"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Date Input -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">📅 Tanggal</label>
                            <input 
                                type="date" 
                                v-model="form.date" 
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                required
                            >
                        </div>

                        <!-- Description Input -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">📝 Deskripsi</label>
                            <input
                                type="text"
                                v-model="form.description"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                @blur="handleBlur"
                                placeholder="Tambahkan deskripsi (opsional)..."
                            >
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6 border-t border-gray-100">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <svg v-if="form.processing" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Transaksi' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions List -->
            <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-semibold text-gray-900 mb-2">📋 Riwayat Transaksi</h3>
                        <p class="text-gray-600">Daftar semua transaksi Anda</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span>{{ transactions.data.length }} transaksi</span>
                    </div>
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
                                    <th class="px-6 py-4 text-left">🏦 Dompet</th>
                                    <th class="px-6 py-4 text-left">📝 Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr 
                                    v-for="transaction in transactions.data" 
                                    :key="transaction.id"
                                    class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-transparent transition-all duration-200"
                                >
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ formatDate(transaction.date) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold',
                                            transaction.type === 'income' 
                                                ? 'bg-green-100 text-green-700' 
                                                : 'bg-red-100 text-red-700'
                                        ]">
                                            {{ transaction.type === 'income' ? '📈 Pemasukan' : '📉 Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">{{ transaction.category.icon }}</span>
                                            <span class="text-gray-700">{{ transaction.category.name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span :class="[
                                            'font-bold text-base',
                                            transaction.type === 'income' ? 'text-green-600' : 'text-red-600'
                                        ]">
                                            {{ transaction.type === 'income' ? '+' : '-' }}
                                            {{ formatCurrency(transaction.amount) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-medium">
                                            {{ transaction.balance.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600 max-w-xs">
                                        <div class="truncate" :title="transaction.description">
                                            {{ transaction.description || 'Tidak ada deskripsi' }}
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="transactions.data.length === 0">
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="text-lg font-medium text-gray-700">Belum ada transaksi</p>
                                                <p class="text-sm text-gray-500">Mulai catat transaksi pertama Anda</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
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

/* Radio button animation */
input[type="radio"]:checked + div {
    transform: scale(1.02);
}

/* Form field focus animation */
input:focus, select:focus {
    transform: translateY(-1px);
}
</style>