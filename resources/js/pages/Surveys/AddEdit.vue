<template>
    <div class="p-6">
        <div class="mb-6 flex justify-between items-center">
            <primary-button v-if="isEditMode" @click.prevent="copyPublicUrl">Copy Public Url</primary-button>
            <primary-link to="/surveys">Back</primary-link>
        </div>

        <form class="max-w-2xl">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Title</label>
                <input
                    id="name"
                    type="text"
                    v-model="survey.title"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter survey title"
                />
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Description</label>
                <textarea
                    id="description"
                    v-model="survey.description"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter survey description"
                ></textarea>
            </div>
            <div class="mb-4">
                <label for="company_id" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Company ID</label>
                <input
                    id="company_id"
                    type="number"
                    v-model="survey.company_id"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                    placeholder="Enter company ID"
                    disabled="true"
                />
            </div>
            <div class="mb-4">
                <label for="start_at" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Start At</label>
                <input
                    id="start_at"
                    type="datetime-local"
                    v-model="survey.start_at"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                />
            </div>
            <div class="mb-4">
                <label for="end_at" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">End At</label>
                <input
                    id="end_at"
                    type="datetime-local"
                    v-model="survey.end_at"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                />
            </div>

            <div class="mb-4" v-if="isEditMode">
                <label for="status" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">Status</label>
                <select
                    id="status"
                    v-model="survey.status"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                >
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="archived">Archived</option>
                </select>
            </div>

            <primary-button type="submit" @click.prevent="submitForm">Save</primary-button>
            <primary-button v-if="isEditMode" type="button" @click.prevent="deleteSurvey" class="ml-2 bg-red-600 dark:bg-red-600">Delete</primary-button>
        </form>
    </div>
</template>

<script setup>
import { inject, onMounted, ref, watch, computed } from 'vue';
import { createSurvey, getSurvey, updateSurvey, deleteSurvey as deleteSurveyApi } from '../../api';
import PrimaryLink from '../../components/PrimaryLink.vue';
import PrimaryButton from '../../components/PrimaryButton.vue';
import { AxiosError } from 'axios';
import { useRouter } from 'vue-router';
import { onBeforeRouteUpdate } from 'vue-router'
import { formatDate } from '../../bootstrap';

const router = useRouter();
const userInfo = inject('userInfo');
const isEditMode = computed(() => !!router.currentRoute.value.params.id);

watch(userInfo, (newValue) => {
    if (newValue && newValue.companies && !survey.value.company_id) {
        survey.value.company_id = newValue.companies[0].id;
    }
});

const existingSurvey = ref({
    title: null,
    description: null,
    company_id: null,
    start_at: null,
    end_at: null,
});

const survey = ref({
    title: null,
    description: null,
    company_id: userInfo.value && userInfo.value.companies ? userInfo.value.companies[0].id : '',
    start_at: null,
    end_at: null,
    status: 'draft',
});

onBeforeRouteUpdate(async (to, from) => {
    if (to.params.id) {
        await fetchExistingSurvey(to.params.id);
    }
});

onMounted(async () => {
    if (router.currentRoute.value.params.id) {
        await fetchExistingSurvey(router.currentRoute.value.params.id);
    }
});

const fetchExistingSurvey = async (id) => {
    try {
        existingSurvey.value = (await getSurvey(id)).data;

        survey.value.title = existingSurvey.value.title;
        survey.value.description = existingSurvey.value.description;
        survey.value.company_id = existingSurvey.value.company.id;
        survey.value.status = existingSurvey.value.status;
        survey.value.start_at = formatDate(existingSurvey.value.start_at);
        survey.value.end_at = formatDate(existingSurvey.value.end_at);
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};

const submitForm = async () => {
    try {
        const payload = {
            ...survey.value,
            start_at: survey.value.start_at ? new Date(survey.value.start_at).toISOString() : null,
            end_at: survey.value.end_at ? new Date(survey.value.end_at).toISOString() : null,
        }

        await (existingSurvey.value.id ? updateSurvey(existingSurvey.value.id, payload) : createSurvey(payload));
        alert(`Survey was ${existingSurvey.value.id ? 'updated' : 'created'} successfully.`);
        router.push('/surveys');
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
};

const deleteSurvey = async () => {
    if (!existingSurvey.value.id || !confirm('Are you sure you want to delete this survey?')) {
        return;
    }

    try {
        await deleteSurveyApi(existingSurvey.value.id);
        alert('Survey was deleted successfully.');
        router.push('/surveys');
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
