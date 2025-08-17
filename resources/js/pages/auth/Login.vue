<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle, Eye, EyeOff, Mail, Lock, BookOpen } from 'lucide-vue-next';
import { ref } from 'vue';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50 flex items-center justify-center p-4">
        <Head title="Masuk - SmartBook Keeper" />
        
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-grid-pattern opacity-5"></div>
        
        <!-- Login Container -->
        <div class="relative w-full max-w-md">
            <!-- Main Card -->
            <div class="bg-white/80 backdrop-blur-lg rounded-3xl shadow-2xl border border-white/20 overflow-hidden">
                <!-- Header Section -->
                <div class="bg-gradient-to-r from-blue-600 to-green-600 p-8 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-lg rounded-2xl mb-4">
                        <BookOpen class="h-8 w-8 text-white" />
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">SmartBook Keeper</h1>
                    <p class="text-blue-100 text-sm">Kelola keuangan Anda dengan mudah</p>
                </div>

                <!-- Form Section -->
                <div class="p-8">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-semibold text-gray-800 mb-2">Masuk ke Akun Anda</h2>
                        <p class="text-gray-600 text-sm">Masukkan email dan password untuk melanjutkan</p>
                    </div>

                    <!-- Status Message -->
                    <div v-if="status" class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-center">
                        <p class="text-sm font-medium text-green-600">{{ status }}</p>
                    </div>

                    <!-- Login Form -->
                    <form @submit.prevent="submit" class="space-y-6">
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
                                    autofocus
                                    :tabindex="1"
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
                            <div class="flex items-center justify-between">
                                <Label for="password" class="text-sm font-medium text-gray-700">Password</Label>
                                <TextLink 
                                    v-if="canResetPassword" 
                                    :href="route('password.request')" 
                                    class="text-sm text-blue-600 hover:text-blue-500 font-medium"
                                    :tabindex="5"
                                >
                                    Lupa password?
                                </TextLink>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <Lock class="h-5 w-5 text-gray-400" />
                                </div>
                                <Input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    :tabindex="2"
                                    autocomplete="current-password"
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

                        <!-- Remember Me -->
                        <div class="flex items-center">
                            <div class="flex items-center space-x-3">
                                <Checkbox 
                                    id="remember" 
                                    v-model="form.remember" 
                                    :tabindex="3"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                />
                                <Label for="remember" class="text-sm text-gray-600 cursor-pointer">
                                    Ingat saya
                                </Label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <Button 
                            type="submit" 
                            :tabindex="4" 
                            :disabled="form.processing"
                            class="w-full h-12 bg-gradient-to-r from-blue-600 to-green-600 hover:from-blue-700 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-[1.02] disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none"
                        >
                            <LoaderCircle v-if="form.processing" class="h-5 w-5 animate-spin mr-2" />
                            <span v-if="!form.processing">Masuk</span>
                            <span v-else>Memproses...</span>
                        </Button>
                    </form>

                    <!-- Register Link -->
                    <div class="mt-8 text-center">
                        <p class="text-gray-600 text-sm">
                            Belum punya akun?
                            <TextLink 
                                :href="route('register')" 
                                :tabindex="6"
                                class="font-semibold text-blue-600 hover:text-blue-500 ml-1"
                            >
                                Daftar sekarang
                            </TextLink>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-xs text-gray-500">
                    © 2024 SmartBook Keeper. Semua hak dilindungi.
                </p>
            </div>
        </div>

        <!-- Floating Elements -->
        <div class="absolute top-10 left-10 w-20 h-20 bg-blue-200 rounded-full opacity-20 animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-16 h-16 bg-green-200 rounded-full opacity-20 animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-5 w-12 h-12 bg-purple-200 rounded-full opacity-20 animate-bounce delay-500"></div>
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
</style>
