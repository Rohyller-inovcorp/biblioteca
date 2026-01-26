<script setup>
import DataTable from '@/Components/DataTable.vue'
import { router, useForm, usePage, Link } from '@inertiajs/vue3'
import { ref, watch} from 'vue'
const toastMessage = ref(null)

const props = defineProps({
    books: Object,
    filters: Object,
})

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
const loanForm = useForm({
    book_id: null,
});
const showLoanModal = ref(false);
const selectedBook = ref(null);

const openLoanModal = (book) => {
    selectedBook.value = book;
    showLoanModal.value = true;
};

const confirmLoan = () => {
    if (!selectedBook.value) return;

    loanForm.book_id = selectedBook.value.id;

    loanForm.post(route('loans.store'), {
        preserveScroll: true,
        onSuccess: () => {
            const bookIndex = props.books.data.findIndex(b => b.id === selectedBook.value.id);
            if (bookIndex !== -1) {
                props.books.data[bookIndex].loans = [{ id: 'temp' }];
            }
            showLoanModal.value = false;
            selectedBook.value = null;
        },
        onError: (errors) => {
            showLoanModal.value = false;
            if (errors.message) {
                toastMessage.value = errors.message;

                setTimeout(() => {
                    toastMessage.value = null;
                }, 5000);
            }
        },
    });
};
</script>

<template>
    <DataTable title="Livros" :data="books" :columns="columns" :filters="filters" baseRoute="books" row-route="books.show">
        <template #loans>
            <Link :href="route('loans.index')" class="btn btn-info">Emprestimos</Link>
        </template>
        <template #search-input>
            <input type="text" :value="filters.search" @input="handleSearch" placeholder="Pesquisar Nome ou ISBN"
                class="input input-bordered input-lg w-full px-6 max-w-md placeholder:text-sm"  />
        </template>
       
        <template #extra_actions="{ item }">
            <div class="flex flex-col gap-1 items-center">
                <span v-if="item.loans?.length > 0"
                    class="badge badge-error badge-outline badge-xs py-2 w-20 font-bold uppercase text-[13px] mb-4">
                    Ocupado
                </span>
                <span v-else
                    class="badge badge-success badge-outline badge-xs py-2 w-20 font-bold uppercase text-[13px] mb-4">
                    Livre
                </span>

                <button v-if="!item.loans || item.loans?.length === 0" @click="openLoanModal(item)"
                    class="btn btn-secondary btn-xs w-20" :disabled="item.loans?.length > 0 || loanForm.processing">
                    Requisitar
                </button>
            </div>
        </template>
    </DataTable>
    <div v-if="showLoanModal" class="modal modal-open">
        <div class="modal-box">
            <h3 class="font-bold text-lg text-primary">Confirmar Requisição</h3>

            <div class="py-4">
                <p>Deseja requisitar o livro: <span class="font-bold">{{ selectedBook?.name }}</span>?</p>
                <p class="text-sm text-gray-500 mt-2">
                    • O prazo de entrega será de <strong>5 dias.</strong> <br>
                    • Receberá um email de confirmação com os detalhes.
                </p>
            </div>

            <div class="modal-action">
                <button class="btn btn-primary p-2" @click="confirmLoan" :disabled="loanForm.processing">
                    <span v-if="loanForm.processing" class="loading loading-spinner"></span>
                    Confirmar Requisição
                </button>

                <button class="btn btn-outline p-2" @click="showLoanModal = false" :disabled="loanForm.processing">
                    Cancelar
                </button>
            </div>
        </div>
    </div>
    <div v-if="toastMessage" class="toast toast-top toast-end z-[999] mt-16">
        <div class="alert alert-error shadow-lg border-none">
            <div class="flex items-center gap-2 text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current h-6 w-6 shrink-0" fill="none"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-bold text-sm">{{ toastMessage }}</span>
                <button @click="toastMessage = null" class="btn btn-xs btn-circle btn-ghost">✕</button>
            </div>
        </div>
    </div>
</template>