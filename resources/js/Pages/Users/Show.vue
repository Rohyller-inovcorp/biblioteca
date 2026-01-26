<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    user: Object,
    isAdmin: Boolean
})
const getInitials = (name) => {
    if (!name) return '?'

    const words = name.trim().split(' ').filter(word => word.length > 0)

    if (words.length === 1) {
        return words[0][0].toUpperCase()
    } else {
        return (words[0][0] + words[words.length - 1][0]).toUpperCase()
    }
}
</script>

<template>

    <Head :title="`Perfil de ${user.name}`" />

    <div class="min-h-screen  py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto">

            <div class="mb-6">
                <Link :href="route('dashboard')" class="btn btn-ghost btn-sm">
                    ← Voltar ao Dashboard
                </Link>
            </div>

            <div class="overflow-hidden rounded-2xl p-6 mb-6">
                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="avatar">
                        <div
                            class="w-28 h-28  overflow-hidden ring ring-primary ring-offset-base-100 ring-offset-2 bg-base-100">
                            <img v-if="user?.profile_photo_path" :src="'/storage/' + user?.profile_photo_path"
                                class="w-full h-full object-cover block" />

                            <div v-else
                                class="w-full h-full bg-primary text-white flex items-center justify-center text-3xl font-bold  p-8">
                                {{ getInitials(user.name) }}
                            </div>
                        </div>
                    </div>



                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl font-extrabold text-gray-100">{{ user.name }}</h1>
                        <p class="text-lg text-gray-500">{{ user.email }}</p>
                        <div class="mt-3 flex flex-wrap justify-center md:justify-start gap-2">
                            <span class="badge badge-secondary badge-md uppercase font-bold text-xs shadow-sm">
                                {{ user.role }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl p-8">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3 text-gray-800">
                        <div class="p-2 bg-primary/10 rounded-lg text-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332-4.5-1.253" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold italic">Histórico de Movimentos</h3>
                    </div>

                    <div class="text-sm font-medium text-gray-500">
                        Total: {{ user.loans.length }} requisições
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="rounded-l-lg">Livro</th>
                                <th>Data Requisição</th>
                                <th>Data Prevista</th>
                                <th>Data Devolução</th>
                                <th class="rounded-r-lg text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="loan in user.loans" :key="loan.id" class="hover:bg-gray-50 transition-colors">
                                <td class="py-4">
                                    <Link :href="route('books.show', loan.book.id)"
                                        class="font-bold text-primary hover:underline">
                                        {{ loan.book.name }}
                                    </Link>
                                </td>
                                <td class="text-primary">{{ new Date(loan.loan_date).toLocaleDateString() }}</td>
                                <td class="text-primary">{{ new Date(loan.expected_return_date).toLocaleDateString() }}
                                </td>
                                <td>
                                    <span v-if="loan.actual_return_date" class="font-medium text-gray-500">
                                        {{ new Date(loan.actual_return_date).toLocaleDateString() }}
                                    </span>
                                    <span v-else class="text-gray-500 italic">Em aberto</span>
                                </td>
                                <td class="text-center">
                                    <span v-if="loan.actual_return_date"
                                        class="badge badge-success text-white badge-sm py-3 px-4">
                                        Devolvido
                                    </span>
                                    <span v-else
                                        class="badge badge-warning text-white badge-sm py-3 px-4 animate-pulse">
                                        Ativo
                                    </span>
                                </td>
                            </tr>

                            <tr v-if="user.loans.length === 0">
                                <td colspan="5" class="text-center py-16">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-200"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-gray-400 italic font-medium">Este cidadão ainda não tem registos
                                            de requisição.</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>