<script setup>
import { router, Head } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    books: Array,
    filters: Object
})

// Estados de la interfaz
const isModalOpen = ref(false)
const selectedBook = ref(null)
const searchQuery = ref(props.filters.q || '')
const isSearching = ref(false) // Para la búsqueda en Google
const isLoading = ref(false)   // Para la importación a la DB
const showToast = ref(false)
const searchError = ref(null)

// Sincronizar el input con la URL si cambia
watch(() => props.filters.q, (newVal) => {
    searchQuery.value = newVal || ''
})

const openConfirmModal = (book) => {
    selectedBook.value = book
    isModalOpen.value = true
}

const triggerToast = () => {
    showToast.value = true
    setTimeout(() => {
        showToast.value = false
    }, 3000)
}

// Lógica de Búsqueda (Google Books)
// Lógica de Búsqueda (Google Books)
function handleSearch() {
    if (!searchQuery.value.trim()) {
        searchError.value = 'Por favor, insira um termo de busca'
        setTimeout(() => searchError.value = null, 3000)
        return
    }

    searchError.value = null

    router.get(
        route('books.google.search'),
        { q: searchQuery.value.trim() },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onStart: () => { isSearching.value = true },
            onSuccess: (page) => {
                const flashError = page.props.flash?.error;
                
                if (flashError || page.props.books.length === 0) {
                    searchError.value = 'Nenhum livro encontrado ou erro no servidor';
                } else {
                    searchError.value = null;
                }
            },
            onFinish: () => { isSearching.value = false },
            onError: () => {
                searchError.value = 'Nenhum livro encontrado ou erro no servidor.';
                isSearching.value = false;
            }
        }
    )
}

const confirmImport = () => {
    if (!selectedBook.value) return

    router.post(route('books.google.import'), {
        google_books_id: selectedBook.value.google_books_id
    }, {
        onBefore: () => {
            isModalOpen.value = false
            isLoading.value = true
        },
        onSuccess: () => {
            triggerToast()
            selectedBook.value = null
        },
        onError: (errors) => {
            console.error('Import errors:', errors)
            searchError.value = errors.google_books_id || 'Erro ao importar o livro'
        },
        onFinish: () => {
            isLoading.value = false
        }
    })
}
</script>

<template>
    <div class="min-h-screen bg-base-200/50">

        <Head title="Pesquisa Externa" />

        <div class="p-6 w-full max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold mb-6 flex items-center gap-2">
                Procurar no Google Books
            </h1>

            <div class="flex flex-col gap-4 mb-8">
                <div class="join w-full max-w-2xl shadow-lg">
                    <input v-model="searchQuery" type="text" placeholder="Título, autor ou ISBN..."
                        class="input input-bordered join-item w-full focus:outline-none p-2" @keyup.enter="handleSearch"
                        :disabled="isSearching" />
                    <button @click="handleSearch" class="btn btn-primary join-item px-8" :disabled="isSearching">
                        <span v-if="isSearching" class="loading loading-spinner"></span>
                        {{ isSearching ? 'Buscando...' : 'Pesquisar' }}
                    </button>
                </div>

                <div v-if="searchError" class="alert alert-error shadow-lg max-w-2xl">
                    <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ searchError }}</span>
                </div>
            </div>

            <div v-if="isSearching" class="flex justify-center py-24">
                <div class="flex flex-col items-center gap-4">
                    <span class="loading loading-spinner loading-lg text-primary"></span>
                    <p class="text-gray-500 animate-pulse">Consultando Google Books API...</p>
                </div>
            </div>

            <div v-else-if="books && books.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="book in books" :key="book.google_books_id"
                    class="card card-side bg-base-100 shadow-xl border border-base-300 hover:border-primary transition-all duration-300 h-64 overflow-hidden">

                    <figure class="w-1/3 bg-base-300">
                        <img v-if="book.cover_image" :src="book.cover_image" :alt="book.name"
                            class="h-full w-full object-cover" />
                        <div v-else
                            class="flex items-center justify-center h-full text-gray-400 text-center p-2 text-xs">Sem
                            Capa</div>
                    </figure>

                    <div class="card-body w-2/3 p-4">
                        <h2 class="card-title text-sm line-clamp-2 leading-tight h-10">{{ book.name }}</h2>
                        <p class="text-xs text-primary font-medium truncate">
                            {{ book.authors_array?.join(', ') || 'Autor desconhecido' }}
                        </p>
                        <div class="space-y-1 mt-2">
                            <p class="text-[10px] uppercase font-bold text-gray-400">ISBN: {{ book.isbn || 'N/A' }}</p>
                            <p class="text-[10px] text-gray-500 line-clamp-2">
                                Preço: <span class="text-success font-bold">{{ book.price || 'Não disponível' }}</span>
                            </p>
                        </div>
                        <div class="card-actions justify-end mt-auto">
                            <button @click="openConfirmModal(book)"
                                class="btn btn-sm btn-outline btn-primary gap-2 p-2">
                                Importar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else
                class="text-center py-24 bg-base-100 rounded-3xl border-2 border-dashed border-base-300 shadow-inner">
                <div v-if="filters.q" class="max-w-xs mx-auto">
                    <div class="text-6xl mb-4">🏜️</div>

                    <p class="text-lg font-bold">
                        <!-- Si hay error, mostrar mensaje de error, si no, Sem resultados -->
                        {{ searchError ? searchError : 'Sem resultados' }}
                    </p>

                    <p class="text-gray-500 text-sm">
                        <!-- Si hay error, mostrar mensaje de ayuda, si no, mensaje normal -->
                        {{ searchError
                            ? 'Intente novamente mais tarde ou espere alguns segundos.'
                            : `Nenhum livro encontrado para "${filters.q}". Tente outros termos.`
                        }}
                    </p>
                </div>
                <div v-else class="max-w-xs mx-auto">
                    <div class="text-6xl mb-4 text-primary/20">🔍</div>
                    <p class="text-lg font-bold">Comece a pesquisar</p>
                    <p class="text-gray-500 text-sm">Os resultados da biblioteca global do Google aparecerão aqui.</p>
                </div>
            </div>

            <input type="checkbox" id="import-modal" class="modal-toggle" v-model="isModalOpen" />
            <div class="modal modal-bottom sm:modal-middle backdrop-blur-sm">
                <div class="modal-box border-t-4 border-primary">
                    <h3 class="font-bold text-xl text-center">Importar para Biblioteca</h3>
                    <div class="py-6 text-center space-y-2">
                        <p>Deseja importar este livro?</p>
                        <p class="text-primary font-bold italic">"{{ selectedBook?.name }}"</p>
                    </div>
                    <div class="modal-action justify-center gap-4">
                        <button @click="confirmImport" class="btn btn-primary px-8" :disabled="isLoading">
                            <span v-if="isLoading" class="loading loading-spinner"></span>
                            {{ isLoading ? 'Importando...' : 'Confirmar' }}
                        </button>
                        <button @click="isModalOpen = false" class="btn btn-ghost"
                            :disabled="isLoading">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="showToast" class="toast toast-end toast-top mt-16 z-50">
            <div class="alert alert-success shadow-2xl border-none">
                <div class="flex items-center gap-3 text-white">
                    <div class="bg-white/20 p-1 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="font-bold">Livro importado com sucesso!</span>
                </div>
            </div>
        </div>
    </div>
</template>