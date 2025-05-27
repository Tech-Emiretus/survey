<template>
    <div>
        <form-builder v-if="currentPage === 'FormBuilder'" />
    </div>
</template>

<script lang='ts' setup>
    import { onMounted, ref } from 'vue';
    import FormBuilder from '../pages/FormBuilder.vue';

    const currentPage = ref('');

    onMounted(() => {
        const page = window.location.pathname.split('/').pop();
        currentPage.value = page === 'form-builder' ? 'FormBuilder' : '';
    });
</script>
