<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    book: Object,
    isAdmin: Boolean
})
</script>

<template>
    <div class="py-12 px-4 max-w-7xl mx-auto">

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 bg-gray-100 flex justify-center p-6">
                    <img v-if="book.cover_image" :src="'/storage/' + book.cover_image" class="rounded-lg max-h-96 object-cover" />
                    <img v-else src="/storage/no_available.png" class="rounded-lg max-h-26" />
                </div>

                <div class="md:w-2/3 p-8">
                    <div class="flex justify-between items-start">
                        <h1 class="text-4xl font-extrabold text-gray-900">{{ book.name }}</h1>
                        <Link :href="route('books.index')" class="btn btn-ghost btn-sm text-gray-900">← Voltar</Link>
                    </div>

                    <p class="text-xl text-gray-800 font-medium mt-2">
                        {{ book.authors && book.authors.length > 1 ? 'Autores:' : 'Autor:' }}
                        {{book.authors?.map(a => a.name).join(', ') || 'N/A'}}
                    </p>

                    <div class="mt-4 flex gap-4 text-sm text-gray-700">
                        <span><strong>ISBN:</strong> {{ book.isbn || 'N/A' }}</span>

                        <span v-if="book.publisher">
                            <strong>Editora:</strong> {{ book.publisher.name }}
                        </span>
                    </div>
                    <div class="mt-4">
                        <span class="text-1xl font-bold text-gray-700">
                            Price: {{ Number(book.price).toFixed(2) }} €
                        </span>
                    </div>

                    <div class="divider"></div>

                    <div class="flex items-center gap-4">
                        <h3 class="font-bold text-gray-700">Disponibilidade:</h3>
                        <div v-if="book.loans.some(l => !l.actual_return_date)"
                            class="badge badge-error py-3 text-white">
                            Atualmente Requisitado
                        </div>
                        <div v-else class="badge badge-success py-3 text-white">
                            Livre para Requisição
                        </div>
                    </div>
                    <div v-if="book.bibliography" class="mt-4">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Bibliografia</h2>
                        <p class="text-gray-700 whitespace-pre-line">{{ book.bibliography }}</p>
                    </div>
                </div>
            </div>

        </div>

        <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100">
            <div class="flex items-center gap-3 mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-800">Histórico de Requisições</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr class="bg-base-200">
                            <th>#</th>
                            <th v-if="isAdmin">Cidadão</th>
                            <th>Data Início</th>
                            <th>Data Prevista</th>
                            <th>Data Devolução</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(loan, index) in book.loans" :key="loan.id" class="hover [&:nth-child(odd)]:text-gray-900">
                            <td class="font-mono text-xs">{{ book.loans.length - index }}</td>

                            <td v-if="isAdmin">
                                <div class="flex items-center gap-2">
                                    <div class="font-bold">{{ loan.user?.name }}</div>
                                </div>
                            </td>

                            <td>{{ new Date(loan.loan_date).toLocaleDateString() }}</td>
                            <td>{{ new Date(loan.expected_return_date).toLocaleDateString() }}</td>
                            <td>
                                <span v-if="loan.actual_return_date">
                                    {{ new Date(loan.actual_return_date).toLocaleDateString() }}
                                </span>
                                <span v-else class="text-gray-600 italic">Aguardando...</span>
                            </td>

                            <td>
                                <div v-if="loan.actual_return_date" class="badge badge-success badge-outline badge-sm font-bold">
                                    Devolvido ({{ loan.days_elapsed }} dias)
                                </div>
                                <div v-else class="badge badge-warning badge-sm">
                                    Em posse
                                </div>
                            </td>
                        </tr>

                        <tr v-if="book.loans.length === 0">
                            <td :colspan="isAdmin ? 6 : 5" class="text-center py-10 text-gray-400 italic">
                                Este livro nunca foi requisitado.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</template>