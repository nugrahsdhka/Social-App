<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, onMounted, nextTick, watch } from 'vue';
import axios from 'axios';

// Menerima data user lain dari Controller
const props = defineProps({
    users: Array,
});

const page = usePage();
const currentUser = page.props.auth.user; // User yang sedang login

const selectedUser = ref(null);
const messages = ref([]);
const newMessage = ref('');
const messageContainer = ref(null);

// Fungsi 1: Saat user lain diklik
const selectUser = async (user) => {
    selectedUser.value = user;
    // Ambil history chat dari backend
    try {
        const response = await axios.get(`/chat/${user.id}`);
        messages.value = response.data;
        scrollToBottom();
    } catch (error) {
        console.error("Gagal memuat pesan:", error);
    }
};

// Fungsi 2: Mengirim Pesan
const sendMessage = async () => {
    if (!newMessage.value.trim() || !selectedUser.value) return;

    const tempMessage = {
        id: Date.now(), // ID sementara
        sender_id: currentUser.id,
        receiver_id: selectedUser.value.id,
        message: newMessage.value,
        created_at: new Date().toISOString()
    };

    // Tampilkan pesan langsung di layar (Optimistic UI)
    messages.value.push(tempMessage);
    const messageToSend = newMessage.value;
    newMessage.value = ''; // Kosongkan input
    scrollToBottom();

    try {
        await axios.post('/chat', {
            receiver_id: selectedUser.value.id,
            message: messageToSend
        });
    } catch (error) {
        console.error("Gagal mengirim:", error);
    }
};

// Fungsi 3: Scroll otomatis ke bawah
const scrollToBottom = async () => {
    await nextTick();
    if (messageContainer.value) {
        messageContainer.value.scrollTop = messageContainer.value.scrollHeight;
    }
};

// Fungsi 4: SETUP REAL-TIME (Reverb)
onMounted(() => {
    // Dengarkan channel 'chat.ID_SAYA'
    window.Echo.private(`chat.${currentUser.id}`)
        .listen('MessageSent', (e) => {
            // Jika pesan datang dari user yang sedang dibuka chat-nya
            if (selectedUser.value && e.message.sender_id === selectedUser.value.id) {
                messages.value.push(e.message);
                scrollToBottom();
            } else {
                // Opsional: Kasih notifikasi toast/badge disini jika chat sedang tidak dibuka
                console.log("Pesan baru dari user lain!");
            }
        });
});
</script>

<template>
    <Head title="Chat" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">WhatsApp Clone</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg flex h-[600px]">

                    <div class="w-1/3 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
                        <div class="p-4 bg-gray-100 dark:bg-gray-900 font-bold text-gray-700 dark:text-gray-300">
                            Contacts
                        </div>
                        <ul>
                            <li v-for="user in users" :key="user.id"
                                @click="selectUser(user)"
                                :class="{'bg-blue-100 dark:bg-blue-900': selectedUser?.id === user.id}"
                                class="p-4 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700 border-b border-gray-200 dark:border-gray-700 flex items-center transition">
                                <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3 font-bold text-gray-600">
                                    {{ user.name.charAt(0) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</p>
                                    <p class="text-xs text-gray-500">Click to chat</p>
                                </div>
                            </li>
                        </ul>
                    </div>

                    <div class="w-2/3 flex flex-col bg-gray-50 dark:bg-gray-900">
                        
                        <div v-if="selectedUser" class="p-4 bg-gray-200 dark:bg-gray-800 border-b border-gray-300 dark:border-gray-700 flex items-center">
                            <div class="font-bold text-lg text-gray-800 dark:text-gray-200">{{ selectedUser.name }}</div>
                        </div>

                        <div v-if="selectedUser" ref="messageContainer" class="flex-1 overflow-y-auto p-4 space-y-4">
                            <div v-for="msg in messages" :key="msg.id" 
                                 :class="{'justify-end': msg.sender_id === currentUser.id, 'justify-start': msg.sender_id !== currentUser.id}"
                                 class="flex">
                                
                                <div :class="msg.sender_id === currentUser.id ? 'bg-green-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200'"
                                     class="max-w-xs md:max-w-md px-4 py-2 rounded-lg shadow">
                                    <p>{{ msg.message }}</p>
                                    <p class="text-[10px] mt-1 opacity-70 text-right">
                                        {{ new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div v-else class="flex-1 flex items-center justify-center text-gray-400">
                            Pilih kontak untuk mulai chat
                        </div>

                        <div v-if="selectedUser" class="p-4 bg-gray-200 dark:bg-gray-800">
                            <form @submit.prevent="sendMessage" class="flex gap-2">
                                <input v-model="newMessage" type="text" placeholder="Ketik pesan..." 
                                       class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-green-500 focus:border-green-500">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-bold">
                                    Kirim
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>