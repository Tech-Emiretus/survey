<template>
    <div class="p-6">
        <div class="mb-6 flex justify-end">
            <primary-link to="/surveys/create">Add Survey</primary-link>
        </div>

        <template v-if="surveys.length">
            <div class="overflow-x-scroll shadow bg-white dark:bg-gray-800">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th>Actions</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Start</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">End</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Creator</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="survey in surveys" :key="survey.id" class="hover:bg-gray-50 dark:hover:bg-gray-700  cursor-pointer" @click="$router.push(`/surveys/${survey.id}`)" title="Click to view survey details">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">
                                <primary-link :to="`/surveys/edit/${survey.id}`" @click.stop class="px-1 mr-2" v-if="userInfo.id === survey.created_by.id">
                                    Edit
                                </primary-link>
                                <primary-button @click.prevent.stop="copyPublicUrl(survey.public_id)">Url</primary-button>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ survey.id }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">{{ survey.title }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ survey.description }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <span
                                    :class="survey.status === 'active'
                                        ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'
                                        : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'"
                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                >
                                    {{ survey.status }}
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ survey.start_at }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ survey.end_at }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ survey.created_by.name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ survey.created_at }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template v-else>
            <p class="text-center text-gray-500 dark:text-gray-400 py-8">No surveys available</p>
        </template>
    </div>
</template>

<script setup>
import { onMounted, ref, inject } from 'vue';
import { getSurveys } from '../../api';
import PrimaryLink from '../../components/PrimaryLink.vue';
import PrimaryButton from '../../components/PrimaryButton.vue';

const userInfo = inject('userInfo');
const surveys = ref([]);

const copyPublicUrl = (publicId) => {
    const publicUrl = `${window.location.origin}/participate/${publicId}`;
    navigator.clipboard.writeText(publicUrl).then(() => {
        alert('Public URL copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('Failed to copy the public URL.');
    });
};

onMounted(async () => {
    surveys.value = (await getSurveys()).data;
})
</script>
