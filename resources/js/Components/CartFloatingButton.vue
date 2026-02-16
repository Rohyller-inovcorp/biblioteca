<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Link } from '@inertiajs/vue3'
const cartCount = ref(0)

function updateCartCount() {
    const stored = JSON.parse(localStorage.getItem('cart') || '[]')
    cartCount.value = stored.reduce((sum, item) => sum + item.quantity, 0)
}

onMounted(() => {
    updateCartCount()
    window.addEventListener('cart-updated', updateCartCount)
})

onBeforeUnmount(() => {
    window.removeEventListener('cart-updated', updateCartCount)
})
</script>

<template>
    <Link
        href="/cart"
        class="fixed bottom-6 right-6 bg-primary text-white rounded-full shadow-lg flex items-center justify-center w-16 h-16 hover:scale-110 transition"
    >
        <div class="relative">
            <span class="text-3xl">🛒</span>
            <span
                v-if="cartCount > 0"
                class="absolute -top-2 -right-2 bg-red-600 text-white text-xs font-bold rounded-full px-2 py-1"
            >
                {{ cartCount }}
            </span>
        </div>
    </Link>
</template>
