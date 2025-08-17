<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Eye, EyeOff, Mail, Lock, User, BookOpen } from 'lucide-vue-next';
import { ref } from 'vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const showPassword = ref(false);
const showPasswordConfirmation = ref(false);

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};

const togglePasswordConfirmationVisibility = () => {
    showPasswordConfirmation.value = !showPasswordConfirmation.value;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center justify-center p-4">
        <Head title="Daftar - SmartBook Keeper" />
        
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        
        <!-- Register Container -->
        <div class="relative w-full max-w-md">
            <!-- Main Card -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-600 to-green-600 p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-lg rounded-2xl mb-4">
                        <BookOpen class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">SmartBook Keeper</h1>
                    <p class="text-blue-100 text-sm">Mulai perjalanan keuangan Anda</p>
                </div>

                <!-- Form Section -->
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Buat Akun Baru</h2>
                        <p class="text-gray-600 text-sm">Lengkapi data di bawah untuk membuat akun</p>
                    </div>

                    <!-- Register Form -->
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name Field -->
                        <div class="space-y-2">
                            <Label for="name" class="text-sm font-medium text-gray-700">Nama Lengkap</Label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <User class="h-5 w-5 text-gray-400" />
                                </div>
                                <Input
                                    id="name"
                                    type="text"
                                    required
                                    autofocus
                                    :tabindex="1"
                                    autocomplete="name"
                                    v-model="form.name"
                                    placeholder="Masukkan nama lengkap"
                                    class="pl-10 h-12 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 focus:bg-white transition-all duration-200"
                                />
                            </div>
                            <InputError :message="form.errors.name" />
                        </div>

                        <!-- Email Field -->
                        <div class="space-y-2">
                            <Label for="email" class="text-sm font-medium text-gray-700">Email</Label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Mail class="h-5 w-5 text-gray-400" />
                                </div>
                                <Input
                                    id="email"
                                    type="email"
                                    required
                                    :tabindex="2"
                                    autocomplete="email"
                                    v-model="form.email"
                                    placeholder="nama@email.com"
                                    class="pl-10 h-12 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 focus:bg-white transition-all duration-200"
                                />
                            </div>
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Password Field -->
                        <div class="space-y-2">
                            <Label for="password" class="text-sm font-medium text-gray-700">Password</Label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Lock class="h-5 w-5 text-gray-400" />
                                </div>
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    :tabindex="3"
                                    autocomplete="new-password"
                                    v-model="form.password"
                                    placeholder="Masukkan password"
                                    class="pl-10 pr-10 h-12 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 focus:bg-white transition-all duration-200"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                >
                                    <Eye v-if="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" />
                                    <EyeOff v-else class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" />
                                </button>
                            </div>
                            <InputError :message="form.errors.password" />
                        </div>

                        <!-- Password Confirmation Field -->
                        <div class="space-y-2">
                            <Label for="password_confirmation" class="text-sm font-medium text-gray-700">Konfirmasi Password</Label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Lock class="h-5 w-5 text-gray-400" />
                                </div>
                                <Input
                                    id="password_confirmation"
                                    :type="showPasswordConfirmation ? 'text' : 'password'"
                                    required
                                    :tabindex="4"
                                    autocomplete="new-password"
                                    v-model="form.password_confirmation"
                                    placeholder="Konfirmasi password"
                                    class="pl-10 pr-10 h-12 rounded-xl border-gray-200 focus:border-blue-500 focus:ring-blue-500 bg-gray-50 focus:bg-white transition-all duration-200"
                                />
                                <button
                                    type="button"
                                    @click="togglePasswordConfirmationVisibility"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                >
                                    <Eye v-if="!showPasswordConfirmation" class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" />
                                    <EyeOff v-else class="h-5 w-5 text-gray-400 hover:text-gray-600 transition-colors" />
                                </button>
                            </div>
                            <InputError :message="form.errors.password_confirmation" />
                        </div>

                        <!-- Submit Button -->
                        <Button 
                            type="submit" 
                            :tabindex="5" 
                            :disabled="form.processing"
                            class="w-full h-12 bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin mr-2" />
                            <span v-if="!form.processing">Buat Akun</span>
                            <span v-else>Memproses...</span>
                        </Button>
                    </form>

                    <!-- Login Link -->
                    <div class="mt-8 text-center">
                        <p class="text-gray-600 text-sm">
                            Sudah punya akun?
                            <TextLink 
                                :href="route('login')" 
                                :tabindex="6"
                                class="font-semibold text-blue-600 hover:text-blue-500 ml-1"
                            >
                                Masuk sekarang
                            </TextLink>
                        </p>
                    </div>

                    <!-- Terms and Privacy -->
                    <div class="mt-6 text-center">
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Dengan mendaftar, Anda menyetujui 
                            <span class="text-blue-600 hover:text-blue-500 cursor-pointer">Syarat & Ketentuan</span> 
                            dan 
                            <span class="text-blue-600 hover:text-blue-500 cursor-pointer">Kebijakan Privasi</span> 
                            kami.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    © 2025 SmartBook Keeper. Semua hak dilindungi.
                </p>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="absolute top-10 left-10 w-20 h-20 bg-blue-200 rounded-full opacity-20 animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-16 h-16 bg-green-200 rounded-full opacity-20 animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-5 w-12 h-12 bg-purple-200 rounded-full opacity-20 animate-bounce delay-500"></div>
        <div class="absolute top-1/3 right-1/4 w-8 h-8 bg-yellow-200 rounded-full opacity-20 animate-pulse delay-2000"></div>
    </div>
</template>

<style scoped>
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-10px);
    }
}

.bg-grid-pattern {
    background-image: 
        linear-gradient(rgba(0,0,0,0.1) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0,0,0,0.1) 1px, transparent 1px);
    background-size: 20px 20px;
}

/* Custom animations */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.slide-in-up {
    animation: slideInUp 0.6s ease-out;
}

/* Enhance focus states */
.focus-enhanced:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    border-color: #3b82f6;
}

/* Background gradient animation */
@keyframes gradientShift {
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

.animated-gradient {
    background: linear-gradient(-45deg, #ee7724, #d8363a, #dd3675, #b44593);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
}

/* Input field enhancements */
input:focus {
    transform: translateY(-1px);
}

/* Button hover effects */
button:hover {
    transform: translateY(-1px);
}

/* Card hover effect */
.bg-white\/80:hover {
    transform: translateY(-2px);
}
</style>
