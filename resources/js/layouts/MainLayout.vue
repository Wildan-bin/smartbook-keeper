<script setup>
import { Link, router } from '@inertiajs/vue3';
import { LogOut, Settings } from 'lucide-vue-next';
import { ref } from 'vue';

const isSidebarOpen = ref(true);
const isUserMenuOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
};

const handleLogout = () => {
    router.post(route('logout'));
};

defineProps({
    user: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="relative min-h-screen bg-[#F9FAFC]"> 
        <!-- Sidebar - Updated transform and width handling -->
        <aside
            class="fixed inset-y-0 left-0 z-50 shadow-lg transition-all duration-300 "
            :class=" [
                isSidebarOpen ? 'w-72' : 'w-20',
                'bg-gradient-to-b from-[#007abb] to-[#2FABEC]'
            ], [isSidebarOpen ? 'rounded-r-3xl' : 'rounded-r-md']"

            
        >
            <!-- Logo and Brand - Updated for collapsed state -->
            <div class="flex items-center justify-between border-b border-white/10 px-4 py-4">
                <h1 class="text-xl font-bold text-white" :class="{ 'hidden': !isSidebarOpen }">
                    Smartbook Keeper
                </h1>
                <button @click="toggleSidebar" class="rounded-lg hover:bg-[#0088d1] p-2">
                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            :d="isSidebarOpen ? 'M6 18L18 6M6 6l12 12' : 'M4 6h16M4 12h16M4 18h16'" />
                    </svg>
                </button>
            </div>

            <!-- Navigation Links - Updated for collapsed state -->
            <nav class="space-y-2 p-4 text-sm">
                <Link
                    :href="route('dashboard')"
                    class="flex items-center rounded-lg p-2 text-white transition-colors hover:bg-[#0088d1]"
                    :class="{ 'bg-[#0088d1]': route().current('dashboard') }"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"
                        />
                    </svg>
                    <span :class="{ 'hidden': !isSidebarOpen }" class="ml-3">Beranda</span>
                </Link>
                
                <Link
                    :href="route('transactions.index')"
                    class="flex items-center rounded-lg p-2 text-white transition-colors hover:bg-[#0088d1]"
                    :class="{ 'bg-[#0088d1]': route().current('transactions.index') }"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                        />
                    </svg>
                    <span :class="{ 'hidden': !isSidebarOpen }" class="ml-3">Transaksi</span>
                </Link>

                <Link
                    :href="route('balances.index')"
                    class="flex items-center rounded-lg p-2 text-white transition-colors hover:bg-[#0088d1]"
                    :class="{ 'bg-[#0088d1]': route().current('balances.index') }"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"
                        />
                    </svg>
                    <span :class="{ 'hidden': !isSidebarOpen }" class="ml-3">Saldo</span>
                </Link>
            </nav>

            <!-- User Menu - Updated for collapsed state -->
            <div class="absolute bottom-0 left-0 w-full border-t border-white/10">
                <div class="p-4">
                    <div class="relative">
                        <button @click="toggleUserMenu" 
                            class="flex w-full items-center justify-center rounded-lg p-2 text-white hover:bg-[#0088d1]"
                        >
                            <div class="flex h-8 w-8 items-center justify-center rounded-full bg-white/20">
                                {{ user.name.charAt(0) }}
                            </div>
                            <div v-if="isSidebarOpen" class="ml-3 flex-1">
                                <div class="text-sm">
                                    <p class="font-medium text-white">{{ user.name }}</p>
                                    <p class="text-white/70">{{ user.email }}</p>
                                </div>
                                <svg class="h-5 w-5 text-white/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        <!-- Dropdown Menu - Kept light theme for contrast -->
                        <div v-if="isUserMenuOpen" class="absolute bottom-full left-0 mb-1 w-full rounded-lg bg-white py-1 shadow-lg">
                            <Link :href="route('profile.edit')" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <Settings class="mr-3 h-4 w-4" />
                                Settings
                            </Link>
                            <button @click="handleLogout" class="flex w-full items-center px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <LogOut class="mr-3 h-4 w-4" />
                                Log out
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content - Updated margin -->
        <div class="transition-all duration-300" :class="{ 'ml-64': isSidebarOpen, 'ml-12': !isSidebarOpen }">
            <!-- Remove the toggle button since it's now in the sidebar -->
            <main class="min-h-screen pt-4">
                <slot />
            </main>
        </div>
    </div>
</template>
