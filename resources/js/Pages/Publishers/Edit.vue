<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SimpleEntityForm from '@/Components/SimpleEntityForm.vue'

defineOptions({ layout: AppLayout })
defineProps({ publisher: Object })
</script>

<template>
  <SimpleEntityForm 
    :entity="publisher" 
    baseRoute="publishers" 
    title="Editora" 
    imageField="logo" 
  />
</template>