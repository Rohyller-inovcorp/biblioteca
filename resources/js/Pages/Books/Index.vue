<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import { router } from '@inertiajs/vue3'

defineOptions({ layout: AppLayout })

const props = defineProps({
    books: Object,
    filters: Object,
})

// Configuración de columnas para Libros
const columns = [
    { label: 'ISBN', key: 'isbn', sortable: true, width: 'w-1/12' },
    { label: 'Nome', key: 'name', sortable: true, width: 'w-3/12' },
    { label: 'Editora', key: 'publisher_id', type: 'relation', relation: 'publisher', width: 'w-2/12' },
    { label: 'Autores', key: 'authors', type: 'collection', width: 'w-2/12' },
    { label: 'Preço', key: 'price', sortable: true, format: 'price', width: 'w-1/12' },
    { label: 'Capa', key: 'cover_image', type: 'image', width: 'w-2/12' },
]

function handleSearch(e) {
    router.get(route('books.index'), { 
        ...props.filters, 
        search: e.target.value 
    }, { preserveState: true, replace: true });
}
</script>

<template>
    <DataTable 
        title="Livros" 
        :data="books" 
        :columns="columns" 
        :filters="filters"
        baseRoute="books"
    >
        <template #search-input>
            <input 
                type="text" 
                :value="filters.search" 
                @input="handleSearch" 
                placeholder="Pesquisar por nome ou ISBN..." 
                class="input input-bordered w-full max-w-sm" 
            />
        </template>
    </DataTable>
</template>