<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    username: '', // LOGIKA TETAP SAMA (USERNAME)
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 flex flex-col items-center justify-center relative overflow-hidden">
        
        <div class="absolute inset-0 overflow-hidden z-0 pointer-events-none">
            <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-gradient-to-br from-blue-400/30 via-blue-300/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
            <div class="absolute top-[30%] -right-[15%] w-[400px] h-[400px] bg-gradient-to-bl from-purple-400/25 via-pink-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute -bottom-[15%] left-[15%] w-[450px] h-[450px] bg-gradient-to-tr from-indigo-400/20 via-blue-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
            <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
        </div>

        <div class="relative z-10 w-full max-w-md px-6">
            
            <div class="mb-8 flex justify-center">
                <div class="relative group">
                    <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-3xl blur-xl opacity-60 group-hover:opacity-100 transition duration-500 animate-pulse"></div>
                    <div class="relative w-20 h-20 bg-gradient-to-br from-blue-600 via-purple-600 to-pink-500 rounded-2xl flex items-center justify-center shadow-2xl transform hover:rotate-6 transition duration-300">
                         <img src="/img/logo.png" alt="Logo 76 Apel" class="w-12 h-12 object-contain animate-bounce" style="animation-duration: 2s" />
                    </div>
                </div>
            </div>

            <div class="bg-white/80 dark:bg-gray-900/60 backdrop-blur-xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 p-8 overflow-hidden relative">
                
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-600 via-purple-600 to-pink-500"></div>

                <h2 class="text-3xl font-black text-center text-gray-900 dark:text-white mb-6 tracking-tight">
                    Masuk Akun
                </h2>

                <div v-if="status" class="mb-4 font-medium text-sm text-green-600 bg-green-100 dark:bg-green-900/30 p-3 rounded-lg text-center">
                    {{ status }}
                </div>

                <form @submit.prevent="submit">
                    <div>
                        <InputLabel for="username" value="Username" class="dark:text-gray-300" />
                        <TextInput
                            id="username"
                            type="text"
                            class="mt-1 block w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-gray-300 dark:border-gray-600 focus:border-purple-500 focus:ring-purple-500 rounded-xl transition-all"
                            v-model="form.username"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="Masukkan username anda"
                        />
                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <div class="mt-4">
                        <InputLabel for="password" value="Password" class="dark:text-gray-300" />
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border-gray-300 dark:border-gray-600 focus:border-purple-500 focus:ring-purple-500 rounded-xl transition-all"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="block mt-4">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" class="text-purple-600 focus:ring-purple-500 rounded" />
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-300">Remember me</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-between mt-6">
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-purple-600 dark:hover:text-purple-400 rounded-md focus:outline-none transition-colors"
                        >
                            Lupa Password?
                        </Link>

                        <button 
                            class="relative group px-6 py-2.5 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:shadow-purple-500/30 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="{ 'opacity-75': form.processing }" 
                            :disabled="form.processing"
                        >
                            <span class="relative z-10">Log in</span>
                            <div class="absolute inset-0 bg-gradient-to-r from-purple-600 to-pink-600 opacity-0 group-hover:opacity-100 transition duration-300"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Animasi tambahan jika diperlukan, sisa animasi menggunakan utility Tailwind */
</style>