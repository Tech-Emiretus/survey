<template>
    <div>
        <template v-if="summaries.length">
            <div class="overflow-x-auto shadow bg-white dark:bg-gray-800">
                <table class="min-w-full w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-900">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">ID</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Response</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sentiment</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Completed At</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Creator</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Created At</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                        <tr v-for="summary in summaries" :key="summary.id" class="hover:bg-gray-50 dark:hover:bg-gray-700  cursor-pointer" @click="$router.push(`/surveys/${survey.id}/summaries/${summary.id}`)" title="Click to view survey details">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ summary.id }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100 font-medium">{{ summary.total_responses }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ summary.sentiment }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ summary.status }}</span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ formatDate(summary.completed_at) }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-200">{{ summary.created_by.name }}</td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ formatDate(summary.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>

        <template v-else>
            <p class="text-center text-gray-500 dark:text-gray-400 py-8">No survey summaries available</p>
        </template>
    </div>
</template>

<script setup>
import PrimaryLink from '../../components/PrimaryLink.vue';
import PrimaryButton from '../../components/PrimaryButton.vue';
import { formatDate } from '../../bootstrap';

defineProps({
    survey: {
        type: Object,
        required: true
    },
    summaries: {
        type: Array,
        required: true
    }
});
</script>
