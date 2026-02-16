<script setup>
import { onMounted } from 'vue'

onMounted(() => {
    localStorage.removeItem('cart')
    window.dispatchEvent(new Event('cart-updated'))
})
</script>

<template>
    <div class="text-center py-20">
        <h1 class="text-3xl font-bold mb-4">Pagamento concluído!</h1>
        <p class="text-lg">A sua encomenda foi paga com sucesso.</p>

        <a href="/books" class="btn btn-primary mt-6">Voltar aos livros</a>
    </div>
</template>
