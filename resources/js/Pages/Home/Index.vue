<template>
  <div class="min-h-screen  flex flex-col items-center justify-center
  -translate-y-4 relative">
    <!-- Hero Section -->
    <div class="hero min-h-[30vh]
    -translate-y-4">
      <div class="hero-content text-center text-white">
        <div class="max-w-3xl">
          <h1 class="text-3xl font-bold mb-6 animate-pulse">
            Olá! Bem-vindo 👋
          </h1>
          <p class="text-2xl mb-4 font-light">
            Ao teu <span class="font-bold">Sistema de Gestão de Biblioteca</span>
          </p>
          <p class="text-lg mb-8 opacity-90">
            Aqui podes gerir tudo o que precisas: livros, autores e editoras. Tudo num só sítio, fácil e rápido!
          </p>
          <div class="flex gap-4 justify-center flex-wrap">
            <a :href="route('books.index')"
              class="btn btn-primary">Ver
              Livros</a>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-col items-center gap-4">
      <div class="alert alert-success max-w-2xl">
        <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span><strong>Segurança a sério!</strong> Todos os dados ficam cifrados na base de dados. 🔒</span>
      </div>

      <div class="text-center">
        <p class="mb-4 text-lg font-semibold">Queres todos os livros em Excel?</p>
        <a :href="route('books.export')" class="btn btn-outline btn-success w-full max-w-xs">
          Baixar Excel Agora
        </a>
      </div>
    </div>
  </div>
</template>

<script setup>
const scrollTo = (id) => {
  const element = document.getElementById(id);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

</script>
