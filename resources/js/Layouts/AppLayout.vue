<template>
    <navigation v-if="!inParticipation" :user="user" />
  <div>
    <header class="bg-white dark:bg-gray-800 shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ header }}
            </h2>
        </div>
    </header>
    <main>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <RouterView />
                    </div>
                </div>
            </div>
        </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, provide } from 'vue';
import { useRoute } from 'vue-router';
import Navigation from './Navigation.vue';
import { getUserInfo } from '../api.js';

const route = useRoute();
const header = computed(() => route.meta.name)
const user = ref({});
const inParticipation = computed(() => route.name === 'participate-survey');

provide('userInfo', user);

onMounted(async () => {
    if (!inParticipation.value) {
        const userInfo = await getUserInfo();
        user.value = userInfo;

        document.title = `Survey - ${header.value}`;
    } else {
        document.title = 'Participate in Survey';
    }
});
</script>
