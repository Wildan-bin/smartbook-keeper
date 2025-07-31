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
        onSuccess: () => form.reset()
    })
}

const handleBlur = () => {
    if (!form.description) {
        form.description = '-'
    }
}

const user = usePage().props.auth.user
</script>

<template>
    <MainLayout :user="user">
        <div class="ps-16 pe-8 py-6">
        <h1 class="absolute top-6 ml-2 text-5xl font-medium mb-6 mt-2 tracking-tight">Transaksi</h1>
        <!-- Total Balance Display -->
        <div class="mt-12 mb-3 p-6 bg-linear-to-r from-[#007abb] to-[#2FABEC] rounded-3xl shadow-md inset-shadow-sm">
            <h2 class="text-xl font-bold mb-3 text-white">Total Saldo</h2>
            <p class="text-4xl text-white font-medium tracking-tight">{{ totalBalance.toLocaleString('id-ID', {
                style: 'currency',
                currency: 'IDR'
            }) }}</p>
        </div>

        <!-- Transaction Form -->
        <form @submit.prevent="submit" class="mb-3 p-6 bg-white rounded-3xl shadow-md inset-shadow-sm">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1">Tipe</label>
                    <select v-model="form.type" class="w-full border rounded p-2">
                        <option value="income">Pemasukan</option>
                        <option value="expense">Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Dompet</label>
                    <select v-model="form.balance_id" class="w-full border rounded p-2">
                        <option v-for="balance in balances" :key="balance.id" :value="balance.id">
                            {{ balance.name }}
                        </option>
                    </select>
                </div>  

                <div>
                    <label class="block mb-1">Kategori</label>
                    <select v-model="form.category_id" class="w-full border rounded p-2">
                        <option v-for="category in filteredCategories" :key="category.id" :value="category.id">
                            {{ category.icon }} {{ category.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Jumlah</label>
                    <input type="number" v-model="form.amount" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block mb-1">Tanggal</label>
                    <input type="date" v-model="form.date" class="w-full border rounded p-2">
                </div>

                <div>
                    <label class="block mb-1">Deskripsi</label>
                    <input
                        type="text"
                        v-model="form.description"
                        class="w-full border rounded p-2"
                        @blur="handleBlur"
                        placeholder="Deskripsi (opsional)"
                    >
                </div>
            </div>

            <button type="submit" 
                class="mt-4 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                :disabled="form.processing">
                Simpan
            </button>
        </form>

        <!-- Transactions List -->
        <div class="bg-white p-6 rounded-3xl shadow-md inset-shadow-sm">
            <table class="w-full">
                <thead>
                    <tr>
                        <th class="p-2 text-left">Tanggal</th>
                        <th class="p-2 text-left">Tipe</th>
                        <th class="p-2 text-left">Kategori</th>
                        <th class="p-2 text-left">Jumlah</th>
                        <th class="p-2 text-left">Saldo</th>
                        <th class="p-2 text-left">Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="transaction in transactions.data" :key="transaction.id">
                        <td class="p-2">{{ new Date(transaction.date).toLocaleDateString() }}</td>
                        <td class="p-2">{{ transaction.type }}</td>
                        <td class="p-2">{{ transaction.category.name }}</td>
                        <td :class=" transaction.type === 'income' ? 'p-2 text-green-600' : 'p-2 text-red-600' ">{{ transaction.type === 'income' ? '+' : '-' }} {{ transaction.amount.toLocaleString('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }) }}</td>
                        <td class="p-2">{{ transaction.balance.name }}</td>
                        <td class="p-2">{{ transaction.description }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </MainLayout>
</template>