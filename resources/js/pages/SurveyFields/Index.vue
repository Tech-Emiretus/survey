<template>
    <div class="mt-6">
        <div class="text-center">
            <h1 class="text-2xl font-bold mb-4">Survey Fields for "{{ survey.title }}"</h1>
        </div>

        <add-field :survey="survey" @field-added="handleFieldAdded" />

        <div class="text-center mt-8 mb-4">
            <h2 class="text-xl font-bold mb-4">Fields Preview</h2>
        </div>

        <template v-if="survey.fields.length">
            <div class="mt-6 mb-6 flex w-full items-end align-bottom" v-for="(field, index) in survey.fields" :key="field.id">
                <div class="flex-1 mr-2">
                    <label :for="field.id" class="block text-gray-700 dark:text-gray-200 font-bold mb-2">{{ field.name }}</label>

                    <template v-if="field.type === 'text'">
                        <input
                            :id="field.id"
                            type="text"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                            :placeholder="field.placeholder"
                            disabled="true"
                        />
                    </template>

                    <template v-else-if="field.type === 'textarea'">
                        <textarea
                            :id="field.id"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-gray-100 dark:bg-gray-800 dark:border-gray-600 leading-tight focus:outline-none focus:shadow-outline"
                            :placeholder="field.placeholder"
                            disabled="true"
                        ></textarea>
                    </template>

                    <template v-else-if="field.type === 'radio'">
                        <div class="mt-2 flex justify-between items-center">
                            <label v-for="option in field.options" :key="option" class="inline-flex items-center mr-4">
                                <input
                                    type="radio"
                                    :id="`${field.id}_${option}`"
                                    :value="option"
                                    class="form-radio text-blue-600 dark:text-blue-400 focus:ring-blue-500 dark:focus:ring-blue-400 mr-2"
                                    disabled="true"
                                /><span class="text-gray-700 dark:text-gray-200">{{ option }}</span>
                            </label>
                        </div>
                    </template>
                </div>
                <div class="flex-shrink-0 ml-4 w-24">
                    <primary-button class="w-full" @click.prevent="deleteSurveyField(field.id)">Delete</primary-button>
                </div>
            </div>
        </template>

        <template v-else>
            <p class="text-center text-gray-500 dark:text-gray-400 py-8">No survey responses available</p>
        </template>
    </div>
</template>

<script setup>
import { deleteSurveyField as deleteSurveyFieldApi } from '../../api';
import AddField from './Add.vue';
import PrimaryButton from '../../components/PrimaryButton.vue';
import { AxiosError } from 'axios';

const props = defineProps({
    survey: {
        type: Object,
        required: true
    },
});

const emits = defineEmits(['refreshSurvey']);

const handleFieldAdded = () => {
    emits('refreshSurvey');
};

const deleteSurveyField = async (id) => {
    try {
        if (!confirm('Are you sure you want to delete this survey field?')) {
            return;
        }

        await deleteSurveyFieldApi(props.survey.id, id);
        emits('refreshSurvey');

        alert('Survey field deleted successfully.');
    } catch (error) {
        if (error instanceof AxiosError) {
            console.error('Error deleting survey field:', error.response?.data || error.message);
        } else {
            console.error('Unexpected error:', error);
        }
    }
};
</script>
