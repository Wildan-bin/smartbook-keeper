<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, TrendingUp, PieChart, Calculator, Users } from 'lucide-vue-next';

defineProps<{
    auth: {
        user?: any;
    };
}>();
</script>

<template>
    <Head title="Smart BookKeeper - Aplikasi Keuangan UMKM">
        <meta name="description" content="Aplikasi Smart BookKeeper untuk mengelola keuangan UMKM dengan mudah dan efisien" />
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>

    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
        <!-- Navigation Header -->
        <header class="relative z-10">
            <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8">
                <!-- Logo Section -->
                <div class="flex items-center gap-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-br from-[#007abb] to-[#2FABEC] rounded-2xl shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Smart BookKeeper</h1>
                        <p class="text-sm text-gray-600">Aplikasi Keuangan UMKM</p>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="flex items-center gap-4">
                    <Link
                        v-if="$page.props.auth.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                    >
                        <span>Dashboard</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="px-5 py-2.5 text-sm font-medium text-gray-700 hover:text-[#007abb] transition-colors duration-200"
                        >
                            Masuk
                        </Link>
                        <Link
                            :href="route('register')"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
                        >
                            <span>Daftar</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </template>
                </div>
            </nav>
        </header>

        <!-- Main Content -->
        <main class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Hero Section -->
            <div class="relative isolate pt-14 pb-20">
                <!-- Background Pattern -->
                <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80">
                    <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#007abb] to-[#2FABEC] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]"></div>
                </div>

                <div class="mx-auto max-w-6xl text-center">
                    <!-- Logo Universities Section -->
                    <div class="mb-12 flex items-center justify-center gap-8">
                        <div class="group">
                            <img 
                                src="/images/unmer-smartbookkeeper.png" 
                                alt="Universitas Merdeka Malang" 
                                class="h-20 w-auto object-contain transition-transform duration-200 group-hover:scale-105"
                            />
                        </div>
                        <div class="w-px h-16 bg-gradient-to-b from-transparent via-gray-300 to-transparent"></div>
                        <div class="group">
                            <img 
                                src="/images/diktisainter-smartbookkeeper.png" 
                                alt="Pengabdian Kepada Masyarakat" 
                                class="h-20 w-auto object-contain transition-transform duration-200 group-hover:scale-105"
                            />
                        </div>
                    </div>

                    <!-- Main Title -->
                    <div class="mb-8">
                        <h1 class="text-6xl font-bold tracking-tight text-gray-900 sm:text-7xl mb-4">
                            <span class="bg-gradient-to-r from-[#007abb] to-[#2FABEC] bg-clip-text text-transparent">
                                Smart BookKeeper
                            </span>
                        </h1>
                        <p class="text-2xl leading-8 text-gray-700 font-medium mb-4">
                            Aplikasi untuk mengetahui kinerja keuangan UMKM
                        </p>
                        <div class="w-32 h-1 bg-gradient-to-r from-[#007abb] to-[#2FABEC] mx-auto rounded-full"></div>
                    </div>

                    <!-- Description -->
                    <div class="mb-12 max-w-4xl mx-auto">
                        <p class="text-lg leading-8 text-gray-600 mb-8">
                            Aplikasi Smart BookKeeper yang berisi catatan keuangan UMKM untuk membantu mengetahui jumlah yang masuk dan keluar sehingga hasil akhir menunjukkan jumlah omset dan laporan keuangan.
                        </p>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-12">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white font-semibold rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 text-lg"
                            >
                                <TrendingUp class="w-6 h-6" />
                                <span>Go to Your Dashboard</span>
                                <ArrowRight class="w-5 h-5" />
                            </Link>
                            <template v-else>
                                <Link
                                    :href="route('register')"
                                    class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-[#007abb] to-[#2FABEC] text-white font-semibold rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:scale-105 text-lg"
                                >
                                    <TrendingUp class="w-6 h-6" />
                                    <span>Mulai Sekarang</span>
                                    <ArrowRight class="w-5 h-5" />
                                </Link>
                                <Link
                                    :href="route('login')"
                                    class="inline-flex items-center gap-3 px-8 py-4 bg-white text-[#007abb] font-semibold rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 text-lg border-2 border-[#007abb]/20 hover:border-[#007abb]/40"
                                >
                                    <span>Sudah Punya Akun?</span>
                                </Link>
                            </template>
                        </div>
                    </div>

                    <!-- Features Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                        <!-- Feature 1 -->
                        <div class="group relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 border border-white/20">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative">
                                <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-[#007abb] to-[#2FABEC] rounded-2xl mb-6 mx-auto shadow-lg">
                                    <Calculator class="w-8 h-8 text-white" />
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-4">Catatan Keuangan</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    Catat setiap pemasukan dan pengeluaran dengan mudah dan terorganisir
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="group relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 border border-white/20">
                            <div class="absolute inset-0 bg-gradient-to-br from-green-50 to-emerald-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative">
                                <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-500 rounded-2xl mb-6 mx-auto shadow-lg">
                                    <TrendingUp class="w-8 h-8 text-white" />
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-4">Analisis Omset</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    Pantau perkembangan omset dan kinerja keuangan bisnis Anda
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="group relative bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 border border-white/20">
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-50 to-pink-50 rounded-3xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            <div class="relative">
                                <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-purple-500 to-pink-500 rounded-2xl mb-6 mx-auto shadow-lg">
                                    <PieChart class="w-8 h-8 text-white" />
                                </div>
                                <h3 class="text-xl font-bold text-gray-900 mb-4">Laporan Keuangan</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    Dapatkan laporan keuangan yang detail dan mudah dipahami
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Background Pattern Bottom -->
                <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]">
                    <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#2FABEC] to-[#007abb] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"></div>
                </div>
            </div>

            <!-- Footer Section -->
            <footer class="border-t border-gray-200/50 bg-white/30 backdrop-blur-sm">
                <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
                    <div class="text-center">
                        <!-- Institution Info -->
                        <div class="mb-8">
                            <div class="flex items-center justify-center gap-2 mb-4">
                                <Users class="w-5 h-5 text-[#007abb]" />
                                <span class="text-lg font-semibold text-gray-900">Pengabdian Kepada Masyarakat DPPM 2025</span>
                            </div>
                            <p class="text-lg text-[#007abb] font-medium">Universitas Merdeka Malang</p>
                        </div>

                        <!-- Copyright -->
                        <div class="pt-8 border-t border-gray-200/50">
                            <p class="text-sm text-gray-500">
                                © 2025 Smart BookKeeper. Dikembangkan untuk UMKM Indonesia.
                            </p>
                        </div>
                    </div>
                </div>
            </footer>
        </main>

        <!-- Floating Elements -->
        <div class="fixed top-20 left-10 w-20 h-20 bg-blue-200/30 rounded-full animate-pulse opacity-40"></div>
        <div class="fixed bottom-20 right-10 w-16 h-16 bg-green-200/30 rounded-full animate-pulse delay-1000 opacity-40"></div>
        <div class="fixed top-1/2 right-5 w-12 h-12 bg-purple-200/30 rounded-full animate-bounce delay-500 opacity-40"></div>
    </div>
</template>

<style scoped>
/* Custom animations */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.float-animation {
    animation: float 6s ease-in-out infinite;
}

/* Gradient text animation */
@keyframes gradient-shift {
    0% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
    100% {
        background-position: 0% 50%;
    }
}

.animate-gradient {
    background: linear-gradient(-45deg, #007abb, #2FABEC, #007abb, #2FABEC);
    background-size: 400% 400%;
    animation: gradient-shift 15s ease infinite;
}

/* Enhanced hover effects */
.group:hover {
    transform: translateY(-2px);
}

/* Backdrop blur support */
@supports (backdrop-filter: blur(16px)) {
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .text-6xl {
        font-size: 3rem;
    }
    .text-7xl {
        font-size: 4rem;
    }
}
</style>