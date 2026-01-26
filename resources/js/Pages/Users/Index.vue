<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { Link } from '@inertiajs/vue3'
defineOptions({ layout: AppLayout })

const props = defineProps({
    users: Array,
    filters: Object,
})

const search = ref(props.filters?.search || '')
const roleFilter = ref(props.filters?.role || '')

watch([search, roleFilter], ([newSearch, newRole]) => {
    router.get(route('users.index'), {
        search: newSearch,
        role: newRole
    }, {
        preserveState: true,
        replace: true
    })
})

const showDeleteModal = ref(false)
const userToDelete = ref(null)

const openDeleteModal = (user) => {
    userToDelete.value = user
    showDeleteModal.value = true
}

const deleteItem = () => {
    if (userToDelete.value) {
        router.delete(route('users.destroy', userToDelete.value.id), {
            onSuccess: () => {
                showDeleteModal.value = false
                userToDelete.value = null
            }
        })
    }
}
</script>

<template>
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Gestão de Utilizadores</h1>
            <Link :href="route('users.create')" class="btn btn-primary">Novo Utilizador</Link>
        </div>

        <div class="flex gap-4 mb-6 bg-base-200 p-4 rounded-lg">
            <div class="form-control w-full max-w-xs">
                <input v-model="search" type="text" placeholder="Procurar por nome ou email..."
                    class="input input-bordered w-full p-2" />
            </div>

            <div class="form-control w-full max-w-xs">
                <select v-model="roleFilter" class="select select-bordered w-full p-2">
                    <option value="">Todos os papéis</option>
                    <option value="admin">Administrador</option>
                    <option value="cidadao">Cidadão</option>
                </select>
            </div>
        </div>

        <div class="overflow-x-auto bg-base-100 ">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Nome / Email</th>
                        <th>Papel</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in users" :key="user.id" class="hover">
                        <td>
                            <div class="flex items-center space-x-3">
                                <div class="avatar placeholder">
                                    <div class="bg-neutral text-neutral-content rounded-full w-10
                                    flex items-center justify-center">
                                        <span>{{ user.name.substring(0, 2).toUpperCase() }}</span>
                                    </div>
                                </div>
                                <div>
                                    <div class="font-bold">{{ user.name }}</div>
                                    <div class="text-sm opacity-50">{{ user.email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span :class="user.role === 'admin' ? 'badge badge-secondary' : 'badge badge-accent'">
                                {{ user.role }}
                            </span>
                        </td>
                        <td class="flex gap-2">
                            <Link :href="route('users.show', user.id)" class="btn btn-ghost btn-xs p-2">
                                Histórico
                            </Link>
                            <Link v-if="$page.props.auth.user.id !== user.id" :href="route('users.edit', user.id)" class="btn btn-warning btn-xs">
                                Editar
                            </Link>
                            <button v-if="$page.props.auth.user.id !== user.id" @click="openDeleteModal(user)"
                                class="btn btn-error btn-xs p-2">
                                Apagar
                            </button>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-if="showDeleteModal" class="modal modal-open">
            <div class="modal-box border-t-4 border-error">
                <h3 class="font-bold text-lg text-error">Confirmar eliminação</h3>
                <p class="py-4">
                    Tem a certeza que deseja eliminar o utilizador
                    <span class="font-bold">{{ userToDelete?.name }}</span>?
                    Esta ação não pode ser desfeita.
                </p>
                <div class="modal-action">
                    <button class="btn btn-error p-2 mr-2" @click="deleteItem">Sim, eliminar</button>
                    <button class="btn btn-ghost" @click="showDeleteModal = false">Cancelar</button>
                </div>
            </div>
            <div class="modal-backdrop" @click="showDeleteModal = false"></div>
        </div>
    </div>
</template>