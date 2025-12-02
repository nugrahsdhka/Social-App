<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

// KITA MASUKKAN DATA USER LAMA KE FORM
const form = useForm({
    name: user.name,
    email: user.email,
    username: user.username,   // Tambahan
    bio: user.bio,             // Tambahan
    is_private: Boolean(user.is_private), // Tambahan (pastikan format boolean)
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Profile Information</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update info profil dan pengaturan privasi akunmu.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            
            <!-- INPUT NAME -->
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- INPUT USERNAME (BARU) -->
            <div>
                <InputLabel for="username" value="Username" />
                <TextInput
                    id="username"
                    type="text"
                    class="mt-1 block w-full bg-gray-50 dark:bg-gray-700"
                    v-model="form.username"
                    required
                    autocomplete="username"
                />
                <p class="text-xs text-gray-500 mt-1">Username digunakan untuk login dan pencarian.</p>
                <InputError class="mt-2" :message="form.errors.username" />
            </div>

            <!-- INPUT EMAIL -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- INPUT BIO (BARU) -->
            <div>
                <InputLabel for="bio" value="Bio" />
                <textarea
                    id="bio"
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                    v-model="form.bio"
                    rows="3"
                    placeholder="Tulis sesuatu tentang dirimu..."
                ></textarea>
                <InputError class="mt-2" :message="form.errors.bio" />
            </div>

            <!-- CHECKBOX PRIVATE ACCOUNT (BARU) -->
            <div class="block bg-gray-50 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700">
                <label class="flex items-center cursor-pointer">
                    <input 
                        type="checkbox" 
                        v-model="form.is_private"
                        class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-900"
                    >
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 font-bold flex items-center">
                        <span class="mr-1">🔒</span> Private Account
                    </span>
                </label>
                <p class="text-xs text-gray-500 ml-6 mt-1">
                    Jika aktif, hanya follower yang bisa melihat postingan & mengirim pesan kepadamu.
                </p>
                <InputError class="mt-2" :message="form.errors.is_private" />
            </div>

            <!-- EMAIL VERIFICATION (BAWAAN BREEZE) -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>

                <div
                    v-show="status === 'verification-link-sent'"
                    class="mt-2 font-medium text-sm text-green-600 dark:text-green-400"
                >
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <!-- BUTTON SAVE -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400 font-bold text-green-600">Saved.</p>
                </Transition>
            </div>
        </form>
    </section>
</template>