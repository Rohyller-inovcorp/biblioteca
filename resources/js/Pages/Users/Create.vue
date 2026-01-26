<script setup>
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

defineOptions({ layout: AppLayout })

const form = useForm({
    name: '',
    email: '',
    password: '',
    role: '',
})

function submit() {
    form.post(route('users.store'), {
        onSuccess: () => form.reset('password'),
    })
}
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center -translate-y-4 relative">
        <div class="max-w-3xl w-full px-2 -translate-y-4 relative">
            <h1 class="text-2xl font-bold mb-6 text-primary">Novo Utilizador</h1>

            <form @submit.prevent="submit" class="grid grid-cols-1 gap-4">
                <div class="form-control">
                    <label class="label"><span class="label-text">Nome Completo</span></label>
                    <input v-model="form.name" class="input input-bordered w-full mt-2 p-2"
                        :class="{ 'input-error': form.errors.name }" />
                    <p v-if="form.errors.name" class="text-error text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">E-mail</span></label>
                    <input v-model="form.email" type="email" class="input input-bordered w-full mt-2 p-2"
                        :class="{ 'input-error': form.errors.email }" />
                    <p v-if="form.errors.email" class="text-error text-xs mt-1">{{ form.errors.email }}</p>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Papel do Utilizador</span></label>
                    <select v-model="form.role" class="select select-bordered p-2 w-full mt-2 p-2"
                        :class="{ 'select-error': form.errors.role }">
                        <option disabled value="">Seleccionar...</option>
                        <option value="admin">Administrador</option>
                        <option value="cidadao">Cidadão</option>
                    </select>
                    <p v-if="form.errors.role" class="text-error text-xs mt-1">{{ form.errors.role }}</p>
                </div>

                <div class="form-control">
                    <label class="label"><span class="label-text">Palavra-passe</span></label>
                    <input v-model="form.password" type="password" class="input input-bordered p-2 w-full mt-2 p-2" />
                    <p v-if="form.errors.password" class="text-error text-xs mt-1">{{ form.errors.password }}</p>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-full" :disabled="form.processing">
                        <span v-if="form.processing" class="loading loading-spinner"></span>
                        Criar Utilizador
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>