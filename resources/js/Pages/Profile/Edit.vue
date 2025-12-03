<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        <!-- BACKGROUND WRAPPER -->
        <div class="relative min-h-[calc(100vh-65px)] w-full bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 overflow-x-hidden flex flex-col items-center">
            
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden z-0 pointer-events-none fixed">
                <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-gradient-to-br from-blue-400/30 via-blue-300/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute top-[30%] -right-[15%] w-[400px] h-[400px] bg-gradient-to-bl from-purple-400/25 via-pink-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
                <div class="absolute -bottom-[15%] left-[15%] w-[450px] h-[450px] bg-gradient-to-tr from-indigo-400/20 via-blue-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
                <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
            </div>

            <!-- MAIN CONTENT CONTAINER -->
            <div class="relative z-10 w-full max-w-4xl px-4 py-8 md:py-12 space-y-8">
                <!-- 1. FORM EDIT INFO -->
                <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl rounded-3xl shadow-xl border border-white/20 dark:border-gray-700/50 p-6 md:p-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-blue-500 to-purple-500"></div>
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <!-- 2. FORM GANTI PASSWORD -->
                <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl rounded-3xl shadow-xl border border-white/20 dark:border-gray-700/50 p-6 md:p-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-purple-500 to-pink-500"></div>
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <!-- 3. FORM HAPUS AKUN -->
                <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl rounded-3xl shadow-xl border border-white/20 dark:border-gray-700/50 p-6 md:p-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-red-500 to-orange-500"></div>
                    <DeleteUserForm class="max-w-xl" />
                </div>

                <!-- 4. Logout Mobile -->
                <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl rounded-3xl shadow-xl border border-white/20 dark:border-gray-700/50 p-6 md:p-8 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-red-500 to-orange-500"></div>

                    <div class="flex flex-wrap gap-3">
                        <div class="rounded-2xl">
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </DropdownLink>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>