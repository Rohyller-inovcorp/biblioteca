<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import SimpleEntityForm from '@/Components/SimpleEntityForm.vue'

defineOptions({ layout: AppLayout })
defineProps({ author: Object })
</script>

<template>
  <SimpleEntityForm 
    :entity="author" 
    baseRoute="authors" 
    title="Autor" 
    imageField="photo" 
  />
</template>