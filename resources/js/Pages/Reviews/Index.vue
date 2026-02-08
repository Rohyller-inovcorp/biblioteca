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

function updateStatus(reviewId, status) {
    let reason = null
    if (status === 'rejected') {
        reason = prompt('Digite o motivo da rejeição:')
        if (!reason) {
            alert('Por favor, digite o motivo da rejeição.')
            return 
        }
    }

    router.patch(route('reviews.update', reviewId), {
        status: status,
        reason: reason,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            rejectionReason.value = ''
        }
    })
}

function applyFilter() {
    router.get(route('reviews.index'), { status: statusFilter.value }, { preserveState: true })
}
</script>

<template>
<div class="max-w-7xl mx-auto py-10 px-4">

    <h1 class="text-3xl font-extrabold mb-6">Moderación de Reviews</h1>

    <!-- Filtro -->
    <div class="mb-4 flex items-center gap-2">
        <label class="font-semibold text-gray-300">Estado:</label>
        <select v-model="statusFilter" class="select select-bordered select-sm p-2" @change="applyFilter">
            <option value="pending">Pendentes</option>
            <option value="approved">Aprovadas</option>
            <option value="rejected">Rejeitadas</option>
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
    <div v-if="data.data && data.data.length"
         class="overflow-x-auto bg-white rounded-2xl shadow border border-gray-100">

        <table class="table table-zebra w-full">
            <thead>
                <tr class="bg-base-200">
                    <th>#</th>
                    <th>Cidadão</th>
                    <th>Livro</th>
                    <th>Avaliação</th>
                    <th>Comentário</th>
                    <th>Data</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>

            <tbody>
                <tr v-for="review in data.data" :key="review.id">
                    <td class="text-gray-800">{{ review.id }}</td>
                    <td class="text-gray-800 font-bold">{{ review.user.name }}</td>
                    <td class="text-gray-800">{{ review.book.name }}</td>
                    <td>
                        <div class="rating rating-sm">
                            <input v-for="star in 5" :key="star"
                                   type="radio"
                                   class="mask mask-star-2 bg-orange-400"
                                   :checked="star === review.rating"
                                   disabled />
                        </div>
                    </td>
                    <td class="max-w-xs truncate text-gray-800">{{ review.comment }}</td>
                    <td class="text-sm text-gray-600">{{ new Date(review.created_at).toLocaleDateString() }}</td>
                    <td class="text-center space-x-2">
                        <input v-if="review.status === 'pending' && review.status === 'rejected'"
                               v-model="rejectionReason"
                               type="text"
                               placeholder="Motivo da rejeição"
                               class="input input-bordered input-sm w-full max-w-xs mb-2" />
                        <button v-if="review.status === 'pending'"
                                class="btn btn-success btn-xs p-2"
                                @click="updateStatus(review.id, 'approved')">
                            Aprovar
                        </button>
                        <button v-if="review.status === 'pending'"
                                class="btn btn-error btn-xs ml-2 p-2"
                                @click="updateStatus(review.id, 'rejected')">
                            Rejeitar
                        </button>
                        <span v-else class="text-gray-500">{{ review.status }}</span>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Paginação -->
    <div v-if="data.links && data.links.length > 3"
         class="mt-6 flex flex-col items-center justify-center gap-6 p-4 w-full">

        <div class="text-sm text-gray-600 text-center">
            <span class="font-semibold">{{ data.to || 0 }}</span> de
            <span class="font-semibold">{{ data.total }}</span> resultados
        </div>

        <nav class="flex flex-wrap justify-center gap-1">
            <button v-for="(link, index) in data.links" :key="index"
                    @click="goToPage(link)"
                    class="px-3 py-1 text-sm rounded-md transition-all duration-200 border"
                    :class="{
                        'bg-primary text-white border-primary shadow-md': link.active,
                        'bg-white text-gray-700 hover:bg-gray-50 border-gray-300': !link.active && link.url,
                        'text-gray-300 border-gray-100 cursor-not-allowed': !link.url
                    }"
                    :disabled="!link.url || link.active"
                    v-html="link.label">
            </button>
        </nav>
    </div>

</div>
</template>
