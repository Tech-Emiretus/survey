<template>
    <div class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <div class="flex items-center">
                <primary-button @click.prevent="copyPublicUrl">Copy Public Url</primary-button>
                <primary-button type="button" @click.prevent="deleteSurvey" class="ml-2 bg-red-600 dark:bg-red-600">Delete</primary-button>
            </div>

            <div class="flex items-center">
                <primary-link to="/surveys">Back</primary-link>
            </div>
        </div>

        <div class="text-center">
            <h1 class="text-2xl font-bold mb-2">{{ survey.title }}</h1>
            <p class="mb-6">{{ survey.description }}</p>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold mb-2">Survey Summaries ({{ summaries.length }})</h2>
                <primary-button @click.prevent.stop="generateNewSummary" class="mb-4">Generate New Summary</primary-button>
            </div>
            <survey-summaries :summaries="summaries" :survey="survey" />
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-2">Survey Responses ({{ responses.length }})</h2>
            <survey-responses :responses="responses" :survey="survey" />
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { createSurveySummary, deleteSurvey as deleteSurveyApi, getSurvey, getSurveyResponses, getSurveySummaries } from '../../api';
import PrimaryLink from '../../components/PrimaryLink.vue';
import PrimaryButton from '../../components/PrimaryButton.vue';
import { AxiosError } from 'axios';
import { useRoute, useRouter, onBeforeRouteUpdate } from 'vue-router';
import SurveySummaries from '../SurveySummaries/Index.vue';
import SurveyResponses from '../SurveyResponses/Index.vue';

const router = useRouter();
const route = useRoute();

const survey = ref({});
const responses = ref([]);
const summaries = ref([]);

onBeforeRouteUpdate(async (to, from) => {
    if (to.params.id) {
        await fetchSurvey(to.params.id);
    }
});

onMounted(async () => {
    if (route.params.id) {
        await fetchSurvey(route.params.id);
    }
});

const fetchSurvey = async (id) => {
    try {
        survey.value = (await getSurvey(id)).data;
        responses.value = (await getSurveyResponses(id)).data;
        summaries.value = (await getSurveySummaries(id)).data;
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};

const deleteSurvey = async () => {
    if (!survey.value.id || !confirm('Are you sure you want to delete this survey?')) {
        return;
    }

    try {
        await deleteSurveyApi(survey.value.id);
        alert('Survey was deleted successfully.');
        router.push('/surveys');
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};

const generateNewSummary = async () => {
    const surveyId = route.params.id || survey.value.id;

    if (!surveyId) {
        alert('No survey selected.');
        return;
    }

    try {
        if (!confirm('Are you sure you want to generate a new summary?')) {
            return;
        }

        await createSurveySummary(surveyId);
        alert('New summary generated successfully.');
        await fetchSurvey(surveyId);
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};

const copyPublicUrl = () => {
    const publicUrl = `${window.location.origin}/participate/${existingSurvey.value.public_id}`;
    navigator.clipboard.writeText(publicUrl).then(() => {
        alert('Public URL copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
        alert('Failed to copy the public URL.');
    });
};

</script>
