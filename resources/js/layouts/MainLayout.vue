<script setup>
import { Link, router } from '@inertiajs/vue3';
import { 
    LogOut, 
    Settings, 
    Home, 
    Receipt, 
    Wallet, 
    BarChart3, 
    Menu, 
    X,
    ChevronDown,
    User
} from 'lucide-vue-next';
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

// Navigation items with icons and metadata
const navigationItems = [
    {
        name: 'Beranda',
        route: 'dashboard',
        icon: Home,
        description: 'Dashboard utama',
        color: 'text-blue-400'
    },
    {
        name: 'Transaksi',
        route: 'transactions.index',
        icon: Receipt,
        description: 'Kelola transaksi',
        color: 'text-green-400'
    },
    {
        name: 'Saldo',
        route: 'balances.index',
        icon: Wallet,
        description: 'Kelola dompet',
        color: 'text-purple-400'
    },
    {
        name: 'Laporan',
        route: 'reports.index',
        icon: BarChart3,
        description: 'Laporan keuangan',
        color: 'text-orange-400',
        routePattern: 'reports.*'
    }
];

// Check if route is active
const isRouteActive = (routeName, routePattern = null) => {
    return routePattern ? route().current(routePattern) : route().current(routeName);
};
</script>

<template>
    <div class="relative min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50"> 
        <!-- Backdrop for mobile -->
        <div 
            v-if="isSidebarOpen" 
            @click="toggleSidebar"
            class="fixed inset-0 z-40 bg-black/20 backdrop-blur-sm lg:hidden"
        ></div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col bg-white/95 backdrop-blur-xl shadow-2xl transition-all duration-300 border-r border-white/20"
            :class=" [
                isSidebarOpen ? 'w-80' : 'w-28',
                isSidebarOpen ? 'rounded-r-3xl' : 'rounded-r-2xl'
            ]"
        >
            <!-- Header Section -->
            <div class="relative overflow-hidden">
                <!-- Background Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-[#007abb] via-[#1a8dd4] to-[#2FABEC]"></div>
                
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-4 right-4 w-20 h-20 rounded-full border-2 border-white/30"></div>
                    <div class="absolute bottom-2 right-8 w-12 h-12 rounded-full border-2 border-white/20"></div>
                </div>

                <div class="relative z-10 flex items-center justify-between p-5 border-b border-white/10">
                    <!-- Logo and Brand -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div v-if="isSidebarOpen" class="transition-all duration-300">
                            <h1 class="text-xl font-bold text-white leading-tight">Smart BookKeeper</h1>
                            <!-- <p class="text-xs text-white/80 font-medium">Keeper</p> -->
                        </div>
                    </div>

                    <!-- Toggle Button -->
                    <button 
                        @click="toggleSidebar" 
                        class="flex items-center justify-center ml-2 w-8 h-8 bg-white/20 backdrop-blur-sm rounded-lg hover:bg-white/30 transition-all duration-200 group"
                    >
                        <Menu v-if="!isSidebarOpen" class="w-4 h-4 text-white group-hover:scale-110 transition-transform" />
                        <X v-else class="w-4 h-4 text-white group-hover:scale-110 transition-transform" />
                    </button>
                </div>

                <!-- Welcome Message -->
                <div v-if="isSidebarOpen" class="relative z-10 px-6 py-4">
                    <div class="text-white/90 text-sm">
                        <p class="font-medium">Selamat datang kembali!</p>
                        <p class="text-xs text-white/70">{{ user.name.split(' ')[0] }}</p>
                    </div>
                </div>
            </div>

            <!-- Navigation Section -->
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                <div v-if="isSidebarOpen" class="px-3 py-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Menu Utama</p>
                </div>

                <!-- Navigation Links -->
                <template v-for="item in navigationItems" :key="item.route">
                    <Link
                        :href="route(item.route)"
                        class="group relative flex items-center rounded-2xl p-3 transition-all duration-200 hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 hover:shadow-sm"
                        :class=" [
                            isRouteActive(item.route, item.routePattern) 
                                ? 'bg-gradient-to-r from-[#007abb]/10 to-[#2FABEC]/10 text-[#007abb] shadow-sm border-l-4 border-[#007abb]' 
                                : 'text-gray-700 hover:text-[#007abb]'
                        ]"
                    >
                        <!-- Active Indicator -->
                        <div 
                            v-if="isRouteActive(item.route, item.routePattern)" 
                            class="absolute right-3 w-2 h-2 bg-[#007abb] rounded-full animate-pulse"
                        ></div>

                        <!-- Icon Container -->
                        <div class="relative flex items-center justify-center w-10 h-10 rounded-xl transition-all duration-200"
                             :class=" [
                                isRouteActive(item.route, item.routePattern) 
                                    ? 'bg-[#007abb]/15 text-[#007abb]' 
                                    : 'bg-gray-100 group-hover:bg-white group-hover:shadow-sm'
                             ]">
                            <component :is="item.icon" class="w-5 h-5 transition-transform group-hover:scale-110" />
                        </div>

                        <!-- Text Content -->
                        <div v-if="isSidebarOpen" class="ml-4 flex-1 transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-sm leading-tight">{{ item.name }}</p>
                                    <p class="text-xs text-gray-500 group-hover:text-gray-600 transition-colors">
                                        {{ item.description }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Hover Effect -->
                        <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-[#007abb]/5 to-[#2FABEC]/5 opacity-0 group-hover:opacity-100 transition-opacity duration-200"></div>
                    </Link>
                </template>

                <!-- Divider -->
                <div v-if="isSidebarOpen" class="my-4 border-t border-gray-200"></div>

                <!-- Quick Stats (when expanded) -->
                <div v-if="isSidebarOpen" class="px-3 py-4 bg-gradient-to-br from-gray-50 to-blue-50 rounded-2xl border border-gray-100">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-[#007abb]">🎯</div>
                        <p class="text-xs text-gray-600 mt-1">Kelola keuangan dengan mudah</p>
                    </div>
                </div>
            </nav>

            <!-- User Menu Section -->
            <div class="border-t border-gray-100 bg-white/80 backdrop-blur-sm">
                <div class="p-4">
                    <div class="relative">
                        <button 
                            @click="toggleUserMenu" 
                            class="group w-full flex items-center rounded-2xl p-3 text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-blue-50 transition-all duration-200 hover:shadow-sm"
                        >
                            <!-- Avatar -->
                            <div class="relative flex items-center justify-center w-10 h-10 bg-gradient-to-br from-[#007abb] to-[#2FABEC] rounded-xl shadow-sm">
                                <User class="w-5 h-5 text-white" />
                                <div class="absolute -bottom-1 -right-1 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
                            </div>

                            <!-- User Info -->
                            <div v-if="isSidebarOpen" class="ml-3 flex-1 text-left">
                                <p class="font-semibold text-sm text-gray-900 leading-tight">{{ user.name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                            </div>

                            <!-- Chevron -->
                            <ChevronDown 
                                v-if="isSidebarOpen" 
                                class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="{ 'rotate-180': isUserMenuOpen }"
                            />
                        </button>

                        <!-- Dropdown Menu -->
                        <div 
                            v-if="isUserMenuOpen" 
                            class="absolute bottom-full left-0 mb-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden animate-in slide-in-from-bottom-2 duration-200"
                        >
                            <div class="p-2 space-y-1">
                                <Link 
                                    :href="route('profile.edit')" 
                                    class="flex items-center w-full px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition-colors duration-150 group"
                                >
                                    <div class="flex items-center justify-center w-8 h-8 bg-gray-100 rounded-lg group-hover:bg-blue-100 transition-colors mr-3">
                                        <Settings class="w-4 h-4 text-gray-600 group-hover:text-blue-600" />
                                    </div>
                                    <div>
                                        <p class="font-medium">Pengaturan</p>
                                        <p class="text-xs text-gray-500">Kelola profil Anda</p>
                                    </div>
                                </Link>
                                
                                <div class="border-t border-gray-100 my-1"></div>
                                
                                <button 
                                    @click="handleLogout" 
                                    class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 rounded-xl transition-colors duration-150 group"
                                >
                                    <div class="flex items-center justify-center w-8 h-8 bg-red-100 rounded-lg group-hover:bg-red-200 transition-colors mr-3">
                                        <LogOut class="w-4 h-4 text-red-600" />
                                    </div>
                                    <div class="text-left">
                                        <p class="font-medium">Keluar</p>
                                        <p class="text-xs text-red-500">Akhiri sesi Anda</p>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="transition-all duration-300" :class="{ 'ml-72': isSidebarOpen, 'ml-20': !isSidebarOpen }">
            <main class="min-h-screen">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
/* Custom animations */
@keyframes slide-in-from-bottom {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-in {
    animation-fill-mode: both;
}

.slide-in-from-bottom-2 {
    animation: slide-in-from-bottom 0.2s ease-out;
}

/* Custom scrollbar */
nav::-webkit-scrollbar {
    width: 4px;
}

nav::-webkit-scrollbar-track {
    background: transparent;
}

nav::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #007abb, #2FABEC);
    border-radius: 10px;
}

nav::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #006699, #1a8dd4);
}

/* Enhanced hover effects */
.group:hover {
    transform: translateX(2px);
}

/* Active link glow effect */
.border-l-4 {
    box-shadow: 0 0 0 1px rgba(0, 122, 187, 0.1);
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(16px)) {
    .backdrop-blur-xl {
        backdrop-filter: blur(16px);
    }
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
}

/* Mobile responsiveness */
@media (max-width: 1024px) {
    aside {
        transform: translateX(-100%);
    }
    
    aside.w-80 {
        transform: translateX(0);
    }
}
</style>
