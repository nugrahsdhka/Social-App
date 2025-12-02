<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';

const props = defineProps({
    recentUsers: Array
});

const page = usePage();
const currentUser = page.props.auth.user;

const contactList = ref(props.recentUsers || []);
const searchQuery = ref('');
const searchResults = ref([]);
const selectedUser = ref(null); 
const selectedUserData = ref(null); 

const messages = ref([]);
const newMessage = ref('');
const messageContainer = ref(null);
const isSearching = ref(false);
const showMobileChat = ref(false); // State untuk navigasi mobile

// 1. Fungsi Cari User
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

// 2. Pilih User
const selectUser = async (user) => {
    selectedUser.value = user;
    showMobileChat.value = true; // Buka tampilan chat di mobile
    
    // Reset pencarian
    searchQuery.value = ''; 
    searchResults.value = [];
    
    await loadUserProfile(user.id);
};

// Fungsi untuk kembali ke list kontak (Mobile only)
const closeMobileChat = () => {
    showMobileChat.value = false;
};

const loadUserProfile = async (userId) => {
    try {
        const response = await axios.get(`/chat/${userId}`);
        selectedUserData.value = response.data;
        messages.value = response.data.messages;
        scrollToBottom();
    } catch (error) {
        console.error("Load profile error", error);
    }
}

const toggleFollow = async () => {
    if(!selectedUser.value) return;
    try {
        await axios.post('/chat/follow', { user_id: selectedUser.value.id });
        await loadUserProfile(selectedUser.value.id);
    } catch (error) { console.error(error); }
};

// 3. Kirim Pesan
const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedUser.value) return;

    const tempMsg = {
        id: Date.now(),
        sender_id: currentUser.id,
        receiver_id: selectedUser.value.id,
        message: newMessage.value,
        created_at: new Date().toISOString()
    };
    
    messages.value.push(tempMsg);
    
    const exists = contactList.value.find(u => u.id === selectedUser.value.id);
    if (!exists) {
        contactList.value.unshift(selectedUser.value);
    }

    const msgToSend = newMessage.value;
    newMessage.value = '';
    scrollToBottom();

    await axios.post('/chat', {
        receiver_id: selectedUser.value.id,
        message: msgToSend
    });
};

const scrollToBottom = async () => {
    await nextTick();
    if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
};

onMounted(() => {
    if (window.Echo) {
        window.Echo.private(`chat.${currentUser.id}`)
            .listen('MessageSent', (e) => {
                if (selectedUser.value && e.message.sender_id === selectedUser.value.id) {
                    messages.value.push(e.message);
                    scrollToBottom();
                }

                const senderExists = contactList.value.find(u => u.id === e.message.sender.id);
                if (!senderExists) {
                    contactList.value.unshift(e.message.sender);
                }
            });
    }
});
</script>

<template>
    <Head title="Chat" />

    <AuthenticatedLayout>
        <!-- BACKGROUND WRAPPER -->
        <div class="relative h-[calc(100vh-65px)] w-full bg-gradient-to-br from-gray-50 via-blue-50/30 to-purple-50/30 dark:from-gray-950 dark:via-gray-900 dark:to-gray-950 overflow-hidden flex flex-col">
            
            <!-- Animated Background Elements -->
            <div class="absolute inset-0 overflow-hidden z-0 pointer-events-none">
                <div class="absolute -top-[20%] -left-[10%] w-[500px] h-[500px] bg-gradient-to-br from-blue-400/30 via-blue-300/20 to-transparent rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute top-[30%] -right-[15%] w-[400px] h-[400px] bg-gradient-to-bl from-purple-400/25 via-pink-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
                <div class="absolute -bottom-[15%] left-[15%] w-[450px] h-[450px] bg-gradient-to-tr from-indigo-400/20 via-blue-300/15 to-transparent rounded-full blur-3xl animate-pulse" style="animation-delay: 2s"></div>
                <div class="absolute inset-0 bg-[linear-gradient(rgba(99,102,241,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(99,102,241,0.03)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_50%,black,transparent)]"></div>
            </div>

            <!-- MAIN CHAT INTERFACE -->
            <div class="relative z-10 flex-1 flex flex-col p-2 md:p-4 h-full overflow-hidden">
                
                <!-- GLASS CARD CONTAINER -->
                <div class="w-full h-full flex bg-white/60 dark:bg-gray-900/60 backdrop-blur-2xl rounded-3xl shadow-2xl border border-white/20 dark:border-gray-700/50 overflow-hidden">

                    <!-- SIDEBAR (User List) -->
                    <!-- Logic Mobile: Hidden jika showMobileChat = true -->
                    <div :class="showMobileChat ? 'hidden md:flex' : 'flex'" 
                         class="w-full md:w-1/3 border-r border-white/20 dark:border-gray-700/50 flex-col bg-white/30 dark:bg-gray-800/30 h-full transition-all">
                        
                        <!-- Header & Search -->
                        <div class="p-6 border-b border-white/20 dark:border-gray-700/50">
                            <h2 class="text-2xl font-black mb-6 text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-purple-600 to-pink-500">
                                Messages
                            </h2>
                            <div class="relative">
                                <input 
                                    v-model="searchQuery" 
                                    @keyup="performSearch"
                                    type="text" 
                                    placeholder="Cari teman..." 
                                    class="w-full rounded-2xl bg-white/50 dark:bg-gray-800/50 border-gray-200 dark:border-gray-700 focus:border-purple-500 focus:ring-purple-500 text-gray-800 dark:text-white px-5 py-3 pl-11 shadow-sm transition-all"
                                >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 absolute left-4 top-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        </div>

                        <!-- List Area -->
                        <div class="flex-1 overflow-y-auto no-scrollbar p-3 space-y-2">
                            
                            <!-- Search Results -->
                            <div v-if="searchQuery.length > 0">
                                <p class="px-4 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider">Hasil Pencarian</p>
                                <div v-for="user in searchResults" :key="user.id" 
                                     @click="selectUser(user)"
                                     class="group flex items-center p-3 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-purple-50 dark:hover:from-gray-800 dark:hover:to-gray-700 cursor-pointer transition-all duration-300">
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 p-[2px]">
                                            <div class="w-full h-full bg-white dark:bg-gray-900 rounded-full flex items-center justify-center text-sm font-bold text-gray-700 dark:text-gray-200">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="font-bold text-gray-800 dark:text-white group-hover:text-purple-600 transition-colors">{{ user.username }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ user.name }}</p>
                                    </div>
                                </div>
                                <div v-if="searchResults.length === 0 && !isSearching" class="p-6 text-center text-gray-500 text-sm">
                                    Tidak ditemukan.
                                </div>
                            </div>

                            <!-- Recent Chats -->
                            <div v-else>
                                <div v-for="user in contactList" :key="user.id" 
                                     @click="selectUser(user)"
                                     :class="{'bg-gradient-to-r from-blue-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 border-l-4 border-purple-500': selectedUser?.id === user.id}"
                                     class="group flex items-center p-3 rounded-xl hover:bg-white/40 dark:hover:bg-gray-700/40 cursor-pointer transition-all duration-300">
                                    
                                    <div class="relative">
                                        <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-500 to-pink-500 p-[2px] shadow-lg">
                                            <div class="w-full h-full bg-white dark:bg-gray-900 rounded-full flex items-center justify-center font-bold text-gray-700 dark:text-gray-200">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                        </div>
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white dark:border-gray-900 rounded-full"></div>
                                    </div>
                                    
                                    <div class="ml-4 flex-1 min-w-0">
                                        <div class="flex justify-between items-center mb-1">
                                            <p class="font-bold text-gray-800 dark:text-white truncate group-hover:text-purple-600 transition-colors">
                                                {{ user.username }}
                                            </p>
                                        </div>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ user.name }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="contactList.length === 0" class="flex flex-col items-center justify-center h-48 text-center text-gray-400 px-6">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mb-3">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm">Belum ada obrolan.</p>
                                    <p class="text-xs mt-1">Cari teman untuk memulai!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CHAT AREA -->
                    <!-- Logic Mobile: Visible (flex) jika showMobileChat = true -->
                    <div :class="showMobileChat ? 'flex' : 'hidden md:flex'"
                         class="flex-1 flex-col relative bg-white/20 dark:bg-gray-900/20 h-full">
                        
                        <!-- Active Chat View -->
                        <div v-if="selectedUser && selectedUserData" class="flex flex-col h-full overflow-hidden">
                            
                            <!-- Chat Header -->
                            <div class="px-4 md:px-6 py-4 bg-white/40 dark:bg-gray-800/40 backdrop-blur-md border-b border-white/20 dark:border-gray-700/50 flex items-center justify-between z-20">
                                <div class="flex items-center">
                                    <!-- Back Button (Mobile Only) -->
                                    <button @click="closeMobileChat" class="md:hidden mr-3 text-gray-600 dark:text-gray-300 hover:text-purple-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                        </svg>
                                    </button>

                                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 p-[2px] mr-3">
                                        <div class="w-full h-full bg-white dark:bg-gray-900 rounded-full flex items-center justify-center text-sm font-bold">
                                            {{ selectedUser.name.charAt(0) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg text-gray-900 dark:text-white leading-tight">{{ selectedUser.username }}</h3>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ selectedUserData.is_following ? 'Friend' : 'New Connection' }}
                                        </p>
                                    </div>
                                </div>
                                <button @click="toggleFollow"
                                    :class="selectedUserData.is_following 
                                        ? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600' 
                                        : 'bg-gradient-to-r from-blue-600 to-purple-600 text-white shadow-lg hover:shadow-purple-500/30'"
                                    class="px-3 md:px-5 py-2 rounded-xl font-bold text-xs transition-all duration-300 transform hover:scale-105 whitespace-nowrap">
                                    {{ selectedUserData.is_following ? 'Following' : 'Follow' }}
                                </button>
                            </div>

                            <!-- Chat Messages Area -->
                            <div v-if="selectedUserData.can_chat" class="flex-1 flex flex-col overflow-hidden relative">
                                <div ref="messageContainer" class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4">
                                    <div v-for="msg in messages" :key="msg.id" 
                                         :class="msg.sender_id === currentUser.id ? 'justify-end' : 'justify-start'"
                                         class="flex animate-fade-in-up">
                                        
                                        <div :class="msg.sender_id === currentUser.id 
                                            ? 'bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-2xl rounded-tr-sm shadow-purple-500/20' 
                                            : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-2xl rounded-tl-sm border border-gray-100 dark:border-gray-700'"
                                             class="max-w-[75%] md:max-w-[70%] px-5 py-3 shadow-lg text-sm relative group">
                                            {{ msg.message }}
                                            
                                            <!-- Timestamp (Visible on Hover) -->
                                            <span class="absolute -bottom-5 text-[10px] text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity"
                                                  :class="msg.sender_id === currentUser.id ? 'right-0' : 'left-0'">
                                                Just now
                                            </span>
                                        </div>

                                    </div>
                                </div>

                                <!-- Input Area -->
                                <div class="p-3 md:p-5 bg-white/40 dark:bg-gray-800/40 backdrop-blur-md">
                                    <form @submit.prevent="sendMessage" class="relative flex items-center gap-2 md:gap-3">
                                        <input 
                                            v-model="newMessage" 
                                            type="text" 
                                            placeholder="Tulis pesan..." 
                                            class="w-full rounded-2xl border-none bg-white/80 dark:bg-gray-700/80 backdrop-blur-sm shadow-inner px-4 md:px-5 py-3 md:py-4 pr-12 md:pr-14 focus:ring-2 focus:ring-purple-500 dark:text-white transition-all text-sm md:text-base"
                                        >
                                        <button 
                                            type="submit" 
                                            class="absolute right-2 p-2 bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl text-white shadow-lg hover:shadow-purple-500/40 transition-all duration-300 transform hover:scale-105 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                                            :disabled="!newMessage.trim()"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <!-- Private Account Lock Screen -->
                            <div v-else class="flex-1 flex flex-col items-center justify-center text-center p-8 bg-white/30 dark:bg-gray-900/30 backdrop-blur-sm">
                                <!-- Back Button (Mobile Only) for Lock Screen too -->
                                <button @click="closeMobileChat" class="md:hidden absolute top-4 left-4 text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                    </svg>
                                </button>
                                
                                <div class="w-24 h-24 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600 rounded-full flex items-center justify-center mb-6 shadow-inner animate-pulse">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-black text-gray-800 dark:text-white mb-2">Akun Private</h2>
                                <p class="text-gray-500 dark:text-gray-400 mb-6 max-w-xs">Anda harus mengikuti user ini terlebih dahulu untuk memulai percakapan.</p>
                                <button @click="toggleFollow" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold rounded-2xl shadow-xl hover:shadow-purple-500/30 transition-all transform hover:-translate-y-1">
                                    Follow Sekarang
                                </button>
                            </div>
                        </div>

                        <!-- Empty State (No Chat Selected) -->
                        <div v-else class="flex-1 flex flex-col items-center justify-center text-center p-8 opacity-60">
                            <!-- Back Button (Mobile Only) -->
                             <button @click="closeMobileChat" class="md:hidden absolute top-4 left-4 text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                            </button>

                            <div class="relative w-32 h-32 mb-6">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-400 to-purple-500 rounded-full blur-xl opacity-30 animate-pulse"></div>
                                <img src="/img/logo.png" alt="Logo" class="relative w-full h-full object-contain animate-bounce" style="animation-duration: 3s" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
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

.animate-fade-in-up {
    animation: fadeInUp 0.3s ease-out forwards;
}
</style>