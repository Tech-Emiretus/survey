<template>
    <div class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <primary-link :to="`/surveys/${$route.params.surveyId}`">Back</primary-link>
        </div>

        <div>
            <h1 class="text-2xl font-bold mb-2">{{ response.survey.title }}</h1>
            <p class="mb-6">{{ response.survey.description }}</p>
        </div>

        <form>
            <div class="mb-4">
                <label for="respondent_name" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Respondent Name</label>
                <input
                    id="respondent_name"
                    type="text"
                    v-model="response.respondent_name"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter your name"
                    :disabled="submitted"
                />
            </div>

            <div class="mb-6">
                <label for="respondent_email" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Respondent Email</label>
                <input
                    id="respondent_email"
                    type="email"
                    v-model="response.respondent_email"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter your email"
                    :disabled="submitted"
                />
            </div>

            <hr />

            <div class="mt-6 mb-6" v-for="(fieldResponse, index) in response.field_responses" :key="fieldResponse.id">
                <label :for="fieldResponse.id" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">{{ fieldResponse.survey_field.name }}</label>

                <template v-if="fieldResponse.survey_field.type === 'text'">
                    <input
                        :id="fieldResponse.id"
                        type="text"
                        v-model="fieldResponse.response"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                        :placeholder="fieldResponse.placeholder"
                        :disabled="submitted"
                    />
                </template>

                <template v-else-if="fieldResponse.survey_field.type === 'textarea'">
                    <textarea
                        :id="fieldResponse.id"
                        v-model="fieldResponse.response"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                        :placeholder="fieldResponse.placeholder"
                        :disabled="submitted"
                    ></textarea>
                </template>

                <template v-else-if="fieldResponse.survey_field.type === 'radio'">
                    <div class="mt-2 flex justify-between items-center">
                        <label v-for="option in fieldResponse.survey_field.options" :key="option" class="inline-flex items-center mr-4">
                            <input
                                type="radio"
                                :id="`${fieldResponse.id}_${option}`"
                                :value="option"
                                v-model="fieldResponse.response"
                                class="form-radio text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 mr-2"
                                :disabled="submitted"
                            />
                            <span class="text-gray-700 dark:text-gray-200">{{ option }}</span>
                        </label>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-center my-6">
                <primary-link :to="`/surveys/${$route.params.surveyId}`">Back</primary-link>
            </div>
        </form>
    </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import { getSurveyResponseDetails } from '../../api';
import PrimaryLink from '../../components/PrimaryLink.vue';
import { AxiosError } from 'axios';
import { useRoute, onBeforeRouteUpdate } from 'vue-router';

const route = useRoute();
const submitted = ref(true);
const response = ref({
    respondent_name: '',
    respondent_email: '',
    survey: {
        title: '',
        description: '',
    },
    field_responses: [],
});

onBeforeRouteUpdate(async (to, from) => {
    if (to.params.surveyId && to.params.responseId) {
        await fetchSurveyResponse(to.params.surveyId, to.params.responseId);
    }
});

onMounted(async () => {
    console.log(route.params);
    if (route.params.surveyId && route.params.responseId) {
        await fetchSurveyResponse(route.params.surveyId, route.params.responseId);
    }
});

const fetchSurveyResponse = async (surveyId, responseId) => {
    try {
        response.value = (await getSurveyResponseDetails(surveyId, responseId)).data;
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};
</script>
