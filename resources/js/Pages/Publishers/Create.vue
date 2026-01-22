<script setup>
import { useForm, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const form = useForm({
    name: '',
    logo: null,
})

function submit() {
    form.post(route('publishers.store'))
}
</script>

<template>
    <div class="p-6 max-w-xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">
            Criar Editora
        </h1>

        <form @submit.prevent="submit" class="space-y-4">
            <!-- Nome -->
            <div>
                <label class="label">
                    <span class="label-text">Nome</span>
                </label>

                <input
                    type="text"
                    class="input input-bordered w-full"
                    v-model="form.name"
                    autocomplete="off"
                />

                <span v-if="form.errors.name" class="text-error text-sm">
                    {{ form.errors.name }}
                </span>
            </div>

            <!-- Logo -->
            <div>
                <label class="label">
                    <span class="label-text">Logo</span>
                </label>

                <input
                    type="file"
                    class="file-input file-input-bordered w-full"
                    accept="image/*"
                    @change="e => form.logo = e.target.files[0]"
                />

                <span v-if="form.errors.logo" class="text-error text-sm">
                    {{ form.errors.logo }}
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
