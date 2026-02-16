<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const address = ref({
    address_line: '',
    city: '',
    postal_code: '',
    country: ''
})

function submit() {
    router.post('/checkout/address', {
        ...address.value,
        cart: JSON.parse(localStorage.getItem('cart') || '[]')
    }, {
        onSuccess: (page) => {
            const orderId = page.props.flash.orderId

            window.location.href = `/checkout/pay?order=${orderId}`;
        }
    })
}

</script>

<template>
    <div class="max-w-lg mx-auto py-24">
        <h1 class="text-2xl font-bold mb-6">Morada de Entrega</h1>

        <form @submit.prevent="submit" class="space-y-4">

            <div>
                <label class="block mb-1">Endereço</label>
                <input v-model="address.address_line" class="input input-bordered w-full p-2" required>
            </div>

            <div>
                <label class="block mb-1">Cidade</label>
                <input v-model="address.city" class="input input-bordered w-full p-2" required>
            </div>

            <div>
                <label class="block mb-1">Código Postal</label>
                <input v-model="address.postal_code" class="input input-bordered w-full p-2" required>
            </div>

            <div>
                <label class="block mb-1">País</label>
                <input v-model="address.country" class="input input-bordered w-full p-2" required>
            </div>

            <button class="btn btn-primary w-full mt-4">Continuar para Pagamento</button>
        </form>
    </div>
</template>
