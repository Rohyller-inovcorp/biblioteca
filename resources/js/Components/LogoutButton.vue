<script setup>
import { router } from '@inertiajs/vue3';
import DangerButton from '@/Components/DangerButton.vue';

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <DangerButton @click="logout">
        Terminar Sessão
    </DangerButton>
</template>