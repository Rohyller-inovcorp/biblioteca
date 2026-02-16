<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
    data: Object,
    filters: Object
})

const statusFilter = ref(props.filters.status)

function goToPage(link) {
    if (link.url) {
        router.visit(link.url, {
            preserveState: true,
            preserveScroll: true,
            replace: true
        })
    }
}

function applyFilter() {
    router.get(route('orders.index'), { status: statusFilter.value }, { preserveState: true })
}
</script>

<template>
    <div class="max-w-7xl mx-auto py-10 px-4">

        <h1 class="text-3xl font-extrabold mb-6">Gestión de Encomendas</h1>

        <!-- Filtro -->
        <div class="mb-4 flex items-center gap-2">
            <label class="font-semibold text-gray-300">Estado:</label>
            <select v-model="statusFilter" class="select select-bordered select-sm p-2" @change="applyFilter">
                <option value="">Todos</option>
                <option value="pending">Pendentes</option>
                <option value="paid">Pagas</option>
            </select>
        </div>

        <!-- Estados -->
        <p class="text-sm text-gray-500 mb-4">
            <template v-if="data.data && data.data.length === 0">
                Nenhum resultado encontrado.
            </template>
            <template v-else-if="!data.data">
                Carregando...
            </template>
        </p>

        <!-- Tabela -->
        <table class="table table-zebra w-full text-base-content">
            <thead class="bg-base-200 text-base-content">
                <tr>
                    <th>#</th>
                    <th>Cidadão</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Data</th>
                </tr>
            </thead>

            <tbody class="text-base-content">
                <tr v-for="order in data.data" :key="order.id">
                    <td>{{ order.id }}</td>
                    <td class="font-bold">{{ order.user.name }}</td>
                    <td>{{ order.total }} €</td>
                    <td>
                        <span :class="{
                            'text-yellow-500 font-bold': order.status === 'pending',
                            'text-green-500 font-bold': order.status === 'paid',
                            'text-red-500 font-bold': order.status === 'canceled'
                        }">
                            {{ order.status }}
                        </span>
                    </td>
                    <td>{{ new Date(order.created_at).toLocaleDateString() }}</td>
                </tr>
            </tbody>
        </table>



        <!-- Paginação -->
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
</template>
