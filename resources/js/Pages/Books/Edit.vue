<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { router } from '@inertiajs/vue3'
defineOptions({ layout: AppLayout })

const props = defineProps({
    book: Object,
    authors: Array,
    publishers: Array,
})

const authorsList = props.authors || []

const form = useForm({
    isbn: props.book.isbn || '',
    name: props.book.name || '',
    publisher_id: props.book.publisher_id || null,
    bibliography: props.book.bibliography || '',
    price: props.book.price || '',
    // Aquí transformamos los autores seleccionados a objetos completos
    authors: props.book.authors
        ? props.book.authors.map(a => ({ id: a.id, name: a.name }))
        : [],
    cover_image: null,
    errors: {},
})

function submit() {
    const data = new FormData()
    data.append('isbn', form.isbn)
    data.append('name', form.name)
    data.append('publisher_id', form.publisher_id)
    data.append('bibliography', form.bibliography)
    data.append('price', form.price)

    // Enviamos solo los IDs de los autores al backend
    form.authors.forEach(author => data.append('authors[]', author.id))

    if (form.cover_image) data.append('cover_image', form.cover_image)
    data.append('_method', 'PUT')

    router.post(route('books.update', props.book.id), data, {
        preserveScroll: true,
        onSuccess: () => (form.errors = {}),
        onError: errors => (form.errors = errors),
    })
}
</script>

<template>
    <div class="p-6 max-w-3xl">
        <h1 class="text-2xl font-bold mb-4">Editar Livro</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <input v-model="form.isbn" placeholder="ISBN" class="input input-bordered w-full" />
                <p v-if="form.errors?.isbn" class="text-red-600 text-sm mt-1">
                    {{ form.errors.isbn }}
                </p>
            </div>
            <div>
                <input v-model="form.name" placeholder="Nome" class="input input-bordered w-full" />
                <p v-if="form.errors?.name" class="text-red-600 text-sm mt-1">
                    {{ form.errors.name }}
                </p>
            </div>
            <div>
                <select v-model="form.publisher_id" class="select select-bordered w-full">
                    <option disabled value="">Selecione uma editora</option>
                    <option v-for="p in publishers" :key="p.id" :value="p.id">
                        {{ p.name }}
                    </option>
                </select>
                <p v-if="form.errors?.publisher_id" class="text-red-600 text-sm mt-1">
                    {{ form.errors.publisher_id }}
                </p>
            </div>
            <!-- Multiselect de autores -->
            <div>
                <Multiselect v-model="form.authors" :options="authorsList" :multiple="true" :close-on-select="false"
                    :clear-on-select="false" :track-by="'id'" :label="'name'" placeholder="Escolha os autores" />
                <p v-if="form.errors?.authors" class="text-red-600 text-sm mt-1">
                    {{ form.errors.authors }}
                </p>
            </div>
            <textarea v-model="form.bibliography" placeholder="Bibliografia"
                class="textarea textarea-bordered w-full"></textarea>
            <div>
                <input type="number" step="0.01" v-model="form.price" placeholder="Preço"
                    class="input input-bordered w-full" />
                <p v-if="form.errors?.price" class="text-red-600 text-sm mt-1">
                    {{ form.errors.price }}
                </p>
            </div>
            <input type="file" @change="e => form.cover_image = e.target.files[0]" accept="image/*" />
            <br />
            <button class="btn btn-primary p-2">Salvar</button>

        </form>
    </div>
</template>
