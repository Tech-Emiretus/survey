<template>
    <div>
        <template v-if="responses.length">
            <div class="overflow-x-auto shadow bg-white dark:bg-gray-800">
                <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Respondent Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Respondent Email</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Updated At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="response in responses" :key="response.id" class="hover:bg-gray-50 dark:hover:bg-gray-700  cursor-pointer" @click="$router.push(`/surveys/${survey.id}/responses/${response.id}`)" title="Click to view response details">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ response.id }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">{{ response.respondent_name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ response.respondent_email }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ formatDate(response.created_at) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ formatDate(response.updated_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template v-else>
            <p class="text-center text-gray-500 dark:text-gray-400 py-8">No survey responses available</p>
        </template>
    </div>
</template>

<script setup>
import { formatDate } from '../../bootstrap';

defineProps({
    survey: {
        type: Object,
        required: true
    },
    responses: {
        type: Array,
        required: true
    }
});
</script>
