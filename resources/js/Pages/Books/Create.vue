<script setup>

import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { reactive, ref } from 'vue'
defineOptions({ layout: AppLayout })

const props = defineProps({
    book: Object,
    authors: Array,
    publishers: Array,
})

const authorsList = ref([])
authorsList.value = props.authors || []

const form = reactive({
    isbn: '',
    name: '',
    publisher_id: null,
    bibliography: '',
    price: '',
    authors: [],
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

    const authorsIds = form.authors.map(a => a.id)
    authorsIds.forEach(id => data.append('authors[]', id))
    if (form.cover_image) {
        data.append('cover_image', form.cover_image)
    }
    router.post(route('books.store'), data, {
        preserveScroll: true,
        onSuccess: () => {
            form.errors = {}
        },
        onError: errors => {
            form.errors = errors
        },
    })
}
</script>


<template>
    <div class="p-6 max-w-3xl">
        <h1 class="text-2xl font-bold mb-4">Criar Livro</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- ISBN -->
            <div>
                <input v-model="form.isbn" placeholder="ISBN" class="input input-bordered w-full" />
                <p v-if="form.errors?.isbn" class="text-red-600 text-sm mt-1">{{ form.errors.isbn }}</p>
            </div>

            <!-- Nome -->
            <div>
                <input v-model="form.name" placeholder="Nome" class="input input-bordered w-full" />
                <p v-if="form.errors?.name" class="text-red-600 text-sm mt-1">{{ form.errors.name }}</p>
            </div>

            <!-- Publisher -->
            <div>
                <select v-model="form.publisher_id" class="select select-bordered w-full">
                    <option disabled value="">Selecione uma editora</option>
                    <option v-for="p in publishers" :key="p.id" :value="p.id">{{ p.name }}</option>
                </select>
                <p v-if="form.errors?.publisher_id" class="text-red-600 text-sm mt-1">{{ form.errors.publisher_id }}</p>
            </div>

            <!-- Authors -->
            <div>
                <Multiselect v-model="form.authors" :options="authorsList" :multiple="true" :close-on-select="false"
                    :clear-on-select="false" :track-by="'id'" :label="'name'" placeholder="Escolha os autores" />
                <p v-if="form.errors?.authors" class="text-red-600 text-sm mt-1">{{ form.errors.authors }}</p>
            </div>


            <!-- Bibliografia -->
            <div>
                <textarea v-model="form.bibliography" placeholder="Bibliografia"
                    class="textarea textarea-bordered w-full">
                </textarea>
                <p v-if="form.errors?.bibliography" class="text-red-600 text-sm mt-1">
                    {{ form.errors.bibliography }}
                </p>
            </div>

            <!-- Preço -->
            <div>
                <input type="number" step="0.01" v-model="form.price" placeholder="Preço"
                    class="input input-bordered w-full" />
                <p v-if="form.errors?.price" class="text-red-600 text-sm mt-1">
                    {{ form.errors.price }}
                </p>
            </div>

            <!-- Capa -->
            <div>
                <input type="file" @change="e => form.cover_image = e.target.files[0]" accept="image/*" r />
                <p v-if="form.errors?.cover_image" class="text-red-600 text-sm mt-1">
                    {{ form.errors.cover_image }}
                </p>
            </div>
            
            <!-- Botão -->
            <div>
                <button class="btn btn-primary w-24" :disabled="form.processing">Salvar</button>
            </div>
        </form>
    </div>
</template>
