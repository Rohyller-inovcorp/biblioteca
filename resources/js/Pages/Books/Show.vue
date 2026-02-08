<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue';

const props = defineProps({
    book: Object,
    isAdmin: Boolean,
    relatedBooks: {
        type: Array,
        default: () => []
    }
})
const page = usePage();

const isModalOpen = ref(false);
const reviewForm = ref({
    rating: 5,
    comment: '',
    book_id: props.book.id,
    loan_id: null
});
const canLeaveReview = computed(() => {
    if (!page.props.auth.user) return false;

    return props.book.loans.some(l =>
        l.user_id === page.props.auth.user.id &&
        l.actual_return_date !== null &&
        !l.review
    );
});
const userLoan = computed(() => {
    if (!page.props.auth.user) return null;

    return props.book.loans.find(l =>
        l.user_id === page.props.auth.user.id &&
        l.actual_return_date !== null &&
        !l.review
    );
});

const openReviewModal = () => {
    if (!userLoan.value) return;

    reviewForm.value.loan_id = userLoan.value.id;
    reviewForm.value.book_id = props.book.id;

    isModalOpen.value = true;
};
const closeReviewModal = () => {
    isModalOpen.value = false;
    reviewForm.value.comment = '';
    reviewForm.value.rating = 5;
};
const submitReview = () => {
    router.post(route('reviews.store'), reviewForm.value, {
        preserveScroll: true,
        onSuccess: () => {
            closeReviewModal();
        }
    });
};

const goToPage = (link) => {
    if (!link.url) return;
    router.get(link.url, {}, {
        preserveScroll: true,
        preserveState: true
    });
};
// Carousel logic
const currentIndex = ref(0)

const canGoPrev = computed(() => currentIndex.value > 0)
const canGoNext = computed(() => currentIndex.value < props.relatedBooks.length - 1)

function prev() {
    if (canGoPrev.value) {
        currentIndex.value--
    }
}

function next() {
    if (canGoNext.value) {
        currentIndex.value++
    }
}

function goToBook(bookId) {
    router.visit(`/books/${bookId}`)
}
</script>

<template>
    <div class="py-12 px-4 max-w-7xl mx-auto">

        <div class="bg-white shadow-xl rounded-2xl overflow-hidden mb-8 border border-gray-100">
            <div class="flex flex-col md:flex-row">
                <div class="md:w-1/3 bg-gray-100 flex justify-center p-6">
                    <img v-if="book.cover_image" :src="'/storage/' + book.cover_image"
                        class="rounded-lg max-h-96 object-cover" />
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
        <!-- Related -->
        <div v-if="relatedBooks.length" class="mt-8">
            <h2 class="text-xl font-bold mb-4">Livros Relacionados</h2>

            <div class="relative">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div v-for="(book, index) in relatedBooks.slice(currentIndex, currentIndex + 3)" :key="book.id"
                        class="w-full">
                        <div class="card bg-base-100 shadow-lg hover:shadow-xl transition-all cursor-pointer h-full"
                            @click="goToBook(book.id)">
                            <figure class="h-64 bg-base-200 flex items-center justify-center overflow-hidden">
                                <img v-if="book.cover_image" :src="'/storage/' + book.cover_image" :alt="book.name"
                                    class="w-full h-full object-contain" />
                                <div v-else class="text-gray-400 text-xs text-center p-2">Sem Capa</div>
                            </figure>
                            <div class="card-body p-4">
                                <h3 class="text-sm font-bold line-clamp-2">{{ book.name }}</h3>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ book.authors_array?.join(', ') || 'Autor desconhecido' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <button v-if="canGoPrev"
                    class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 bg-white hover:bg-gray-50 p-3 rounded-full shadow-lg transition-all z-10"
                    @click="prev">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button v-if="canGoNext"
                    class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 bg-white hover:bg-gray-50 p-3 rounded-full shadow-lg transition-all z-10"
                    @click="next">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <div v-if="relatedBooks.length > 3" class="flex justify-center gap-2 mt-4">
                    <button v-for="i in Math.ceil(relatedBooks.length / 3)" :key="i" @click="currentIndex = (i - 1) * 3"
                        class="w-2 h-2 rounded-full transition-all"
                        :class="Math.floor(currentIndex / 3) === i - 1 ? 'bg-primary w-6' : 'bg-gray-300'">
                    </button>
                </div>
            </div>
        </div>
        <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100 mt-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-800">Reviews dos Leitores</h2>
                </div>

                <button v-if="canLeaveReview" @click="openReviewModal" class="btn btn-primary btn-sm rounded-lg p-2">
                    Deixar Review
                </button>
            </div>
            <!-- Reviews -->
            <div class="grid gap-6">
                <div v-for="review in book.approved_reviews?.data || []" :key="review.id"
                    class="border-b border-gray-50 pb-6 last:border-0">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <div class="avatar placeholder">
                                <div
                                    class="bg-neutral text-neutral-content rounded-full w-8 flex items-center justify-center">
                                    <span>{{ review.user.name.charAt(0) }}</span>
                                </div>
                            </div>
                            <span class="font-bold text-gray-700">{{ review.user.name }}</span>
                        </div>
                        <div class="rating rating-sm">
                            <input v-for="star in 5" type="radio" class="mask mask-star-2 bg-orange-400"
                                :checked="star === review.rating" disabled />
                        </div>
                    </div>
                    <p class="text-gray-600 italic">"{{ review.comment }}"</p>
                    <span class="text-xs text-gray-400">{{ new Date(review.created_at).toLocaleDateString() }}</span>
                </div>

                <div v-if="!book.approved_reviews?.data || book.approved_reviews.data.length === 0"
                    class="text-center py-4 text-gray-400 italic">
                    Ainda não existem reviews para este livro.
                </div>
            </div>

            <div v-if="book.approved_reviews?.links && book.approved_reviews.links.length > 3"
                class="mt-6 flex flex-col items-center justify-center gap-6 p-4 w-full">
                <div class="text-sm text-gray-600 text-center">
                    <span class="font-semibold">{{ book.approved_reviews.to || 0 }}</span> de
                    <span class="font-semibold">{{ book.approved_reviews.total }}</span> resultados
                </div>

                <nav class="flex flex-wrap justify-center gap-1">
                    <button v-for="(link, index) in book.approved_reviews.links" :key="index" @click="goToPage(link)"
                        class="px-3 py-1 text-sm rounded-md transition-all duration-200 border" :class="{
                            'bg-primary text-white border-primary shadow-md': link.active,
                            'bg-white text-gray-700 hover:bg-gray-50 border-gray-300': !link.active && link.url,
                            'text-gray-300 border-gray-100 cursor-not-allowed': !link.url
                        }" :disabled="!link.url || link.active" v-html="link.label">
                    </button>
                </nav>
            </div>
        </div>
        <div class="bg-white shadow-xl rounded-2xl p-8 border border-gray-100"
            v-if="$page.props.auth.user?.role === 'admin'">
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
                        <tr v-for="(loan, index) in book.loans" :key="loan.id"
                            class="hover [&:nth-child(odd)]:text-gray-900">
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
                                <div v-if="loan.actual_return_date"
                                    class="badge badge-success badge-outline badge-sm font-bold">
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
        <div :class="['modal', { 'modal-open': isModalOpen }]">
            <div class="modal-box bg-white">
                <h3 class="font-bold text-lg text-gray-800">Deixar a sua opinião</h3>
                <p class="py-4 text-sm text-gray-600">Partilhe a sua experiência de leitura com outros cidadãos.</p>

                <div class="form-control w-full">
                    <label class="label"><span class="label-text font-semibold text-gray-700">Avaliação: </span></label>
                    <div class="rating rating-md ml-4">
                        <input v-for="star in 5" :key="star" type="radio" name="rating-2"
                            class="mask mask-star-2 bg-orange-400" :value="star" v-model="reviewForm.rating" />
                    </div>

                    <label class="label mt-4"><span class="label-text font-semibold">O seu comentário</span></label>
                    <textarea v-model="reviewForm.comment"
                        class="textarea textarea-bordered h-24 bg-gray-50 text-gray-800 mt-4"
                        placeholder="O que achou do livro?"></textarea>
                </div>

                <div class="modal-action">
                    <button class="btn btn-secondary p-2" @click="closeReviewModal">Cancelar</button>
                    <button class="btn btn-primary p-2" @click="submitReview" :disabled="!reviewForm.comment">
                        Enviar Review
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>