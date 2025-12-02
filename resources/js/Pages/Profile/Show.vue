<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, router, Link } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

// Menerima data dari ProfileController
const props = defineProps({
    user: Object, 
    isCurrentUserProfile: Boolean,
    isFollowing: Boolean,
});

// State lokal
const isFollowingLocal = ref(props.isFollowing);
const followersCountLocal = ref(props.user.followers_count);
const isLoadingFollow = ref(false);

// --- LOGIC PENCARIAN USER ---
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
        console.error("Search error", error);
    } finally {
        isSearching.value = false;
    }
};

const visitProfile = (username) => {
    searchQuery.value = '';
    searchResults.value = [];
    router.visit(route('profile.show', username));
};

watch(() => props.user, (newUser) => {
    followersCountLocal.value = newUser.followers_count;
    isFollowingLocal.value = props.isFollowing;
});

const toggleFollow = async () => {
    isLoadingFollow.value = true;
    try {
        await axios.post('/chat/follow', { user_id: props.user.id });
        if (isFollowingLocal.value) {
            followersCountLocal.value--;
        } else {
            followersCountLocal.value++;
        }
        isFollowingLocal.value = !isFollowingLocal.value;
    } catch (error) {
        console.error("Gagal follow", error);
    } finally {
        isLoadingFollow.value = false;
    }
};
</script>

<template>
    <Head :title="user.username + ' (@' + user.name + ')'" />

    <AuthenticatedLayout>
        <!-- BACKGROUND WRAPPER -->
        <div class="relative min-h-[calc(100vh-65px)] w-full bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 overflow-hidden flex flex-col md:items-center">
            
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden z-0 pointer-events-none fixed">
                <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-gradient-to-br from-blue-400/30 via-blue-300/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute top-[30%] -right-[15%] w-[400px] h-[400px] bg-gradient-to-bl from-purple-400/25 via-pink-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
                <div class="absolute -bottom-[15%] left-[15%] w-[450px] h-[450px] bg-gradient-to-tr from-indigo-400/20 via-blue-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
                <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
            </div>

            <!-- ================= MOBILE LAYOUT (INSTAGRAM STYLE - BERSIH) ================= -->
            <div class="md:hidden relative z-10 w-full text-gray-900 dark:text-white pb-20">

                <!-- 2. SEARCH BAR (MOBILE) - ADDED HERE -->
                <div class="px-4 mt-4 relative z-40">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input 
                            v-model="searchQuery"
                            @keyup="performSearch"
                            type="text" 
                            class="block w-full pl-9 pr-4 py-2 border-none rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 shadow-inner text-sm transition-all" 
                            placeholder="Cari user lain..." 
                        />
                    </div>

                    <!-- Search Dropdown Mobile -->
                    <div v-if="searchQuery.length > 0" class="absolute left-0 right-0 mt-2 mx-4 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-xl rounded-xl ring-1 ring-black ring-opacity-5 overflow-hidden z-50">
                        <div v-if="searchResults.length > 0">
                            <div 
                                v-for="result in searchResults" 
                                :key="result.id"
                                @click="visitProfile(result.username)"
                                class="px-4 py-3 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer flex items-center transition-colors border-b border-gray-100 dark:border-gray-700/50 last:border-0"
                            >
                                <div class="flex-shrink-0 h-9 w-9 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 p-[2px] mr-3">
                                    <div class="w-full h-full bg-white dark:bg-gray-800 rounded-full flex items-center justify-center font-bold text-xs text-gray-700 dark:text-gray-200">
                                        {{ result.name.charAt(0) }}
                                    </div>
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900 dark:text-white leading-tight">{{ result.username }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ result.name }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="!isSearching" class="px-4 py-3 text-sm text-gray-500 text-center">
                            Tidak ditemukan.
                        </div>
                    </div>
                </div>

                <!-- 3. Profile Info Area (Layout: Avatar Left, Username & Stats Right) -->
                <div class="px-5 pt-6 pb-4 flex items-center">
                    <!-- Avatar (Left) -->
                    <div class="mr-6 flex-shrink-0 relative">
                        <div class="w-20 h-20 rounded-full p-[3px] bg-gradient-to-tr from-blue-400 via-purple-500 to-pink-500 shadow-lg">
                             <div class="w-full h-full bg-white dark:bg-gray-900 rounded-full p-[2px]">
                                <div class="w-full h-full bg-gray-200 dark:bg-gray-800 rounded-full flex items-center justify-center overflow-hidden">
                                     <span class="text-3xl font-bold text-gray-500">{{ user.name.charAt(0) }}</span>
                                </div>
                             </div>
                        </div>
                    </div>

                    <!-- Right Column: Username & Stats -->
                    <div class="flex-1 flex flex-col justify-center h-full">
                        <!-- Username Header -->
                        <div class="flex items-center gap-1 mb-3">
                            <h1 class="text-xl font-bold tracking-tight text-gray-900 dark:text-white leading-none">
                                {{ user.username }}
                            </h1>
                        </div>

                        <!-- Stats Row -->
                        <div class="flex justify-between items-center pr-2">
                            <div class="flex flex-col items-center">
                                <span class="font-bold text-lg text-gray-900 dark:text-white leading-tight">{{ user.posts_count }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">posts</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="font-bold text-lg text-gray-900 dark:text-white leading-tight">{{ followersCountLocal }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">followers</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <span class="font-bold text-lg text-gray-900 dark:text-white leading-tight">{{ user.following_count }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">following</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. Name & Bio -->
                <div class="px-5 pb-5">
                    <div class="font-bold text-sm text-gray-900 dark:text-white">{{ user.name }}</div>
                    <div class="text-sm whitespace-pre-line leading-relaxed mt-1 text-gray-700 dark:text-gray-300">{{ user.bio || 'Belum ada bio.' }}</div>
                </div>

                <!-- 5. Action Buttons -->
                <div class="px-5 flex gap-2 mb-6">
                    <template v-if="isCurrentUserProfile">
                        <Link :href="route('profile.edit')" class="flex-1 bg-white/40 dark:bg-gray-800/40 backdrop-blur-sm border border-white/40 dark:border-gray-700/50 hover:bg-white/60 dark:hover:bg-gray-700/60 text-sm font-semibold py-2 rounded-xl text-center transition-all shadow-sm">
                            Edit profile
                        </Link>
                    </template>
                    <template v-else>
                         <button 
                            @click="toggleFollow"
                            :class="isFollowingLocal 
                                ? 'bg-white/40 dark:bg-gray-800/40 text-gray-900 dark:text-white border border-white/40 dark:border-gray-700/50' 
                                : 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg shadow-purple-500/30 border-transparent'"
                            class="flex-1 font-semibold py-2 rounded-xl text-sm text-center transition-all flex items-center justify-center backdrop-blur-sm"
                        >
                            <span v-if="isLoadingFollow" class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
                            <span v-else>{{ isFollowingLocal ? 'Following' : 'Follow' }}</span>
                        </button>
                    </template>
                </div>

                <!-- 6. Tabs (Hanya Post Grid) -->
                <div class="px-10 flex border-t border-gray-300/30 dark:border-gray-700/30">
                    <div class="flex-1 py-3 border-b-2 border-gray-800 dark:border-white flex justify-center cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-900 dark:text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </div>
                </div>

                <!-- 7. Grid Posts -->
                <div class="grid grid-cols-3 gap-0.5 mt-0.5">
                    <div v-if="user.posts_count === 0" class="col-span-3 text-center py-20 text-gray-500 dark:text-gray-400">
                        <div class="w-16 h-16 border-2 border-gray-400 dark:border-gray-600 rounded-full flex items-center justify-center mx-auto mb-4 opacity-70">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200">No Posts Yet</h3>
                        <p class="text-sm">When you share photos, they will appear on your profile.</p>
                    </div>
                </div>

            </div>

            <!-- ================= DESKTOP LAYOUT (GLASSMORPHISM) ================= -->
            <div class="hidden md:block relative z-10 w-full max-w-4xl px-4 py-8">
                
                <!-- GLASS CARD -->
                <div class="bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 p-6 md:p-10 relative overflow-visible">
                    
                    <!-- SEARCH BAR (Floating Style) -->
                    <div class="mb-10 relative z-50">
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-purple-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input 
                                v-model="searchQuery"
                                @keyup="performSearch"
                                type="text" 
                                class="block w-full pl-11 pr-4 py-3 border-none rounded-2xl bg-white/50 dark:bg-gray-800/50 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500 shadow-sm backdrop-blur-sm transition-all" 
                                placeholder="Cari user lain..." 
                            />
                        </div>

                        <!-- Search Dropdown -->
                        <div v-if="searchQuery.length > 0" class="absolute mt-2 w-full bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl shadow-xl rounded-2xl py-2 ring-1 ring-black ring-opacity-5 overflow-hidden z-50 animate-fade-in-up">
                            <div v-if="searchResults.length > 0">
                                <div 
                                    v-for="result in searchResults" 
                                    :key="result.id"
                                    @click="visitProfile(result.username)"
                                    class="px-4 py-3 hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-600 cursor-pointer flex items-center transition-colors"
                                >
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 p-[2px] mr-3">
                                        <div class="w-full h-full bg-white dark:bg-gray-800 rounded-full flex items-center justify-center font-bold text-xs text-gray-700 dark:text-gray-200">
                                            {{ result.name.charAt(0) }}
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ result.username }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ result.name }}</div>
                                    </div>
                                </div>
                            </div>
                            <div v-else-if="!isSearching" class="px-4 py-3 text-sm text-gray-500 text-center">
                                Tidak ditemukan.
                            </div>
                        </div>
                    </div>

                    <!-- PROFILE INFO SECTION -->
                    <div class="flex flex-col md:flex-row md:items-start relative z-10">
                        
                        <!-- Avatar Profile -->
                        <div class="flex-shrink-0 mx-auto md:mx-0 md:mr-10 mb-6 md:mb-0">
                            <div class="w-32 h-32 md:w-40 md:h-40 bg-gradient-to-tr from-blue-500 via-purple-500 to-pink-500 p-[4px] rounded-full shadow-lg animate-pulse-slow">
                                <div class="w-full h-full bg-white dark:bg-gray-800 rounded-full flex items-center justify-center overflow-hidden border-4 border-white dark:border-gray-900">
                                    <span class="text-5xl md:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-br from-blue-400 to-purple-600">
                                        {{ user.name.charAt(0) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- User Details -->
                        <div class="flex-1 flex flex-col text-center md:text-left">
                            
                            <div class="flex flex-col md:flex-row items-center mb-6 justify-center md:justify-start">
                                <h2 class="text-3xl font-black text-gray-800 dark:text-white mb-4 md:mb-0 mr-0 md:mr-6 tracking-tight">
                                    {{ user.username }}
                                </h2>

                                <div v-if="isCurrentUserProfile">
                                    <Link 
                                        :href="route('profile.edit')" 
                                        class="px-5 py-2 rounded-xl font-bold text-sm text-gray-700 dark:text-gray-200 bg-white/50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 hover:bg-white hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5"
                                    >
                                        Edit Profile
                                    </Link>
                                </div>
                                
                                <div v-else>
                                    <button 
                                        @click="toggleFollow"
                                        :disabled="isLoadingFollow"
                                        :class="isFollowingLocal 
                                            ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600' 
                                            : 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg hover:shadow-purple-500/30'"
                                        class="px-6 py-2 rounded-xl font-bold text-sm transition-all duration-300 transform hover:-translate-y-0.5 flex items-center justify-center min-w-[110px]"
                                    >
                                        <span v-if="isLoadingFollow" class="flex items-center gap-2">
                                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </span>
                                        <span v-else>{{ isFollowingLocal ? 'Following' : 'Follow' }}</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Stats -->
                            <div class="flex justify-center md:justify-start space-x-8 mb-8 text-gray-800 dark:text-gray-200">
                                <div class="text-center md:text-left">
                                    <span class="font-bold text-lg block">{{ user.posts_count }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">posts</span>
                                </div>
                                <div class="text-center md:text-left cursor-pointer hover:text-purple-600 transition-colors">
                                    <span class="font-bold text-lg block">{{ followersCountLocal }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">followers</span>
                                </div>
                                <div class="text-center md:text-left cursor-pointer hover:text-purple-600 transition-colors">
                                    <span class="font-bold text-lg block">{{ user.following_count }}</span>
                                    <span class="text-xs text-gray-500 uppercase tracking-wide">following</span>
                                </div>
                            </div>

                            <!-- Bio Desktop -->
                            <div class="hidden md:block text-gray-700 dark:text-gray-300">
                                <h1 class="font-bold text-lg">{{ user.name }}</h1>
                                <p v-if="user.bio" class="mt-2 whitespace-pre-line leading-relaxed">{{ user.bio }}</p>
                                <p v-else class="text-gray-500 italic mt-2">Belum ada bio.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Divider / Tabs -->
                    <div class="border-t border-gray-200 dark:border-gray-700 mt-10 pt-1 flex justify-center relative z-10">
                        <div class="border-t-2 border-black dark:border-white -mt-1.5 py-4 px-6 text-black dark:text-white flex items-center cursor-pointer gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            <span class="text-xs font-bold tracking-widest">POSTS</span>
                        </div>
                    </div>

                    <!-- Post Grid -->
                    <div class="grid grid-cols-3 gap-1 md:gap-4 mt-2 relative z-10">
                        <div v-if="user.posts_count === 0" class="col-span-3 text-center py-20 text-gray-400 dark:text-gray-500 bg-gray-50/50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                            <div class="text-6xl mb-4 opacity-50">📷</div>
                            <p class="font-medium">Belum ada postingan.</p>
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
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse-slow {
    0%, 100% { box-shadow: 0 0 0 0px rgba(139, 92, 246, 0.4); }
    50% { box-shadow: 0 0 0 10px rgba(139, 92, 246, 0); }
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
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
}
</style>