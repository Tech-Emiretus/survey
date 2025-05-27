<template>
    <div class="flex h-screen">
        <!-- Left: Drop area (larger) -->
        <div class="flex-1 bg-gray-100 p-6 mr-4 rounded-lg shadow-md">
            <draggable
                v-model="formFields"
                :group="{ name: 'fields', pull: false, put: true }"
                @add="onAddField"
                item-key="id"
                class="space-y-3 min-w-full min-h-full"
            >
                <template #item="{ element }">
                    <div class="bg-white p-4 rounded shadow">
                        <p>{{ element.label }}</p>
                    </div>
                </template>
            </draggable>
        </div>

        <!-- Right: Draggable field types (smaller) -->
        <div class="w-100 bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-semibold mb-4">Field Types</h3>
            <draggable
                :list="fieldTypes"
                :group="{ name: 'fields', pull: 'clone', put: false }"
                :clone="(original) => ({ ...original })"
                item-key="id"
                class="space-y-3"
            >
                <template #item="{ element }">
                    <li class="cursor-move bg-gray-200 px-4 py-2 rounded">
                        {{ element.label }}
                    </li>
                </template>
            </draggable>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref } from 'vue'
import Draggable from 'vuedraggable'

// List of available field types
const fieldTypes = ref([
    { id: 1, label: 'Text Input', type: 'text' },
    { id: 2, label: 'Checkbox', type: 'checkbox' },
    { id: 3, label: 'Radio Button', type: 'radio' },
    { id: 4, label: 'Dropdown', type: 'dropdown' }
])

// List of fields in the form builder area
const formFields = ref([])

// Handle adding a field to the form builder area
function onAddField(event: any) {
    // Clone the dragged field type
    // const newField = { ...event.item, id: Date.now() }
    // formFields.value.splice(event.newIndex, 0, newField)
}
</script>
