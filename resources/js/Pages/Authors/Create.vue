<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const form = useForm({
    name: '',
    photo: null,
})

function submit() {
    form.post(route('authors.store'))
}
</script>

<template>
    <div class="p-6 max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Criar Autor</h1>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Name -->
            <div>
                <label class="label">
                    <span class="label-text">Nome</span>
                </label>
                <input
                    type="text"
                    class="input input-bordered w-full"
                    v-model="form.name"
                />
                <span v-if="form.errors.name" class="text-error text-sm">
                    {{ form.errors.name }}
                </span>
            </div>

            <!-- Photo -->
            <div>
                <label class="label">
                    <span class="label-text">Foto</span>
                </label>
                <input
                    type="file"
                    class="file-input file-input-bordered w-full"
                    @change="e => form.photo = e.target.files[0]"
                    accept="image/*"
                />
                <span v-if="form.errors.photo" class="text-error text-sm">
                    {{ form.errors.photo }}
                </span>
            </div>

            <!-- Actions -->
            <div class="flex gap-2">
                <button
                    type="submit"
                    class="btn btn-primary p-2"
                    :disabled="form.processing"
                >
                    Salvar
                </button>
                <Link
                    :href="route('authors.index')"
                    class="btn btn-ghost"
                >
                    Cancelar
                </Link>
            </div>
        </form>
    </div>
</template>
