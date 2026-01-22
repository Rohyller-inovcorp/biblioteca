<script setup>
import { router, Link, Head } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    title: String,      // Ej: "Autores"
    data: Object,       // coleçao
    columns: Array,     // configuração das colunas
    filters: Object,    // Filtros atuales (search, sortBy, sortDir)
    baseRoute: String,  // Ej: "authors"
})

function sort(column) {
    const dir = props.filters.sortBy === column && props.filters.sortDir === 'asc'
        ? 'desc' : 'asc';

    router.get(route(`${props.baseRoute}.index`), {
        ...props.filters,
        sortBy: column,
        sortDir: dir
    }, { preserveState: true, replace: true });
}

const showDeleteModal = ref(false)
const itemToDelete = ref(null)

function confirmDelete(item) {
    itemToDelete.value = item
    showDeleteModal.value = true
}

function deleteItem() {
    router.delete(route(`${props.baseRoute}.destroy`, itemToDelete.value.id), {
        preserveScroll: true,
        onSuccess: () => showDeleteModal.value = false,
    })
}
function goToPage(link) {
    if (link.url) {
        router.visit(link.url, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        })
    }
}
</script>

<template>

    <Head :title="title" />

    <div class="p-6 w-full max-w-full">
        <h1 class="text-2xl font-bold mb-4">Gestão de {{ title }}</h1>

        <div class="flex flex-wrap gap-4 mb-4 items-center">
            <Link :href="route(`${baseRoute}.create`)" class="btn btn-primary">Criar {{ title }}</Link>

            <div class="flex-1">
                <slot name="search-input"></slot>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra w-full min-w-[768px] md:min-w-full"
                v-if="data.data && data.data.length > 0">
                <thead>
                    <tr>
                        <th v-for="col in columns" :key="col.key"
                            :class="[col.width, { 'cursor-pointer select-none': col.sortable }]"
                            @click="col.sortable ? sort(col.key) : null">
                            {{ col.label }}
                            <span v-if="filters.sortBy === col.key">
                                {{ filters.sortDir === 'asc' ? '↑' : '↓' }}
                            </span>
                        </th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in data.data" :key="item.id">
                        <td v-for="col in columns" :key="col.key" :class="col.width">
                            <template v-if="col.type === 'image'">
                                <img v-if="item[col.key]" :src="`/storage/${item[col.key]}`"
                                    class="w-48 h-48 object-cover rounded" />
                                <span v-else class="text-gray-400">—</span>
                            </template>

                            <template v-else-if="col.type === 'relation'">
                                {{ item[col.relation]?.name || '—' }}
                            </template>

                            <template v-else-if="col.type === 'collection'">
                                <ul v-if="item[col.key]?.length">
                                    <li v-for="subItem in item[col.key]" :key="subItem.id"
                                        class="text-xs whitespace-nowrap">
                                        • {{ subItem.name }}
                                    </li>
                                </ul>
                                <span v-else class="text-gray-400">—</span>
                            </template>

                            <template v-else>
                                {{ col.format === 'price' ? Number(item[col.key]).toFixed(2) + ' €' : item[col.key] }}
                            </template>
                        </td>
                        <td class="flex-col gap-3 ">
                            <Link :href="route(`${baseRoute}.edit`, item.id)" class="btn btn-primary w-20">Editar</Link>
                            <button @click="confirmDelete(item)" class="btn btn-error w-20 mt-3">Apagar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
            <p>
                <template v-if="data.data && data.data.length === 0">
                    Nenhum resultado encontrado.
                </template>
                <template v-else-if="!data.data">
                    Carregando...
                </template>
            </p>
            <div v-if="data.links && data.links.length > 3"
                class="mt-6 flex flex-col items-center justify-center gap-6 p-4 w-full">
                <div class="text-sm text-gray-600 text-center">
                    <span class="font-semibold">{{ data.to || 0 }}</span> de
                    <span class="font-semibold">{{ data.total }}</span> resultados
                </div>

                <nav class="flex flex-wrap justify-center gap-1">
                    <button v-for="(link, index) in data.links" :key="index" @click="goToPage(link)"
                        class="px-3 py-1 text-sm rounded-md transition-all duration-200 border" :class="{
                            'bg-primary text-white border-primary shadow-md': link.active,
                            'bg-white text-gray-700 hover:bg-gray-50 border-gray-300': !link.active && link.url,
                            'text-gray-300 border-gray-100 cursor-not-allowed': !link.url
                        }" :disabled="!link.url || link.active" v-html="link.label">
                    </button>
                </nav>
            </div>
        </div>

        <div v-if="showDeleteModal" class="modal modal-open">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Confirmar eliminação</h3>
                <p class="py-4">Tem a certeza que deseja eliminar este registro?</p>
                <div class="modal-action">
                    <button class="btn btn-error p-2 mr-2" @click="deleteItem">Sim, eliminar</button>
                    <button class="btn btn-outline" @click="showDeleteModal = false">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</template>