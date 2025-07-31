<script setup>
import MainLayout from '@/layouts/MainLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3'

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
    color: '#000000',
});

const submitBalance = () => {
    balanceForm.post(route('balances.store'), {
        onSuccess: () => balanceForm.reset(),
    });
};

const submitCategory = () => {
    categoryForm.post(route('balances.category.store'), {
        onSuccess: () => categoryForm.reset(),
    });
};

// Helper function for safe number formatting
const formatCurrency = (amount, currency = 'IDR') => {
    if (amount === undefined || amount === null) return '-';
    return amount.toLocaleString('id-ID', {
        style: 'currency',
        currency: currency,
    });
};

const user = usePage().props.auth.user
</script>

<template>
    <MainLayout :user="user">
        <div class="py-6 space-y-6 ps-16 pe-8">
            <h1 class="absolute top-6 ml-2 text-5xl font-medium mb-6 mt-2 tracking-tight">Saldo</h1>
            <!-- Total Balance -->
            <div class="mt-14 rounded-3xl bg-white p-4 shadow-md inset-shadow-sm">
                <h2 class="mb-4 text-xl font-bold">Total Saldo</h2>
                <p class="text-3xl font-bold text-green-600">
                    {{ formatCurrency(totalBalance) }}
                </p>
            </div>

            <!-- Current Wallets Grid -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                <div v-for="balance in balances" :key="balance.id" class="rounded-3xl bg-white p-4 shadow-md inset-shadow-sm">
                    <h3 class="font-bold">{{ balance.name }}</h3>
                    <p class="text-xl">
                        {{ formatCurrency(balance.current_amount, balance.currency) }}
                    </p>
                </div>
            </div>

            <!-- Add New Wallet -->
            <div class="rounded-3xl bg-white p-4 shadow-md inset-shadow-sm">
                <h2 class="mb-4 text-xl font-bold">Tambahkan Dompet Baru</h2>
                <form @submit.prevent="submitBalance" class="space-y-4">
                    <div>
                        <label class="mb-1 block">Beri nama wallet untuk sumber saldo Anda</label>
                        <input
                            type="text"
                            v-model="balanceForm.name"
                            class="w-full rounded border p-2"
                            placeholder="e.g. Cash, Bank BCA, etc"
                            required
                        />
                    </div>

                    <div>
                        <label class="mb-1 block">Jumlah awal <i>(jika ada pemasukan awal)</i></label>
                        <input
                            type="number"
                            v-model="balanceForm.initial_amount"
                            class="w-full rounded border p-2"
                            placeholder="bisa dikosongi"
                            min="0"
                            step="1"
                        />
                    </div>

                    <button
                        type="submit"
                        class="rounded bg-blue-500 px-4 py-2 text-white hover:bg-blue-600"
                        :disabled="balanceForm.processing || !balanceForm.name"
                    >
                        Tambah Dompet
                    </button>
                </form>
            </div>

            <!-- Add Custom Category -->
            <div class="rounded-3xl bg-white p-4 shadow-md inset-shadow-sm">
                <h2 class="mb-4 text-xl font-bold">Tambah Kategori</h2>
                <form @submit.prevent="submitCategory" class="space-y-4">
                    <div>
                        <label class="mb-1 block">Nama Kategori</label>
                        <input
                            type="text"
                            v-model="categoryForm.name"
                            class="w-full rounded border p-2"
                            placeholder="e.g. Office Supplies"
                            required
                        />
                    </div>

                    <div>
                        <label class="mb-1 block">Tipe</label>
                        <select v-model="categoryForm.type" class="w-full rounded border p-2" required>
                            <option value="income">Pemasukan</option>
                            <option value="expense">Pengeluaran</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block">Icon</label>
                            <input type="text" v-model="categoryForm.icon" class="w-full rounded border p-2" placeholder="e.g. 📝" />
                        </div>

                        <div>
                            <label class="mb-1 block">Warna</label>
                            <input type="color" v-model="categoryForm.color" class="h-10 w-full rounded border p-1" />
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="rounded bg-green-500 px-4 py-2 text-white hover:bg-green-600"
                        :disabled="categoryForm.processing || !categoryForm.name"
                    >
                        Tambah kategori
                    </button>
                </form>
            </div>

            <!-- Current Categories -->
            <div class="rounded-3xl bg-white p-4 shadow-md inset-shadow-sm">
                <h3 class="mb-2 font-bold">Kategori Saat Ini</h3>
                <div class="grid gap-2">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="flex items-center rounded bg-gray-50 p-2"
                        :style="{ borderLeftColor: category.color || '#000000', borderLeftWidth: '4px' }"
                    >
                        <span class="mr-2">{{ category.icon || '📝' }}</span>
                        <span>{{ category.name }}</span>
                        <span
                            class="ml-auto rounded px-2 py-1 text-sm"
                            :class="category.type === 'income' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                        >
                            {{ category.type }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
