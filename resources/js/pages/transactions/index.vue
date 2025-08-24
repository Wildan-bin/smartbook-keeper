<script setup>
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import MainLayout from '@/layouts/MainLayout.vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
    transactions: Object,
    balances: Array,
    categories: Array,
    totalBalance: Number,
    filters: {
        type: Object,
        default: () => ({
            date: null,
            month: null,
            type: 'all',
            category: null,
            balance: null
        })
    }
})

const form = useForm({
    balance_id: '',
    category_id: '',
    type: 'income',
    amount: '',
    description: '',
    date: new Date().toISOString().substr(0, 10)
})

// Mobile state management
const isMobile = ref(false)
const isTablet = ref(false)

// Filter states
const filterForm = useForm({
    date: props.filters.date || '',
    month: props.filters.month || '',
    type: props.filters.type || 'all',
    category: props.filters.category || '',
    balance: props.filters.balance || ''
})

const showFilters = ref(false)
const filterMode = ref('all') // 'all', 'daily', 'monthly'

// Edit Modal State
const showEditModal = ref(false)
const editForm = useForm({
    id: '',
    balance_id: '',
    category_id: '',
    type: 'income',
    amount: '',
    description: '',
    date: ''
})

// Delete confirmation state
const showDeleteConfirm = ref(false)
const deleteTransactionId = ref(null)

const checkScreenSize = () => {
    const width = window.innerWidth
    isMobile.value = width < 640
    isTablet.value = width >= 640 && width < 1024
}

onMounted(() => {
    checkScreenSize()
    window.addEventListener('resize', checkScreenSize)
})

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize)
})

// Filter kategori berdasarkan tipe transaksi
const filteredCategories = computed(() => {
    return props.categories.filter(category => category.type === form.type)
})

// Filter categories for edit form
const editFilteredCategories = computed(() => {
    return props.categories.filter(category => category.type === editForm.type)
})

// Filter categories for filter dropdown
const filterCategories = computed(() => {
    if (filterForm.type === 'all') {
        return props.categories
    }
    return props.categories.filter(category => category.type === filterForm.type)
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

// Edit Transaction Functions
const openEditModal = (transaction) => {
    editForm.reset()
    editForm.id = transaction.id
    editForm.balance_id = transaction.balance_id
    editForm.category_id = transaction.category_id
    editForm.type = transaction.type
    editForm.amount = transaction.amount
    editForm.description = transaction.description || ''
    if (transaction.date) {
        // Jika tanggal berformat datetime atau string lain, konversi ke YYYY-MM-DD
        const date = new Date(transaction.date)
        const year = date.getFullYear()
        const month = String(date.getMonth() + 1).padStart(2, '0')
        const day = String(date.getDate()).padStart(2, '0')
        editForm.date = `${year}-${month}-${day}`
    } else {
        editForm.date = ''
    }
    showEditModal.value = true
}

const updateTransaction = () => {
    editForm.put(route('transactions.update', editForm.id), {
        onSuccess: () => {
            showEditModal.value = false
            editForm.reset()
            showSuccessMessage.value = true
            setTimeout(() => {
                showSuccessMessage.value = false
            }, 3000)
        }
    })
}

const closeEditModal = () => {
    showEditModal.value = false
    editForm.reset()
}

// Delete Transaction Functions
const confirmDelete = (transactionId) => {
    deleteTransactionId.value = transactionId
    showDeleteConfirm.value = true
}

const deleteTransaction = () => {
    if (deleteTransactionId.value) {
        router.delete(route('transactions.destroy', deleteTransactionId.value), {
            onSuccess: () => {
                showDeleteConfirm.value = false
                deleteTransactionId.value = null
                showSuccessMessage.value = true
                setTimeout(() => {
                    showSuccessMessage.value = false
                }, 3000)
            }
        })
    }
}

const cancelDelete = () => {
    showDeleteConfirm.value = false
    deleteTransactionId.value = null
}

// Apply filters
const applyFilters = () => {
    const params = {}
    
    if (filterForm.date) params.date = filterForm.date
    if (filterForm.month) params.month = filterForm.month
    if (filterForm.type && filterForm.type !== 'all') params.type = filterForm.type
    if (filterForm.category) params.category = filterForm.category
    if (filterForm.balance) params.balance = filterForm.balance
    
    router.get(route('transactions.index'), params, {
        preserveState: true,
        replace: true
    })
}

// Clear filters
const clearFilters = () => {
    filterForm.reset()
    filterMode.value = 'all'
    router.get(route('transactions.index'), {}, {
        preserveState: true,
        replace: true
    })
}

// Watch filter mode changes
watch(filterMode, (newMode) => {
    if (newMode === 'all') {
        filterForm.date = ''
        filterForm.month = ''
    } else if (newMode === 'daily') {
        filterForm.month = ''
        if (!filterForm.date) {
            filterForm.date = new Date().toISOString().substr(0, 10)
        }
    } else if (newMode === 'monthly') {
        filterForm.date = ''
        if (!filterForm.month) {
            const now = new Date()
            filterForm.month = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`
        }
    }
})

// Auto-apply filters when changed
watch(filterForm, () => {
    applyFilters()
}, { deep: true })

const user = usePage().props.auth.user

// Helper functions - Mobile optimized
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
        year: isMobile.value ? '2-digit' : 'numeric'
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

// Generate month options for the past 12 months
const monthOptions = computed(() => {
    const options = []
    const now = new Date()
    
    for (let i = 0; i < 12; i++) {
        const date = new Date(now.getFullYear(), now.getMonth() - i, 1)
        const value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`
        const label = date.toLocaleDateString('id-ID', { 
            year: 'numeric', 
            month: 'long' 
        })
        options.push({ value, label })
    }
    
    return options
})

// Check if any filters are active
const hasActiveFilters = computed(() => {
    return filterForm.date || filterForm.month || 
           (filterForm.type && filterForm.type !== 'all') || 
           filterForm.category || filterForm.balance
})
</script>

<template>
    <MainLayout :user="user">
        <!-- Mobile-Responsive Container -->
        <div 
            class="space-y-4 sm:space-y-6"
            :class=" [
                isMobile ? 'px-4 py-4' : isTablet ? 'px-6 py-5 ps-10 pe-8' : 'ps-16 pe-8 py-6'
            ]"
        >
            <!-- Header Section - Mobile Optimized -->
            <div class="flex flex-col gap-3 sm:gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="space-y-1 sm:space-y-2">
                    <h1 
                        class="font-medium tracking-tight text-gray-900"
                        :class=" [
                            isMobile ? 'text-2xl' : isTablet ? 'text-3xl' : 'text-5xl'
                        ]"
                    >
                        <span class="text-base sm:text-2xl lg:text-4xl">💰</span> Transaksi
                    </h1>
                    <p 
                        class="text-gray-600"
                        :class=" [
                            isMobile ? 'text-sm' : isTablet ? 'text-base' : 'text-lg'
                        ]"
                    >
                        Kelola pemasukan dan pengeluaran Anda
                    </p>
                </div>
                <div class="flex items-center gap-2 sm:gap-3">
                    <div 
                        class="bg-white shadow-sm border border-gray-100"
                        :class=" [
                            isMobile ? 'rounded-lg px-3 py-2' : 'rounded-xl sm:rounded-2xl px-4 py-2'
                        ]"
                    >
                        <div 
                            class="text-gray-500"
                            :class=" [
                                isMobile ? 'text-xs' : 'text-sm'
                            ]"
                        >
                            Total Transaksi
                        </div>
                        <div 
                            class="font-semibold text-gray-900"
                            :class=" [
                                isMobile ? 'text-sm' : 'text-base'
                            ]"
                        >
                            {{ transactions.data.length }} item
                        </div>
                    </div>
                </div>
            </div>

            <!-- Success Message - Mobile Optimized -->
            <div 
                v-if="showSuccessMessage" 
                class="bg-green-50 border border-green-200 text-green-700 shadow-sm"
                :class=" [
                    isMobile ? 'px-4 py-3 rounded-lg' : 'px-6 py-4 rounded-xl sm:rounded-2xl'
                ]"
            >
                <div class="flex items-center">
                    <svg 
                        class="mr-2"
                        :class=" [
                            isMobile ? 'w-4 h-4' : 'w-5 h-5'
                        ]"
                        fill="currentColor" 
                        viewBox="0 0 20 20"
                    >
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span 
                        class="font-medium"
                        :class=" [
                            isMobile ? 'text-sm' : 'text-base'
                        ]"
                    >
                        Transaksi berhasil disimpan!
                    </span>
                </div>
            </div>

            <!-- Total Balance Card - Mobile Optimized -->
            <div 
                class="group relative bg-gradient-to-br from-[#007abb] via-[#1a8dd4] to-[#2FABEC] text-white shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-[1.02] overflow-hidden"
                :class=" [
                    isMobile 
                        ? 'h-36 rounded-xl sm:rounded-2xl p-4' 
                        : isTablet 
                            ? 'h-40 rounded-2xl p-6' 
                            : 'h-48 rounded-3xl p-8'
                ]"
            >
                <!-- Background Pattern - Mobile Optimized -->
                <div class="absolute inset-0 opacity-10">
                    <div 
                        class="absolute border-2 border-white/20 rounded-full"
                        :class=" [
                            isMobile 
                                ? 'top-3 right-3 w-16 h-16' 
                                : isTablet 
                                    ? 'top-4 right-4 w-24 h-24' 
                                    : 'top-6 right-6 w-32 h-32'
                        ]"
                    ></div>
                    <div 
                        class="absolute border-2 border-white/20 rounded-full"
                        :class=" [
                            isMobile 
                                ? 'bottom-2 right-8 w-10 h-10' 
                                : isTablet 
                                    ? 'bottom-3 right-12 w-14 h-14' 
                                    : 'bottom-4 right-16 w-20 h-20'
                        ]"
                    ></div>
                    <div 
                        v-if="!isMobile"
                        class="absolute border-2 border-white/20 rounded-full"
                        :class=" [
                            isTablet 
                                ? 'top-1/2 right-2 w-12 h-12' 
                                : 'top-1/2 right-4 w-16 h-16'
                        ]"
                    ></div>
                </div>
                
                <div class="relative z-10 h-full flex flex-col justify-between">
                    <div class="flex items-start justify-between">
                        <div 
                            class="bg-white/20 backdrop-blur-sm"
                            :class=" [
                                isMobile 
                                    ? 'p-2 rounded-lg' 
                                    : isTablet 
                                        ? 'p-2.5 rounded-xl' 
                                        : 'p-3 rounded-2xl'
                            ]"
                        >
                            <svg 
                                class="fill-none stroke-current" 
                                :class=" [
                                    isMobile ? 'w-5 h-5' : isTablet ? 'w-6 h-6' : 'w-8 h-8'
                                ]"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a2.25 2.25 0 0 0-2.25-2.25H15a3 3 0 1 1-6 0H5.25A2.25 2.25 0 0 0 3 12m18 0v6a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 9m18 0V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v3" />
                            </svg>
                        </div>
                        <div class="text-right">
                            <div 
                                class="inline-flex items-center rounded-full font-semibold"
                                :class=" [
                                    isMobile ? 'px-2 py-0.5 text-xs' : 'px-3 py-1 text-xs',
                                    getBalanceStatus() === 'excellent' ? 'bg-emerald-100 text-emerald-700' :
                                    getBalanceStatus() === 'good' ? 'bg-green-100 text-green-700' :
                                    getBalanceStatus() === 'average' ? 'bg-yellow-100 text-yellow-700' :
                                    'bg-red-100 text-red-700'
                                ]"
                            >
                                <span v-if="!isMobile">
                                    {{ getBalanceStatus() === 'excellent' ? '🎉 Excellent' :
                                       getBalanceStatus() === 'good' ? '👍 Good' :
                                       getBalanceStatus() === 'average' ? '⚠️ Average' :
                                       '🚨 Low' }}
                                </span>
                                <span v-else>
                                    {{ getBalanceStatus() === 'excellent' ? '🎉' :
                                       getBalanceStatus() === 'good' ? '👍' :
                                       getBalanceStatus() === 'average' ? '⚠️' :
                                       '🚨' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-1 sm:space-y-2">
                        <h2 
                            class="font-medium opacity-90"
                            :class=" [
                                isMobile ? 'text-sm' : isTablet ? 'text-base' : 'text-lg'
                            ]"
                        >
                            Total Saldo Saat Ini
                        </h2>
                        <div 
                            class="font-bold transition-all duration-300"
                            :class=" [
                                isMobile 
                                    ? 'text-xl group-hover:text-2xl' 
                                    : isTablet 
                                        ? 'text-2xl group-hover:text-3xl' 
                                        : 'text-4xl group-hover:text-5xl'
                            ]"
                        >
                            <!-- Mobile: Shortened format -->
                            <span v-if="isMobile">{{ formatCurrency(totalBalance) }}</span>
                            <!-- Desktop: Full format -->
                            <span v-else>{{ formatCurrency(totalBalance) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction Form - Mobile Optimized -->
            <div 
                class="bg-white shadow-lg border border-gray-100"
                :class=" [
                    isMobile 
                        ? 'rounded-xl sm:rounded-2xl p-4' 
                        : isTablet 
                            ? 'rounded-2xl p-6' 
                            : 'rounded-3xl p-8'
                ]"
            >
                <div 
                    class="mb-4 sm:mb-6"
                    :class="{ 'mb-3': isMobile }"
                >
                    <h3 
                        class="font-semibold text-gray-900"
                        :class=" [
                            isMobile ? 'text-lg mb-1' : isTablet ? 'text-xl mb-2' : 'text-2xl mb-2'
                        ]"
                    >
                        <span :class="[isMobile ? 'text-sm' : 'text-lg']">✨</span> Tambah Transaksi Baru
                    </h3>
                    <p 
                        class="text-gray-600"
                        :class=" [
                            isMobile ? 'text-sm' : 'text-base'
                        ]"
                    >
                        Catat setiap pemasukan dan pengeluaran Anda
                    </p>
                </div>

                <form @submit.prevent="submit" class="space-y-4 sm:space-y-6">
                    <div 
                        class="grid gap-4 sm:gap-6"
                        :class=" [
                            isMobile ? 'grid-cols-1' : 'grid-cols-1 md:grid-cols-2'
                        ]"
                    >
                        <!-- Type Selection - Mobile Optimized -->
                        <div class="space-y-2">
                            <label 
                                class="block font-medium text-gray-700"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-sm'
                                ]"
                            >
                                <span :class="[isMobile ? 'text-sm' : '']">📊</span> Tipe Transaksi
                            </label>
                            <div class="grid grid-cols-2 gap-2 sm:gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="income" 
                                        v-model="form.type" 
                                        class="sr-only peer"
                                    >
                                    <div 
                                        class="w-full text-center border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 transition-all duration-200 hover:border-green-300"
                                        :class=" [
                                            isMobile 
                                                ? 'px-3 py-2 rounded-lg' 
                                                : 'px-4 py-3 rounded-xl'
                                        ]"
                                    >
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <span :class="[isMobile ? 'text-base' : 'text-lg']">📈</span>
                                            <span 
                                                class="font-medium"
                                                :class=" [
                                                    isMobile ? 'text-sm' : 'text-base'
                                                ]"
                                            >
                                                Pemasukan
                                            </span>
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
                                    <div 
                                        class="w-full text-center border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all duration-200 hover:border-red-300"
                                        :class=" [
                                            isMobile 
                                                ? 'px-3 py-2 rounded-lg' 
                                                : 'px-4 py-3 rounded-xl'
                                        ]"
                                    >
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <span :class="[isMobile ? 'text-base' : 'text-lg']">📉</span>
                                            <span 
                                                class="font-medium"
                                                :class=" [
                                                    isMobile ? 'text-sm' : 'text-base'
                                                ]"
                                            >
                                                Pengeluaran
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Balance Selection - Mobile Optimized -->
                        <div class="space-y-2">
                            <label 
                                class="block font-medium text-gray-700"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-sm'
                                ]"
                            >
                                <span :class="[isMobile ? 'text-sm' : '']">🏦</span> Pilih Dompet
                            </label>
                            <select 
                                v-model="form.balance_id" 
                                class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                :class=" [
                                    isMobile 
                                        ? 'px-3 py-2.5 rounded-lg text-sm' 
                                        : 'px-4 py-3 rounded-xl text-base'
                                ]"
                                required
                            >
                                <option value="" disabled>Pilih dompet...</option>
                                <option v-for="balance in balances" :key="balance.id" :value="balance.id">
                                    {{ balance.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Category Selection - Mobile Optimized -->
                        <div class="space-y-2">
                            <label 
                                class="block font-medium text-gray-700"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-sm'
                                ]"
                            >
                                <span :class="[isMobile ? 'text-sm' : '']">📂</span> Kategori
                            </label>
                            <select 
                                v-model="form.category_id" 
                                class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                :class=" [
                                    isMobile 
                                        ? 'px-3 py-2.5 rounded-lg text-sm' 
                                        : 'px-4 py-3 rounded-xl text-base'
                                ]"
                                required
                            >
                                <option value="" disabled>Pilih kategori...</option>
                                <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
                                    {{ category.icon }} {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Amount Input - Mobile Optimized -->
                        <div class="space-y-2">
                            <label 
                                class="block font-medium text-gray-700"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-sm'
                                ]"
                            >
                                <span :class="[isMobile ? 'text-sm' : '']">💰</span> Jumlah
                            </label>
                            <div class="relative">
                                <span 
                                    class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium"
                                    :class=" [
                                        isMobile ? 'text-sm' : 'text-base'
                                    ]"
                                >
                                    Rp
                                </span>
                                <input 
                                    type="number" 
                                    v-model="form.amount" 
                                    class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                    :class=" [
                                        isMobile 
                                            ? 'pl-10 pr-3 py-2.5 rounded-lg text-sm' 
                                            : 'pl-12 pr-4 py-3 rounded-xl text-base'
                                    ]"
                                    placeholder="0"
                                    min="0"
                                    step="1"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Date Input - Mobile Optimized -->
                        <div class="space-y-2">
                            <label 
                                class="block font-medium text-gray-700"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-sm'
                                ]"
                            >
                                <span :class="[isMobile ? 'text-sm' : '']">📅</span> Tanggal
                            </label>
                            <input 
                                type="date" 
                                v-model="form.date" 
                                class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                :class=" [
                                    isMobile 
                                        ? 'px-3 py-2.5 rounded-lg text-sm' 
                                        : 'px-4 py-3 rounded-xl text-base'
                                ]"
                                required
                            >
                        </div>

                        <!-- Description Input - Mobile Optimized -->
                        <div 
                            class="space-y-2"
                            :class=" [
                                isMobile ? '' : 'md:col-span-2'
                            ]"
                        >
                            <label 
                                class="block font-medium text-gray-700"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-sm'
                                ]"
                            >
                                <span :class="[isMobile ? 'text-sm' : '']">📝</span> Deskripsi
                            </label>
                            <input
                                type="text"
                                v-model="form.description"
                                class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                :class=" [
                                    isMobile 
                                        ? 'px-3 py-2.5 rounded-lg text-sm' 
                                        : 'px-4 py-3 rounded-xl text-base'
                                ]"
                                @blur="handleBlur"
                                placeholder="Tambahkan deskripsi (opsional)..."
                            >
                        </div>
                    </div>

                    <!-- Submit Button - Mobile Optimized -->
                    <div 
                        class="flex justify-end border-t border-gray-100"
                        :class=" [
                            isMobile ? 'pt-4' : 'pt-6'
                        ]"
                    >
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                            :class=" [
                                isMobile 
                                    ? 'px-6 py-2.5 rounded-lg text-sm w-full sm:w-auto justify-center' 
                                    : 'px-8 py-3 rounded-xl text-base'
                            ]"
                        >
                            <svg 
                                v-if="form.processing" 
                                class="animate-spin" 
                                :class="[isMobile ? 'w-4 h-4' : 'w-5 h-5']"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg 
                                v-else 
                                class=""
                                :class="[isMobile ? 'w-4 h-4' : 'w-5 h-5']"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <span>{{ form.processing ? 'Menyimpan...' : 'Simpan Transaksi' }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Transactions List with Filters - Mobile Optimized -->
            <div 
                class="bg-white shadow-lg border border-gray-100"
                :class=" [
                    isMobile 
                        ? 'rounded-xl sm:rounded-2xl p-4' 
                        : isTablet 
                            ? 'rounded-2xl p-6' 
                            : 'rounded-3xl p-8'
                ]"
            >
                <div 
                    class="flex items-center justify-between"
                    :class=" [
                        isMobile ? 'mb-4 flex-col gap-2 items-start' : 'mb-6 flex-row'
                    ]"
                >
                    <div>
                        <h3 
                            class="font-semibold text-gray-900"
                            :class=" [
                                isMobile ? 'text-lg mb-1' : isTablet ? 'text-xl mb-2' : 'text-2xl mb-2'
                            ]"
                        >
                            <span :class="[isMobile ? 'text-sm' : 'text-lg']">📋</span> Riwayat Transaksi
                        </h3>
                        <p 
                            class="text-gray-600"
                            :class=" [
                                isMobile ? 'text-sm' : 'text-base'
                            ]"
                        >
                            Daftar semua transaksi Anda
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Filter Toggle Button -->
                        <button
                            @click="showFilters = !showFilters"
                            class="inline-flex items-center gap-2 border border-gray-200 hover:border-[#007abb] transition-all duration-200"
                            :class=" [
                                isMobile 
                                    ? 'px-3 py-2 rounded-lg text-sm' 
                                    : 'px-4 py-2 rounded-xl text-sm',
                                hasActiveFilters 
                                    ? 'bg-[#007abb] text-white border-[#007abb]' 
                                    : 'bg-white text-gray-600'
                            ]"
                        >
                            <svg 
                                class=""
                                :class="[isMobile ? 'w-4 h-4' : 'w-4 h-4']"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Filter</span>
                            <span v-if="hasActiveFilters" 
                                  class="inline-flex items-center justify-center w-2 h-2 bg-yellow-400 rounded-full">
                            </span>
                        </button>
                        
                        <div class="flex items-center gap-2 text-gray-500">
                            <svg 
                                class="fill-none stroke-current" 
                                :class="[isMobile ? 'w-3 h-3' : 'w-4 h-4']"
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <span 
                                :class=" [
                                    isMobile ? 'text-xs' : 'text-sm'
                                ]"
                            >
                                {{ transactions.data.length }} transaksi
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Filter Panel -->
                <div 
                    v-if="showFilters"
                    class="border-t border-gray-100 pt-4 sm:pt-6 mb-4 sm:mb-6"
                >
                    <div class="space-y-4">
                        <!-- Filter Mode Selection -->
                        <div class="space-y-2">
                            <label 
                                class="block font-medium text-gray-700"
                                :class=" [
                                    isMobile ? 'text-sm' : 'text-sm'
                                ]"
                            >
                                <span :class="[isMobile ? 'text-sm' : '']">📅</span> Mode Filter
                            </label>
                            <div 
                                class="grid gap-2"
                                :class=" [
                                    isMobile ? 'grid-cols-1' : 'grid-cols-3'
                                ]"
                            >
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="all" 
                                        v-model="filterMode" 
                                        class="sr-only peer"
                                    >
                                    <div 
                                        class="w-full text-center border-2 border-gray-200 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:text-blue-700 transition-all duration-200 hover:border-blue-300"
                                        :class=" [
                                            isMobile 
                                                ? 'px-3 py-2 rounded-lg' 
                                                : 'px-4 py-2 rounded-xl'
                                        ]"
                                    >
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <span :class="[isMobile ? 'text-sm' : 'text-base']">📊</span>
                                            <span 
                                                class="font-medium"
                                                :class=" [
                                                    isMobile ? 'text-sm' : 'text-sm'
                                                ]"
                                            >
                                                <p>Semua</p>
                                            </span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="daily" 
                                        v-model="filterMode" 
                                        class="sr-only peer"
                                    >
                                    <div 
                                        class="w-full text-center border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 transition-all duration-200 hover:border-green-300"
                                        :class=" [
                                            isMobile 
                                                ? 'px-3 py-2 rounded-lg' 
                                                : 'px-4 py-2 rounded-xl'
                                        ]"
                                    >
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <span :class="[isMobile ? 'text-sm' : 'text-base']">📅</span>
                                            <span 
                                                class="font-medium"
                                                :class=" [
                                                    isMobile ? 'text-sm' : 'text-sm'
                                                ]"
                                            >
                                                <p>Harian</p>
                                            </span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="monthly" 
                                        v-model="filterMode" 
                                        class="sr-only peer"
                                    >
                                    <div 
                                        class="w-full text-center border-2 border-gray-200 peer-checked:border-purple-500 peer-checked:bg-purple-50 peer-checked:text-purple-700 transition-all duration-200 hover:border-purple-300"
                                        :class=" [
                                            isMobile 
                                                ? 'px-3 py-2 rounded-lg' 
                                                : 'px-4 py-2 rounded-xl'
                                        ]"
                                    >
                                        <div class="flex items-center justify-center gap-1 sm:gap-2">
                                            <span :class="[isMobile ? 'text-sm' : 'text-base']">📆</span>
                                            <span 
                                                class="font-medium"
                                                :class=" [
                                                    isMobile ? 'text-sm' : 'text-sm'
                                                ]"
                                            >
                                                <p>Bulanan</p>
                                            </span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Conditional Date/Month Input -->
                        <div 
                            class="grid gap-4"
                            :class=" [
                                isMobile ? 'grid-cols-1' : 'grid-cols-1 sm:grid-cols-3'
                            ]"
                        >
                            <!-- Daily Filter -->
                            <div v-if="filterMode === 'daily'" class="space-y-2">
                                <label 
                                    class="block font-medium text-gray-700"
                                    :class=" [
                                        isMobile ? 'text-sm' : 'text-sm'
                                    ]"
                                >
                                    <span :class="[isMobile ? 'text-sm' : '']">🗓️</span> Pilih Tanggal
                                </label>
                                <input 
                                    type="date" 
                                    v-model="filterForm.date"
                                    class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                    :class=" [
                                        isMobile 
                                            ? 'px-3 py-2.5 rounded-lg text-sm' 
                                            : 'px-4 py-3 rounded-xl text-sm'
                                    ]"
                                >
                            </div>

                            <!-- Monthly Filter -->
                            <div v-if="filterMode === 'monthly'" class="space-y-2">
                                <label 
                                    class="block font-medium text-gray-700"
                                    :class=" [
                                        isMobile ? 'text-sm' : 'text-sm'
                                    ]"
                                >
                                    <span :class="[isMobile ? 'text-sm' : '']">📆</span> Pilih Bulan
                                </label>
                                <select 
                                    v-model="filterForm.month"
                                    class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                    :class=" [
                                        isMobile 
                                            ? 'px-3 py-2.5 rounded-lg text-sm' 
                                            : 'px-4 py-3 rounded-xl text-sm'
                                    ]"
                                >
                                    <option value="">Semua bulan</option>
                                    <option v-for="month in monthOptions" :key="month.value" :value="month.value">
                                        {{ month.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Type Filter -->
                            <div class="space-y-2">
                                <label 
                                    class="block font-medium text-gray-700"
                                    :class=" [
                                        isMobile ? 'text-sm' : 'text-sm'
                                    ]"
                                >
                                    <span :class="[isMobile ? 'text-sm' : '']">📊</span> Tipe Transaksi
                                </label>
                                <select 
                                    v-model="filterForm.type"
                                    class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                    :class=" [
                                        isMobile 
                                            ? 'px-3 py-2.5 rounded-lg text-sm' 
                                            : 'px-4 py-3 rounded-xl text-sm'
                                    ]"
                                >
                                    <option value="all">Semua tipe</option>
                                    <option value="income">📈 Pemasukan</option>
                                    <option value="expense">📉 Pengeluaran</option>
                                </select>
                            </div>

                            <!-- Category Filter -->
                            <div class="space-y-2">
                                <label 
                                    class="block font-medium text-gray-700"
                                    :class=" [
                                        isMobile ? 'text-sm' : 'text-sm'
                                    ]"
                                >
                                    <span :class="[isMobile ? 'text-sm' : '']">📂</span> Kategori
                                </label>
                                <select 
                                    v-model="filterForm.category"
                                    class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                    :class=" [
                                        isMobile 
                                            ? 'px-3 py-2.5 rounded-lg text-sm' 
                                            : 'px-4 py-3 rounded-xl text-sm'
                                    ]"
                                >
                                    <option value="">Semua kategori</option>
                                    <option v-for="category in filterCategories" :key="category.id" :value="category.id">
                                        {{ category.icon }} {{ category.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Balance Filter -->
                            <div class="space-y-2">
                                <label 
                                    class="block font-medium text-gray-700"
                                    :class=" [
                                        isMobile ? 'text-sm' : 'text-sm'
                                    ]"
                                >
                                    <span :class="[isMobile ? 'text-sm' : '']">🏦</span> Dompet
                                </label>
                                <select 
                                    v-model="filterForm.balance"
                                    class="w-full border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                    :class=" [
                                        isMobile 
                                            ? 'px-3 py-2.5 rounded-lg text-sm' 
                                            : 'px-4 py-3 rounded-xl text-sm'
                                    ]"
                                >
                                    <option value="">Semua dompet</option>
                                    <option v-for="balance in balances" :key="balance.id" :value="balance.id">
                                        {{ balance.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <button
                                    @click="clearFilters"
                                    class="inline-flex items-center gap-2 text-gray-600 hover:text-red-600 transition-colors duration-200"
                                    :class=" [
                                        isMobile ? 'text-sm' : 'text-sm'
                                    ]"
                                >
                                    <svg 
                                        class=""
                                        :class="[isMobile ? 'w-4 h-4' : 'w-4 h-4']"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus Filter</span>
                                </button>
                            </div>
                            
                            <!-- Active Filters Display -->
                            <div v-if="hasActiveFilters" class="flex items-center gap-2">
                                <span 
                                    class="text-gray-500"
                                    :class=" [
                                        isMobile ? 'text-xs' : 'text-sm'
                                    ]"
                                >
                                    Filter aktif:
                                </span>
                                <div class="flex flex-wrap gap-1">
                                    <span v-if="filterForm.date" 
                                          class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium">
                                        📅 {{ formatDate(filterForm.date) }}
                                    </span>
                                    <span v-if="filterForm.month" 
                                          class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-medium">
                                        📆 {{ monthOptions.find(m => m.value === filterForm.month)?.label }}
                                    </span>
                                    <span v-if="filterForm.type !== 'all'" 
                                          class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        {{ filterForm.type === 'income' ? '📈 Pemasukan' : '📉 Pengeluaran' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile: Card Layout with Actions -->
                <div v-if="isMobile" class="space-y-3">
                    <div 
                        v-for="transaction in transactions.data" 
                        :key="transaction.id"
                        class="bg-gradient-to-r from-gray-50 to-white p-4 rounded-lg border border-gray-100 shadow-sm"
                    >
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span 
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold"
                                    :class=" [
                                        transaction.type === 'income' 
                                            ? 'bg-green-100 text-green-700' 
                                            : 'bg-red-100 text-red-700'
                                    ]"
                                >
                                    {{ transaction.type === 'income' ? '📈' : '📉' }}
                                </span>
                                <span class="text-sm font-medium text-gray-700">{{ transaction.category.name }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <!-- Edit Button -->
                                <button
                                    @click="openEditModal(transaction)"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                                <!-- Delete Button -->
                                <button
                                    @click="confirmDelete(transaction.id)"
                                    class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div 
                                        class="font-bold text-base"
                                        :class=" [
                                            transaction.type === 'income' ? 'text-green-600' : 'text-red-600'
                                        ]"
                                    >
                                        {{ transaction.type === 'income' ? '+' : '-' }}
                                        {{ formatCurrency(transaction.amount) }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ transaction.balance.name }}
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">
                                        {{ formatDate(transaction.date) }}
                                    </div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-600">
                                {{ transaction.description || 'Tidak ada deskripsi' }}
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Empty State -->
                    <div v-if="transactions.data.length === 0" class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-gray-700">
                            {{ hasActiveFilters ? 'Tidak ada transaksi sesuai filter' : 'Belum ada transaksi' }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ hasActiveFilters ? 'Coba ubah kriteria filter Anda' : 'Mulai catat transaksi pertama Anda' }}
                        </p>
                    </div>
                </div>

                <!-- Desktop: Table Layout with Actions -->
                <div v-else class="overflow-hidden border border-gray-100"
                     :class=" [
                        isTablet ? 'rounded-xl' : 'rounded-2xl'
                     ]"
                >
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr 
                                    class="font-semibold text-gray-700"
                                    :class=" [
                                        isTablet ? 'text-sm' : 'text-sm'
                                    ]"
                                >
                                    <th 
                                        class="text-left"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        📅 Tanggal
                                    </th>
                                    <th 
                                        class="text-left"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        🏷️ Tipe
                                    </th>
                                    <th 
                                        class="text-left"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        📂 Kategori
                                    </th>
                                    <th 
                                        class="text-left"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        💰 Jumlah
                                    </th>
                                    <th 
                                        class="text-left"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        🏦 Dompet
                                    </th>
                                    <th 
                                        class="text-left"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        📝 Deskripsi
                                    </th>
                                    <th 
                                        class="text-center"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        ⚙️ Aksi
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr 
                                    v-for="transaction in transactions.data" 
                                    :key="transaction.id" 
                                    class="hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-transparent transition-colors duration-200"
                                    :class=" [
                                        isTablet ? 'text-sm' : 'text-sm'
                                    ]"
                                >
                                    <td 
                                        class="font-medium text-gray-900"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        {{ formatDate(transaction.date) }}
                                    </td>
                                    <td 
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        <span 
                                            class="inline-flex items-center rounded-full font-semibold"
                                            :class=" [
                                                isTablet ? 'px-2 py-1 text-xs' : 'px-3 py-1 text-xs',
                                                transaction.type === 'income' 
                                                    ? 'bg-green-100 text-green-700' 
                                                    : 'bg-red-100 text-red-700'
                                            ]"
                                        >
                                            {{ transaction.type === 'income' ? '📈 Pemasukan' : '📉 Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td 
                                        class="text-gray-700"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">{{ transaction.category.icon }}</span>
                                            <span>{{ transaction.category.name }}</span>
                                        </div>
                                    </td>
                                    <td 
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        <span 
                                            class="font-bold"
                                            :class=" [
                                                isTablet ? 'text-sm' : 'text-base',
                                                transaction.type === 'income' ? 'text-green-600' : 'text-red-600'
                                            ]"
                                        >
                                            {{ transaction.type === 'income' ? '+' : '-' }}
                                            {{ isTablet ? formatCurrency(transaction.amount) : formatCurrency(transaction.amount).replace('Rp', '') }}
                                        </span>
                                    </td>
                                    <td 
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        <span 
                                            class="inline-flex items-center bg-blue-50 text-blue-700 rounded-full font-medium"
                                            :class=" [
                                                isTablet ? 'px-2 py-1 text-xs' : 'px-3 py-1 text-xs'
                                            ]"
                                        >
                                            {{ transaction.balance.name }}
                                        </span>
                                    </td>
                                    <td 
                                        class="text-gray-600 max-w-xs"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        <div class="truncate" :title="transaction.description">
                                            {{ transaction.description || 'Tidak ada deskripsi' }}
                                        </div>
                                    </td>
                                    <!-- New Actions Column -->
                                    <td 
                                        class="text-center"
                                        :class=" [
                                            isTablet ? 'px-4 py-3' : 'px-6 py-4'
                                        ]"
                                    >
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Edit Button -->
                                            <button
                                                @click="openEditModal(transaction)"
                                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                                title="Edit transaksi"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <!-- Delete Button -->
                                            <button
                                                @click="confirmDelete(transaction.id)"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                                title="Hapus transaksi"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="transactions.data.length === 0">
                                    <td 
                                        colspan="7" 
                                        class="text-center text-gray-500"
                                        :class=" [
                                            isTablet ? 'px-4 py-8' : 'px-6 py-12'
                                        ]"
                                    >
                                        <div class="flex flex-col items-center gap-3">
                                            <div 
                                                class="bg-gray-100 rounded-full flex items-center justify-center"
                                                :class=" [
                                                    isTablet ? 'w-12 h-12' : 'w-16 h-16'
                                                ]"
                                            >
                                                <svg 
                                                    class="text-gray-400" 
                                                    :class=" [
                                                        isTablet ? 'w-6 h-6' : 'w-8 h-8'
                                                    ]"
                                                    fill="none" 
                                                    stroke="currentColor" 
                                                    viewBox="0 0 24 24"
                                                >
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p 
                                                    class="font-medium text-gray-700"
                                                    :class=" [
                                                        isTablet ? 'text-base' : 'text-lg'
                                                    ]"
                                                >
                                                    {{ hasActiveFilters ? 'Tidak ada transaksi sesuai filter' : 'Belum ada transaksi' }}
                                                </p>
                                                <p 
                                                    class="text-gray-500"
                                                    :class=" [
                                                        isTablet ? 'text-sm' : 'text-sm'
                                                    ]"
                                                >
                                                    {{ hasActiveFilters ? 'Coba ubah kriteria filter Anda' : 'Mulai catat transaksi pertama Anda' }}
                                                </p>
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

        <!-- Edit Modal -->
        <div 
            v-if="showEditModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="closeEditModal"
        >
            <div 
                class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
            >
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-semibold text-gray-900">
                            <span class="text-lg">✏️</span> Edit Transaksi
                        </h3>
                        <button
                            @click="closeEditModal"
                            class="p-2 hover:bg-gray-100 rounded-lg transition-colors duration-200"
                        >
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form @submit.prevent="updateTransaction" class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Type Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-sm">📊</span> Tipe Transaksi
                            </label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="income" 
                                        v-model="editForm.type" 
                                        class="sr-only peer"
                                    >
                                    <div class="w-full text-center border-2 border-gray-200 peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:text-green-700 transition-all duration-200 hover:border-green-300 px-4 py-3 rounded-xl">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-lg">📈</span>
                                            <span class="font-medium text-base">Pemasukan</span>
                                        </div>
                                    </div>
                                </label>
                                <label class="relative flex cursor-pointer">
                                    <input 
                                        type="radio" 
                                        value="expense" 
                                        v-model="editForm.type" 
                                        class="sr-only peer"
                                    >
                                    <div class="w-full text-center border-2 border-gray-200 peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:text-red-700 transition-all duration-200 hover:border-red-300 px-4 py-3 rounded-xl">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-lg">📉</span>
                                            <span class="font-medium text-base">Pengeluaran</span>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Balance Selection -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-sm">🏦</span> Pilih Dompet
                            </label>
                            <select 
                                v-model="editForm.balance_id" 
                                class="w-full px-4 py-3 rounded-xl text-base border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
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
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-sm">📂</span> Kategori
                            </label>
                            <select 
                                v-model="editForm.category_id" 
                                class="w-full px-4 py-3 rounded-xl text-base border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                required
                            >
                                <option value="" disabled>Pilih kategori...</option>
                                <option v-for="category in editFilteredCategories" :key="category.id" :value="category.id">
                                    {{ category.icon }} {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Amount Input -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-sm">💰</span> Jumlah
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-medium text-base">
                                    Rp
                                </span>
                                <input 
                                    type="number" 
                                    v-model="editForm.amount" 
                                    class="w-full pl-12 pr-4 py-3 rounded-xl text-base border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                    placeholder="0"
                                    min="0"
                                    step="1"
                                    required
                                >
                            </div>
                        </div>

                        <!-- Date Input -->
                        <div class="space-y-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-sm">📅</span> Tanggal
                            </label>
                            <input 
                                type="date" 
                                v-model="editForm.date" 
                                class="w-full px-4 py-3 rounded-xl text-base border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                required
                            >
                        </div>

                        <!-- Description Input -->
                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">
                                <span class="text-sm">📝</span> Deskripsi
                            </label>
                            <input
                                type="text"
                                v-model="editForm.description"
                                class="w-full px-4 py-3 rounded-xl text-base border border-gray-300 focus:border-[#007abb] focus:ring-2 focus:ring-blue-100 bg-gray-50 focus:bg-white transition-all duration-200"
                                placeholder="Tambahkan deskripsi (opsional)..."
                            >
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <button 
                            type="button"
                            @click="closeEditModal"
                            class="px-6 py-3 rounded-xl text-base font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors duration-200"
                        >
                            Batal
                        </button>
                        <button 
                            type="submit" 
                            :disabled="editForm.processing"
                            class="inline-flex items-center gap-2 px-8 py-3 rounded-xl text-base font-semibold text-white bg-gradient-to-r from-[#007abb] to-[#2FABEC] shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <svg 
                                v-if="editForm.processing" 
                                class="animate-spin w-5 h-5"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <svg 
                                v-else 
                                class="w-5 h-5"
                                fill="none" 
                                stroke="currentColor" 
                                viewBox="0 0 24 24"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ editForm.processing ? 'Menyimpan...' : 'Simpan Perubahan' }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div 
            v-if="showDeleteConfirm"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
            @click.self="cancelDelete"
        >
            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                <div class="p-6">
                    <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div class="text-center mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">
                            Hapus Transaksi
                        </h3>
                        <p class="text-gray-600">
                            Apakah Anda yakin ingin menghapus transaksi ini? Tindakan ini tidak dapat dibatalkan.
                        </p>
                    </div>
                    <div class="flex items-center justify-end gap-3">
                        <button 
                            @click="cancelDelete"
                            class="px-6 py-3 rounded-xl text-base font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition-colors duration-200"
                        >
                            Batal
                        </button>
                        <button 
                            @click="deleteTransaction"
                            class="px-6 py-3 rounded-xl text-base font-semibold text-white bg-red-600 hover:bg-red-700 transition-colors duration-200"
                        >
                            Hapus
                        </button>
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

/* Enhance hover effects - disabled on mobile */
@media (min-width: 640px) {
    .group:hover {
        transform: translateY(-2px);
    }
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

/* Radio button animation - only on desktop */
@media (min-width: 640px) {
    input[type="radio"]:checked + div {
        transform: scale(1.02);
    }
}

/* Form field focus animation - only on desktop */
@media (min-width: 640px) {
    input:focus, select:focus {
        transform: translateY(-1px);
    }
}

/* Mobile optimizations */
@media (max-width: 640px) {
    /* Ensure better touch targets */
    button, select, input {
        min-height: 44px;
    }
    
    /* Better text readability */
    .text-xs {
        font-size: 0.75rem;
        line-height: 1rem;
    }
    
    /* Optimize scrolling on mobile */
    .overflow-x-auto {
        -webkit-overflow-scrolling: touch;
    }
    
    /* Disable hover effects on touch devices */
    .group:hover {
        transform: none;
    }
    
    input:focus, select:focus {
        transform: none;
    }
    
    input[type="radio"]:checked + div {
        transform: none;
    }
}

/* Tablet optimizations */
@media (min-width: 640px) and (max-width: 1024px) {
    /* Optimize table layout for tablet */
    table {
        font-size: 0.875rem;
    }
    
    /* Better spacing for tablet */
    .space-y-4 > * + * {
               margin-top: 1rem;
    }
}

/* Touch device specific improvements */
@media (hover: none) and (pointer: coarse) {
    .hover\:scale-105:hover {
        transform: none;
    }
    
    .group:hover {
        transform: none;
    }
    
    .transition-all {
        transition: none;
    }
}

/* Improve form accessibility on mobile */
@media (max-width: 640px) {
    select, input {
        -webkit-appearance: none;
        appearance: none;
        background-image: none;
    }
    
    select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.75rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 2.5rem;
    }
}
</style>