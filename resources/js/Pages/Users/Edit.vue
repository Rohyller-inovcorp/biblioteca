<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    user: Object
});

const form = useForm({
    _method: 'put',
    name: props.user.name,
    email: props.user.email,
    role: props.user.role,
    photo: null,
});

const submit = () => {
    form.post(route('users.update', props.user.id), {
        onSuccess: () => {

        },
    });
};
</script>

<template>
    <div>
        <h2 class="font-semibold text-xl leading-tight pt-2 mt-2">
            Editar Utilizador: {{ user.name }}
        </h2>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden  p-6">

                    <form @submit.prevent="submit" class="space-y-6">
                        <div class="form-control">
                            <label class="label"><span class="label-text">Nome</span></label>
                            <input v-model="form.name" type="text" class="input input-bordered w-full p-2"
                                :class="{ 'input-error': form.errors.name }" />
                            <div v-if="form.errors.name" class="text-error text-sm">{{ form.errors.name }}</div>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">E-mail</span></label>
                            <input v-model="form.email" type="email" class="input input-bordered w-full p-2"
                                :class="{ 'input-error': form.errors.email }" />
                            <div v-if="form.errors.email" class="text-error text-sm">{{ form.errors.email }}</div>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Tipo de Utilizador</span></label>
                            <select v-model="form.role" class="select select-bordered w-full p-2">
                                <option value="cidadao">Cidadão</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Atualizar Foto (Opcional)</span></label>
                            <input type="file" @input="form.photo = $event.target.files[0]"
                                class="file-input file-input-bordered w-full" accept="image/*" />
                            <div v-if="form.errors.photo" class="text-error text-sm">{{ form.errors.photo }}</div>
                        </div>

                        <div class="flex items-center justify-end mt-4 gap-2">
                            <Link :href="route('users.index')" class="btn btn-ghost">Cancelar</Link>
                            <button type="submit" class="btn btn-primary p-2" :disabled="form.processing">
                                Guardar Alterações
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</template>