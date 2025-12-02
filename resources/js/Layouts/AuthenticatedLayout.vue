<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();
const currentUser = page.props.auth.user;

// --- LOGIC NOTIFIKASI ---
const showToast = ref(false);
const toastMessage = ref('');
const toastTitle = ref('');

const triggerNotification = (title, message) => {
    toastTitle.value = title;
    toastMessage.value = message;
    showToast.value = true;
    
    // Hilangkan otomatis setelah 3 detik
    setTimeout(() => {
        showToast.value = false;
    }, 3000);
};

onMounted(() => {
    if (currentUser) {
        if (window.Echo) {
             window.Echo.private(`chat.${currentUser.id}`)
                .listen('MessageSent', (e) => {
                    triggerNotification(`Pesan Baru dari ${e.message.sender.name}`, e.message.message);
                })
                .listen('UserFollowed', (e) => {
                    triggerNotification('Pengikut Baru', `${e.follower_name} mulai mengikuti Anda!`);
                });
        }
    }
});
</script>

<template>
    <div>
        <!-- Tambahkan pb-16 untuk mobile agar konten tidak tertutup navigasi bawah -->
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900 pb-16 sm:pb-0">
            
            <!-- Toast Notification -->
            <transition 
                enter-active-class="transform ease-out duration-300 transition" 
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2" 
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-100" 
                leave-from-class="opacity-100" 
                leave-to-class="opacity-0">
                <div v-if="showToast" class="fixed top-5 right-5 z-50 max-w-sm w-full bg-white dark:bg-gray-800 shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                                </svg>
                            </div>
                            <div class="ml-3 w-0 flex-1 pt-0.5">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ toastTitle }}</p>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ toastMessage }}</p>
                            </div>
                            <div class="ml-4 flex flex-shrink-0">
                                <button @click="showToast = false" class="inline-flex rounded-md bg-white dark:bg-gray-800 text-gray-400 hover:text-gray-500 focus:outline-none">
                                    <span class="sr-only">Close</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>

            <!-- TOP NAVIGATION (Desktop Only for links, Logo only for mobile) -->
            <nav class="hidden sm:block bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="shrink-0 flex items-center">
                                <img 
                                    src="/img/logo.png" 
                                    alt="Logo 76 Apel" 
                                    class="w-9 h-9 object-contain" 
                                />
                            </div>

                            <!-- Desktop Nav Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('chat.index')" :active="route().current('chat.index')">
                                    Chat
                                </NavLink>
                                
                                <NavLink 
                                    :href="route('profile.show', $page.props.auth.user.username)" 
                                    :active="route().current('profile.show') && $page.url.includes($page.props.auth.user.username)">
                                    My Profile
                                </NavLink>
                            </div>
                        </div>

                        <!-- Desktop Settings Dropdown -->
                        <div class="hidden sm:flex sm:items-center sm:ms-6">
                            <div class="ms-3 relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')"> Settings </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Content -->
            <main>
                <slot />
            </main>

            <!-- BOTTOM NAVIGATION BAR (Mobile Only) -->
            <div class="fixed bottom-0 left-0 w-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-t border-gray-200 dark:border-gray-800 sm:hidden z-50">
                <div class="flex justify-around items-center h-16">
                    
                    <!-- Chat -->
                    <Link 
                        :href="route('chat.index')" 
                        :class="route().current('chat.index') ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        class="p-2 flex flex-col items-center justify-center w-full h-full"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </Link>

                    <!-- Profile -->
                    <Link 
                        :href="route('profile.show', $page.props.auth.user.username)"
                        :class="route().current('profile.show') && $page.url.includes($page.props.auth.user.username) ? 'text-blue-600 dark:text-blue-400' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        class="p-2 flex flex-col items-center justify-center w-full h-full"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </Link>
                </div>
            </div>

        </div>
    </div>
</template>