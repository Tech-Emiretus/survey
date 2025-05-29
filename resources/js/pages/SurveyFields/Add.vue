<template>
    <div class="p-6">
        <form class="flex justify-between items-center w-full min-w-full">
            <div class="flex items-center">
                <div class="mb-6 mr-2 flex-1">
                    <label for="name" class="mb-2 block font-bold text-gray-700 dark:text-gray-200">Name</label>
                    <input
                        id="name"
                        type="text"
                        v-model="surveyField.name"
                        class="focus:shadow-outline w-full appearance-none rounded border px-3 py-2 leading-tight text-gray-700 shadow focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    />
                </div>
                <div class="mb-6 mr-2 ml-2 flex-shrink-0">
                    <label for="type" class="mb-2 block font-bold text-gray-700 dark:text-gray-200">Type</label>
                    <select
                        id="type"
                        v-model="surveyField.type"
                        class="focus:shadow-outline w-full appearance-none rounded border px-3 py-2 leading-tight text-gray-700 shadow focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    >
                        <option value="text">Text</option>
                        <option value="textarea">Textarea</option>
                        <option value="radio">Radio</option>
                    </select>
                </div>
                <div class="mb-6 ml-2" v-if="surveyField.type === 'radio'">
                    <label for="options" class="mb-2 block font-bold text-gray-700 dark:text-gray-200">Options (comma separated)</label>
                    <input
                        id="options"
                        type="text"
                        v-model="surveyField.options"
                        class="focus:shadow-outline w-full appearance-none rounded border px-3 py-2 leading-tight text-gray-700 shadow focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100"
                    />
                </div>
            </div>
            <div>
                <primary-button
                    class="mt-6"
                    @click.prevent="addField"
                >
                    Add Field
                </primary-button>
            </div>
        </form>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import { createSurveyField } from '@/api';
import { AxiosError } from 'axios';

const props = defineProps({
    survey: {
        type: Object,
        required: true,
    },
});

const emits = defineEmits(['fieldAdded']);

const surveyField = ref({
    name: '',
    type: 'text',
    options: 'Very Poor, Poor, Fair, Good, Very Good, Excellent',
});

const addField = async () => {
    if (!confirm('Are you sure you want to add a new field?')) {
        return;
    }

    if (!surveyField.value.name || !surveyField.value.type) {
        alert('Please fill in all required fields.');
        return;
    }

    const newField = {
        ...surveyField.value,
        options: surveyField.value.type === 'radio' && surveyField.value.options ? surveyField.value.options.split(',').map(option => option.trim()) : [],
    };

    try {
        await createSurveyField(props.survey.id, newField);
        alert('New field added successfully.');

        // Reset the form
        surveyField.value = {
            name: '',
            type: 'text',
            options: 'Very Poor, Poor, Fair, Good, Very Good, Excellent',
        };

        emits('fieldAdded', newField);
    } catch (error) {
        console.error(error);

        if (error instanceof AxiosError) {
            alert(error.response?.data.message);
        }
    }
}
</script>
