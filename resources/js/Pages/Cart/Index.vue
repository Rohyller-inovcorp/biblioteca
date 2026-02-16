<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, router } from '@inertiajs/vue3'

const cart = ref([])

onMounted(() => {
    const stored = localStorage.getItem('cart')
    cart.value = stored ? JSON.parse(stored) : []
})

function removeItem(id) {
    cart.value = cart.value.filter(item => item.id !== id)
    localStorage.setItem('cart', JSON.stringify(cart.value))
}

function updateQuantity(id, qty) {
    cart.value = cart.value.map(item => {
        if (item.id === id) {
            return { ...item, quantity: qty }
        }
        return item
    })
    localStorage.setItem('cart', JSON.stringify(cart.value))
}

const totalItems = computed(() =>
    cart.value.reduce((sum, item) => sum + item.quantity, 0)
)

const totalPrice = computed(() =>
    cart.value.reduce((sum, item) => sum + item.price * item.quantity, 0).toFixed(2)
)

function goToAddress() {
    router.visit('/checkout/address', {
        method: 'get',
        data: { cart: cart.value }
    })
}
</script>

<template>
    <div class="container mx-auto py-10">

        <h1 class="text-3xl font-bold mb-6">Carrinho de Compras</h1>

        <div v-if="cart.length === 0" class="text-center py-20">
            <p class="text-xl mb-4">O seu carrinho está vazio.</p>
            <Link href="/books" class="btn btn-primary">Voltar aos Livros</Link>
        </div>

        <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <div class="md:col-span-2 space-y-4">
                <div v-for="item in cart" :key="item.id" class="card bg-base-200 shadow p-4 flex gap-4">
                    <img :src="'/storage/' + item.cover" alt="" class="w-20 h-28 object-cover rounded" />

                    <div class="flex-1">
                        <h2 class="font-bold text-lg">{{ item.name }}</h2>
                        <p class="text-sm text-gray-500">Preço: €{{ item.price }}</p>

                        <div class="flex items-center gap-2 mt-2">
                            <button class="btn btn-xs p-2"
                                @click="updateQuantity(item.id, Math.max(1, item.quantity - 1))">-</button>

                            <span class="px-3">{{ item.quantity }}</span>

                            <button class="btn btn-xs p-2"
                                @click="updateQuantity(item.id, item.quantity + 1)">+</button>
                        </div>

                        <p class="mt-2 font-semibold">
                            Subtotal: €{{ (item.price * item.quantity).toFixed(2) }}
                        </p>
                    </div>

                    <button class="btn btn-error btn-sm h-10"
                        @click="removeItem(item.id)">
                        Remover
                    </button>
                </div>
            </div>

            <div class="border p-4 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Resumo</h2>

                <p>Total de itens: {{ totalItems }}</p>
                <p class="text-lg font-bold mt-2">Total: €{{ totalPrice }}</p>

                <button @click="goToAddress"
                    class="btn btn-primary w-full mt-4">
                    Continuar para Morada
                </button>
            </div>

        </div>

    </div>
</template>
