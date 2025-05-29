<template>
    <nav class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between">
                <div class="flex">
                    <!-- Logo -->
                    <div class="flex shrink-0 items-center">
                        <router-link to="/surveys">
                            <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                        </router-link>
                    </div>
                    <!-- Navigation Links -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <NavLink to="/surveys" :active="$route.name === 'surveys'"> Surveys </NavLink>
                    </div>
                </div>
                <!-- Settings Dropdown -->
                <div class="hidden sm:ms-6 sm:flex sm:items-center">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300"
                            >
                                <div>{{ user.name }}</div>
                                <div class="ms-1">
                                    <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path
                                            fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd"
                                        />
                                    </svg>
                                </div>
                            </button>
                        </template>
                        <template #content>
                            <DropdownLink as="a" to="/profile" href="/profile">Profile</DropdownLink>
                            <form @submit.prevent="logout">
                                <DropdownLink as="button" type="submit">Log Out</DropdownLink>
                            </form>
                        </template>
                    </Dropdown>
                </div>
                <!-- Hamburger -->
                <div class="-me-2 flex items-center sm:hidden">
                    <button
                        @click="open = !open"
                        class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400"
                    >
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path
                                :class="{ hidden: open, 'inline-flex': !open }"
                                class="inline-flex"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{ hidden: !open, 'inline-flex': open }"
                                class="hidden"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <!-- Responsive Navigation Menu -->
        <div :class="{ block: open, hidden: !open }" class="hidden sm:hidden">
            <div class="space-y-1 pb-3 pt-2">
                <ResponsiveNavLink to="/dashboard" :active="$route.name === 'dashboard'"> Dashboard </ResponsiveNavLink>
            </div>
            <!-- Responsive Settings Options -->
            <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600">
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800 dark:text-gray-200">{{ user.name }}</div>
                    <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink to="/profile/edit">Profile</ResponsiveNavLink>
                    <form @submit.prevent="logout">
                        <ResponsiveNavLink as="button" type="submit">Log Out</ResponsiveNavLink>
                    </form>
                </div>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import ApplicationLogo from '../components/ApplicationLogo.vue';
import NavLink from '../components/NavLink.vue';
import Dropdown from '../components/Dropdown.vue';
import DropdownLink from '../components/DropdownLink.vue';
import ResponsiveNavLink from '../components/ResponsiveNavLink.vue';
import { logout as logoutApi } from '../api.js';

const open = ref(false);
const router = useRouter();

// Dummy user for now; replace with actual auth/user store
defineProps({ user: Object });

async function logout() {
    await logoutApi();
    window.location.href = '/login';
}
</script>
