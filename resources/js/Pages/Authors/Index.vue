<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const props = defineProps({
    authors: Object,
    filters: Object,
})

const columns = [
    { label: 'Nome', key: 'name', sortable: true, width: 'w-1/2' },
    { label: 'Foto', key: 'photo', type: 'image', sortable: false, width: 'w-2/5' },
]

function handleSearch(e) {
    router.get(route('authors.index'), { 
        ...props.filters, 
        search: e.target.value 
    }, { preserveState: true, replace: true });
}
</script>

<template>
    <DataTable 
        title="Autores" 
        :data="authors" 
        :columns="columns" 
        :filters="filters"
        baseRoute="authors"
    >
        <template #search-input>
            <input type="text" :value="filters.search" @input="handleSearch" 
                   placeholder="Pesquisar autor..." class="input input-bordered w-full max-w-xs" />
        </template>
    </DataTable>
</template>