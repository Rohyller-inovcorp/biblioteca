<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import DataTable from '@/Components/DataTable.vue'
import { router } from '@inertiajs/vue3'
import {watch} from 'vue'
import {usePage} from '@inertiajs/vue3'
const page = usePage()

watch(() => page.props.errors.message, (error) => {
  if (error) {
    setTimeout(() => {
      page.props.errors.message = null
    }, 5000)
  }
})

defineOptions({ layout: AppLayout })

const props = defineProps({
    publishers: Object,
    filters: Object,
})

const columns = [
    { label: 'Nome', key: 'name', sortable: true, width: 'w-1/2' },
    { label: 'Logo', key: 'logo', type: 'image', sortable: false, width: 'w-2/5' },
]

function handleSearch(e) {
    router.get(route('publishers.index'), {
        ...props.filters,
        search: e.target.value
    }, { preserveState: true, replace: true });
}
</script>

<template>
    <DataTable title="Editoras" :data="publishers" :columns="columns" :filters="filters" baseRoute="publishers">
        <template #search-input>
            <input type="text" :value="filters.search" @input="handleSearch" placeholder="Pesquisar editora..."
                class="input input-bordered w-full max-w-xs" />
        </template>
    </DataTable>
    <div v-if="$page.props.errors.message" class="toast toast-top toast-end z-[999]">
        <div class="alert alert-error">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium text-sm">{{ $page.props.errors.message }}</span>
                <button @click="$page.props.errors.message = null" class="btn btn-xs btn-circle btn-ghost">✕</button>
            </div>
        </div>
    </div>
</template>