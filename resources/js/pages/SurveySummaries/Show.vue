<template>
    <div class="p-6">
        <div class="mb-6 flex justify-end items-center">
            <primary-link :to="`/surveys/${$route.params.surveyId}`">Back</primary-link>
        </div>

        <div>
            <h1 class="text-2xl font-bold mb-2">{{ summary.survey.title }}</h1>
            <p class="mb-6">{{ summary.survey.description }}</p>
        </div>

        <hr />

        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-4">Summary Details</h2>
            <div class="mb-4">
                <strong>Creator:</strong><br /> {{ summary.created_by.name }}
            </div>
            <div class="mb-4">
                <strong>Sentiment:</strong><br /> {{ summary.sentiment }}
            </div>
            <div class="mb-4">
                <strong>Summary:</strong><br /> {{ summary.summary }}
            </div>
            <div class="mb-4">
                <strong>Total Responses:</strong><br /> {{ summary.total_responses }}
            </div>
            <div class="mb-4">
                <strong>Completed At:</strong><br /> {{ formatDate(summary.completed_at) }}
            </div>
            <div class="mb-4">
                <strong>Status:</strong><br /> {{ summary.status }}
            </div>
            <div class="mb-4">
                <strong>Error Message:</strong><br /> {{ summary.error_message || 'N/A' }}
            </div>
            <div class="mb-4">
                <strong>Created At:</strong><br /> {{ formatDate(summary.created_at) }}
            </div>
        </div>

        <div class="flex items-center justify-center my-6">
            <primary-link :to="`/surveys/${$route.params.surveyId}`">Back</primary-link>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { getSurveySummary } from '../../api';
import PrimaryLink from '../../components/PrimaryLink.vue';
import { AxiosError } from 'axios';
import { useRoute, onBeforeRouteUpdate } from 'vue-router';
import { formatDate } from '../../bootstrap';

const route = useRoute();
const summary = ref({
    created_by: {
        name: '',
    },
    survey: {
        title: '',
        description: '',
    },
});

onBeforeRouteUpdate(async (to, from) => {
    if (to.params.surveyId && to.params.summaryId) {
        await fetchSurveySummary(to.params.surveyId, to.params.summaryId);
    }
});

onMounted(async () => {
    if (route.params.surveyId && route.params.summaryId) {
        await fetchSurveySummary(route.params.surveyId, route.params.summaryId);
    }
});

const fetchSurveySummary = async (surveyId, summaryId) => {
    try {
        summary.value = (await getSurveySummary(surveyId, summaryId)).data;
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};
</script>
