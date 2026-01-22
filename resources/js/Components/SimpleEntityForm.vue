<script setup>
import { ref } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
    entity: Object,     // Autor o Editora
    baseRoute: String,  // 'authors' o 'publishers'
    title: String,      // 'Autor' o 'Editora'
    imageField: {       // 'logo' ou 'photo'
        type: String,
        default: 'photo'
    }
})

const form = useForm({
    name: props.entity.name || '',
    [props.imageField]: null,
    _method: 'PUT',
})

const photoPreview = ref(props.entity[props.imageField] ? `/storage/${props.entity[props.imageField]}` : null)
const localError = ref('')

function handlePhoto(event) {
    const file = event.target.files[0]
    form[props.imageField] = file || null

    if (file) {
        photoPreview.value = URL.createObjectURL(file)
    } else {
        photoPreview.value = props.entity[props.imageField] ? `/storage/${props.entity[props.imageField]}` : null
    }
}

function submit() {
    form.post(route(`${props.baseRoute}.update`, props.entity.id), {
        forceFormData: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center -translate-y-4 relative">
        <div class="max-w-3xl w-full px-2 -translate-y-4 relative">
            <h1 class="text-2xl md:text-5xl font-bold mb-6 text-center">Editar {{ title }}</h1>

            <form @submit.prevent="submit" class="space-y-4 pt-2">
                <div>
                    <label class="block mb-1 font-medium">Nome do {{ title }}</label>
                    <input type="text" v-model="form.name" class="input input-primary w-full p-2"
                        :class="{ 'input-error': form.errors.name }" />
                    <p v-if="form.errors.name" class="text-error text-sm mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label class="block mb-1 font-medium">Imagem</label>
                    <input type="file" @change="handlePhoto" accept="image/*"
                        class="file-input file-input-bordered w-full" />

                    <div v-if="photoPreview" class="mt-4">
                        <p class="text-sm text-gray-500 mb-2">Previsualização:</p>
                        <img :src="photoPreview" class="w-32 h-32 object-cover rounded border" />
                    </div>
                    <p v-if="form.errors[imageField]" class="text-error text-sm mt-1">{{ form.errors[imageField] }}</p>
                </div>

                <div class="pt-4 flex gap-2">
                    <button type="submit" class="btn btn-primary w-28 p-2" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner"></span>
                        Salvar
                    </button>
                    <Link :href="route(`${baseRoute}.index`)" class="btn btn-outline">
                        Cancelar
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>