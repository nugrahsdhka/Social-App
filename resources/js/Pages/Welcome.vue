<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 flex flex-col items-center justify-center relative overflow-hidden">
        
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden z-0 pointer-events-none">
            <!-- Gradient Orbs with Animation -->
            <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-gradient-to-br from-blue-400/30 via-blue-300/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-[30%] -right-[15%] w-[400px] h-[400px] bg-gradient-to-bl from-purple-400/25 via-pink-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute -bottom-[15%] left-[15%] w-[450px] h-[450px] bg-gradient-to-tr from-indigo-400/20 via-blue-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
            
            <!-- Grid Pattern Overlay -->
            <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
        </div>

        <!-- Main Content -->
        <div class="relative z-10 w-full max-w-3xl px-6 text-center">
            
            <!-- Logo with Floating Animation -->
            <div class="mb-10 flex justify-center">
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur-xl opacity-60 group-hover:opacity-100 transition duration-500 animate-pulse"></div>
                    <div class="relative w-24 h-24 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 rounded-3xl flex items-center justify-center shadow-2xl transform hover:rotate-6 hover:scale-110 transition duration-300">
                        <span class="text-3xl animate-bounce" style="animation-duration: 2s">
                            <img 
                                src="/img/logo.png" 
                                alt="Logo 76 Apel" 
                                class="w-16 h-16 object-contain animate-bounce" 
                                style="animation-duration: 2s"
                            />
                        </span>
                    </div>
                </div>
            </div>

            <!-- Title with Gradient Animation -->
            <div class="mb-6">
                <h1 class="text-5xl md:text-7xl font-black tracking-tight text-gray-900 dark:text-white mb-3 leading-tight">
                    Selamat Datang di
                </h1>
                <div class="relative inline-block">
                    <h2 class="text-6xl md:text-8xl font-black tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-500 animate-gradient pb-2">
                        76 Apel
                    </h2>
                    <div class="absolute -bottom-2 left-0 right-0 h-1 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-500 rounded-full"></div>
                </div>
            </div>

            <!-- Action Buttons with Hover Effects -->
            <div v-if="canLogin" class="flex flex-col sm:flex-row gap-5 justify-center items-center mb-8">
                <template v-if="$page.props.auth.user">
                    <Link
                        :href="route('dashboard')"
                        class="group relative px-10 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:shadow-purple-500/40 transition-all duration-300 transform hover:-translate-y-2 overflow-hidden"
                    >
                        <span class="relative z-10">Masuk ke Dashboard</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    </Link>
                </template>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="group relative w-full sm:w-auto px-10 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 text-center overflow-hidden"
                    >
                        <span class="relative z-10">Masuk</span>
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="group w-full sm:w-auto px-10 py-4 bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm text-gray-900 dark:text-white border-2 border-gray-200 dark:border-gray-700 font-bold rounded-2xl shadow-lg hover:shadow-xl hover:border-purple-400 dark:hover:border-purple-600 transition-all duration-300 transform hover:-translate-y-2 text-center"
                    >
                        Daftar Sekarang
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes gradient {
    0%, 100% {
        background-position: 0% 50%;
    }
    50% {
        background-position: 100% 50%;
    }
}

.animate-gradient {
    background-size: 200% 200%;
    animation: gradient 3s ease infinite;
}
</style>