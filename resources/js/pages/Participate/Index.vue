<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-2">{{ survey.title }}</h1>
        <p class="mb-6">{{ survey.description }}</p>

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

            <div class="mt-6 mb-6" v-for="(field, index) in survey.fields" :key="field.id">
                <label :for="field.id" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">{{ field.name }}</label>

                <template v-if="field.type === 'text'">
                    <input
                        :id="field.id"
                        type="text"
                        v-model="response.responses[index].response"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                        :placeholder="field.placeholder"
                        :disabled="submitted"
                    />
                </template>

                <template v-else-if="field.type === 'textarea'">
                    <textarea
                        :id="field.id"
                        v-model="response.responses[index].response"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                        :placeholder="field.placeholder"
                        :disabled="submitted"
                    ></textarea>
                </template>

                <template v-else-if="field.type === 'radio'">
                    <div class="mt-2 flex justify-between items-center">
                        <label v-for="option in field.options" :key="option" class="inline-flex items-center mr-4">
                            <input
                                type="radio"
                                :id="`${field.id}_${option}`"
                                :value="option"
                                v-model="response.responses[index].response"
                                class="form-radio text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 mr-2"
                                :disabled="submitted"
                            />
                            <span class="text-gray-700 dark:text-gray-200">{{ option }}</span>
                        </label>
                    </div>
                </template>
            </div>

            <div class="flex items-center justify-center mb-6">
                <primary-button type="submit" @click.prevent="submitForm" class="mt-6" :disabled="submitted">{{ submitted ? 'Submitted successfully' : 'Save' }}</primary-button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import { getSurveyForParticipant, submitSurveyResponse } from '../../api';
import PrimaryButton from '../../components/PrimaryButton.vue';
import { AxiosError } from 'axios';
import { useRoute } from 'vue-router';
import { onBeforeRouteUpdate } from 'vue-router'

const route = useRoute();
const submitted = ref(false);
const survey = ref({});
const response = ref({
    respondent_name: '',
    respondent_email: '',
    responses: [],
});

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
        survey.value = (await getSurveyForParticipant(id)).data;

        survey.value.fields.forEach(field => {
            response.value.responses.push({
                survey_field_id: field.id,
                response: '',
            });
        });
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};

const submitForm = async () => {
    try {
        if (submitted.value) {
            alert('You have already submitted this survey.');
            return;
        }

        const hasEmptyFieldResponse = response.value.responses.some(r => r.response.trim() === '');

        if (response.value.respondent_name.trim() === '' || response.value.respondent_email.trim() === '' || hasEmptyFieldResponse) {
            alert('Please fill in all required fields.');
            return;
        }

        if (!confirm('Are you sure you want to submit your response?')) {
            return;
        }

        await submitSurveyResponse(route.params.id, response.value);
        alert(`Survey response submitted successfully.`);
        submitted.value = true;
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};
</script>
