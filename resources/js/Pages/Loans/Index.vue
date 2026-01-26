<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import { Head, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'

defineOptions({ layout: AppLayout })

const props = defineProps({
    loans: Object, 
    stats: Object
})


const allLoans = ref([...props.loans.data])
const nextUrl = ref(props.loans.next_page_url)
const loadingMore = ref(false)
const loadMoreTrigger = ref(null)

const loadMore = () => {
    if (!nextUrl.value || loadingMore.value) return

    loadingMore.value = true
    router.get(nextUrl.value, {}, {
        preserveState: true,
        preserveScroll: true,
        only: ['loans'],
        onSuccess: (page) => {
            allLoans.value = [...allLoans.value, ...page.props.loans.data]
            nextUrl.value = page.props.loans.next_page_url
            loadingMore.value = false
        }
    })
}

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) loadMore()
    }, { threshold: 0.5 })

    if (loadMoreTrigger.value) observer.observe(loadMoreTrigger.value)
})


const showConfirmModal = ref(false)
const selectedLoan = ref(null)
const processing = ref(false)

const openConfirmModal = (loan) => {
    selectedLoan.value = loan
    showConfirmModal.value = true
}

const confirmarRececao = () => {
    if (!selectedLoan.value) return
    
    processing.value = true
    router.patch(route('loans.update', selectedLoan.value.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            showConfirmModal.value = false
            const loanIndex = allLoans.value.findIndex(l => l.id === selectedLoan.value.id)
            if (loanIndex !== -1) {
                allLoans.value[loanIndex].actual_return_date = new Date().toISOString()
            }
            selectedLoan.value = null
            processing.value = false
        },
        onError: () => {
            processing.value = false
        }
    })
}
</script>

<template>
    <Head title="Requisições" />

    <div class="py-6 px-4 mx-auto pb-20">
        <div v-if="$page.props.auth.user.role === 'admin'" class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stats shadow bg-primary text-primary-content">
                <div class="stat">
                    <div class="stat-title text-primary-content opacity-90">Requisições Ativas</div>
                    <div class="stat-value text-2xl">{{ stats.active }}</div>
                </div>
            </div>
            <div class="stats shadow bg-secondary text-secondary-content">
                <div class="stat">
                    <div class="stat-title text-secondary-content opacity-90">Últimos 30 dias</div>
                    <div class="stat-value text-2xl">{{ stats.last_30_days }}</div>
                </div>
            </div>
            <div class="stats shadow bg-accent text-accent-content">
                <div class="stat">
                    <div class="stat-title text-accent-content opacity-90">Entregues Hoje</div>
                    <div class="stat-value text-2xl">{{ stats.delivered_today }}</div>
                </div>
            </div>
        </div>

        <div class="overflow-hidden">
            <h2 class="text-2xl font-bold mb-6 text-gray-200">Histórico de Requisições</h2>
            
            <div class="overflow-x-auto bg-base-100 rounded-xl shadow">
                <table class="table table-zebra w-full text-sm">
                    <thead>
                        <tr class="bg-base-200">
                            <th># Seq.</th>
                            <th>Livro</th>
                            <th v-if="$page.props.auth.user.role === 'admin'">Cidadão</th>
                            <th>Image</th>
                            <th>Início</th>
                            <th>Prevista</th>
                            <th>Estado</th>
                            <th v-if="$page.props.auth.user.role === 'admin'">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="loan in allLoans" :key="loan.id" class="hover">
                            <td class="font-mono text-xs font-bold">{{ loan.sequential_number }}</td>
                            <td class="font-bold">{{ loan.book?.name }}</td>
                            <td v-if="$page.props.auth.user.role === 'admin'">{{ loan.user?.name }}</td>
                            <td class="w-20">
                                <div class="avatar">
                                    <div class="w-32 h-32 rounded-lg">
                                        <img :src="'/storage/' + (loan.user?.profile_photo_path || 'no_available2.png')" :alt="loan.user?.profile_photo_path" />
                                    </div>
                                </div>
                            </td>
                            <td>{{ new Date(loan.loan_date).toLocaleDateString() }}</td>
                            <td>{{ new Date(loan.expected_return_date).toLocaleDateString() }}</td>
                            <td>
                                <div v-if="loan.actual_return_date" class="flex flex-col">
                                    <div class="badge badge-success badge-sm text-[10px] text-white">Devolvido</div>
                                    <span class="text-[9px] opacity-80 italic">{{ loan.days_elapsed }} dias</span>
                                </div>
                                <div v-else class="badge badge-warning badge-sm text-[10px] text-white whitespace-nowrap">Em posse</div>
                            </td>
                            <td v-if="$page.props.auth.user.role === 'admin'">
                                <button 
                                    v-if="!loan.actual_return_date" 
                                    @click="openConfirmModal(loan)" 
                                    class="btn btn-primary btn-xs normal-case p-2"
                                >
                                    Receber
                                </button>
                                <span v-else class="text-[10px] opacity-90 italic p-2">Finalizado</span>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div ref="loadMoreTrigger" class="flex justify-center p-8 w-full">
                    <span v-if="loadingMore" class="loading loading-spinner text-primary"></span>
                    <span v-else-if="!nextUrl" class="text-xs opacity-30">Fim do histórico</span>
                </div>
            </div>
        </div>
    </div>

    <dialog class="modal" :class="{ 'modal-open': showConfirmModal }">
        <div class="modal-box border-t-4 border-primary">
            <h3 class="font-bold text-lg text-primary flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Confirmar Receção
            </h3>
            <div class="py-4">
                <p class="text-sm">Confirmar devolução do livro:</p>
                <p class="font-bold text-lg">"{{ selectedLoan?.book?.name }}"</p>
                <div class="mt-4 p-3 bg-base-200 rounded-lg text-xs">
                    <p><strong>Cidadão:</strong> {{ selectedLoan?.user?.name }}</p>
                    <p class="mt-2"><strong>Requisitado em:</strong> {{ selectedLoan ? new Date(selectedLoan.loan_date).toLocaleDateString() : '' }}</p>
                </div>
            </div>
            <div class="modal-action">
                <button @click="showConfirmModal = false" class="btn btn-ghost btn-sm" :disabled="processing">Cancelar</button>
                <button @click="confirmarRececao" class="btn btn-primary btn-sm p-2" :disabled="processing">
                    <span v-if="processing" class="loading loading-spinner loading-xs"></span>
                    Confirmar Entrega
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop" @click="showConfirmModal = false">
            <button>close</button>
        </form>
    </dialog>
</template>