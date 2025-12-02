<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import axios from 'axios';

// Ambil user login dari props global Inertia
const page = usePage();
const currentUser = computed(() => page.props.auth.user);

// --- LOGIC PENCARIAN USER (SAMA RASA DENGAN PROFILE) ---
const searchQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);

const performSearch = async () => {
    if (searchQuery.value.length < 1) {
        searchResults.value = [];
        return;
    }

    isSearching.value = true;
    try {
        const response = await axios.get(`/chat/search?query=${searchQuery.value}`);
        searchResults.value = response.data;
    } catch (error) {
        console.error('Search error', error);
    } finally {
        isSearching.value = false;
    }
};

const visitProfile = (username) => {
    searchQuery.value = '';
    searchResults.value = [];
    router.visit(route('profile.show', username));
};
</script>

<template>
    <Head title="Home" />

    <AuthenticatedLayout>
        <!-- BACKGROUND WRAPPER (SAMA KAYA PROFILE) -->
        <div
            class="relative min-h-[calc(100vh-65px)] w-full bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 overflow-hidden flex flex-col md:items-center"
        >
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden z-0 pointer-events-none fixed">
                <div
                    class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-gradient-to-br from-blue-400/30 via-blue-300/20 to-transparent rounded-full blur-3xl animate-pulse"
                ></div>
                <div
                    class="absolute top-[30%] -right-[15%] w-[400px] h-[400px] bg-gradient-to-bl from-purple-400/25 via-pink-300/15 to-transparent rounded-full blur-3xl animate-pulse"
                    style="animation-delay: 1s"
                ></div>
                <div
                    class="absolute -bottom-[15%] left-[15%] w-[450px] h-[450px] bg-gradient-to-tr from-indigo-400/20 via-blue-300/15 to-transparent rounded-full blur-3xl animate-pulse"
                    style="animation-delay: 2s"
                ></div>
                <div
                    class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"
                ></div>
            </div>

            <!-- ================= MOBILE LAYOUT ================= -->
            <div class="md:hidden relative z-10 w-full text-gray-900 dark:text-white pb-20">
                <!-- SEARCH BAR MOBILE -->
                <div class="px-4 mt-4 relative z-40">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg
                                class="h-4 w-4 text-gray-500 dark:text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                        <input
                            v-model="searchQuery"
                            @keyup="performSearch"
                            type="text"
                            class="block w-full pl-9 pr-4 py-2 border-none rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-inner text-sm transition-all"
                            placeholder="Cari user untuk mulai chat..."
                        />
                    </div>

                    <!-- Search Dropdown Mobile -->
                    <div
                        v-if="searchQuery.length > 0"
                        class="absolute left-0 right-0 mt-2 mx-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-xl rounded-xl ring-1 ring-black ring-opacity-5 overflow-hidden z-50"
                    >
                        <div v-if="searchResults.length > 0">
                            <div
                                v-for="result in searchResults"
                                :key="result.id"
                                @click="visitProfile(result.username)"
                                class="px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex items-center transition-colors border-b border-gray-100 dark:border-gray-700/50 last:border-0"
                            >
                                <div
                                    class="flex-shrink-0 h-9 w-9 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 p-[2px] mr-3"
                                >
                                    <div
                                        class="w-full h-full bg-white dark:bg-gray-800 rounded-full flex items-center justify-center font-bold text-xs text-gray-700 dark:text-gray-200"
                                    >
                                        {{ result.name.charAt(0) }}
                                    </div>
                                </div>
                                <div>
                                    <div
                                        class="text-sm font-bold text-gray-900 dark:text-white leading-tight"
                                    >
                                        {{ result.username }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ result.name }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            v-else-if="!isSearching"
                            class="px-4 py-3 text-sm text-gray-500 text-center"
                        >
                            Tidak ditemukan.
                        </div>
                    </div>
                </div>

                <!-- HERO / WELCOME SECTION -->
                <div class="px-5 pt-8">
                    <h1 class="text-2xl font-black tracking-tight">
                        Halo, {{ currentUser?.name || currentUser?.username }} 👋
                    </h1>
                </div>

            </div>

            <!-- ================= DESKTOP LAYOUT ================= -->
            <div class="hidden md:block relative z-10 w-full max-w-4xl px-4 py-10">
                <!-- GLASS CARD -->
                <div
                    class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 p-8 md:p-10 relative overflow-visible"
                >
                    <!-- SEARCH BAR DESKTOP -->
                    <div class="mb-8 relative z-50">
                        <div class="relative group">
                            <div
                                class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                            >
                                <svg
                                    class="h-5 w-5 text-gray-400 group-focus-within:text-purple-500 transition-colors"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                    />
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                @keyup="performSearch"
                                type="text"
                                class="block w-full pl-11 pr-4 py-3 border-none rounded-2xl bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-sm backdrop-blur-sm transition-all"
                                placeholder="Cari user untuk mulai chat..."
                            />
                        </div>

                        <!-- Search Dropdown Desktop -->
                        <div
                            v-if="searchQuery.length > 0"
                            class="absolute mt-2 w-full bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl shadow-xl rounded-2xl py-2 ring-1 ring-black ring-opacity-5 overflow-hidden z-50 animate-fade-in-up"
                        >
                            <div v-if="searchResults.length > 0">
                                <div
                                    v-for="result in searchResults"
                                    :key="result.id"
                                    @click="visitProfile(result.username)"
                                    class="px-4 py-3 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-600 cursor-pointer flex items-center transition-colors"
                                >
                                    <div
                                        class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 p-[2px] mr-3"
                                    >
                                        <div
                                            class="w-full h-full bg-white dark:bg-gray-800 rounded-full flex items-center justify-center font-bold text-xs text-gray-700 dark:text-gray-200"
                                        >
                                            {{ result.name.charAt(0) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div
                                            class="text-sm font-bold text-gray-900 dark:text-white"
                                        >
                                            {{ result.username }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ result.name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                v-else-if="!isSearching"
                                class="px-4 py-3 text-sm text-gray-500 text-center"
                            >
                                Tidak ditemukan.
                            </div>
                        </div>
                    </div>

                    <!-- CONTENT GRID -->
                    <div class="flex flex-col md:flex-row gap-8 items-start">
                        <!-- LEFT: WELCOME -->
                        <div class="flex-1">
                            <h1
                                class="text-3xl md:text-4xl font-black tracking-tight text-gray-900 dark:text-white"
                            >
                                Selamat datang kembali,
                                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-500 to-purple-500">
                                    {{ currentUser?.name || currentUser?.username }}
                                </span>
                                👋
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 0.3s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes pulse-slow {
    0%,
    100% {
        box-shadow: 0 0 0 0px rgba(139, 92, 246, 0.4);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(139, 92, 246, 0);
    }
}

.animate-pulse-slow {
    animation: pulse-slow 3s infinite;
}

/* Hide scrollbar for Chrome, Safari and Opera */
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.no-scrollbar {
    -ms-overflow-style: none; /* IE and Edge */
    scrollbar-width: none; /* Firefox */
}
</style>
