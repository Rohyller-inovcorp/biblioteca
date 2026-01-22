<template>
  <div class="drawer">
    <input id="my-drawer" type="checkbox" class="drawer-toggle" v-model="drawerOpen" />

    <!-- Contenido principal (para TODAS las pantallas) -->
    <div class="drawer-content flex flex-col">
      <!-- Navbar -->
      <div class="navbar bg-base-200 shadow-lg">
        <!-- Botón hamburguesa (solo móvil) -->
        <div class="flex-none lg:hidden">
          <label for="my-drawer" class="btn btn-square btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
              class="inline-block w-6 h-6 stroke-current">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </label>
        </div>

        <!-- Logo -->
        <div class="flex-1">
          <Link href="/dashboard" class="btn btn-ghost normal-case text-xl">Biblioteca</Link>
        </div>

        <!-- Menú desktop (solo pantallas grandes) -->
        <div class="flex-none hidden lg:block">
          <ul class="menu menu-horizontal px-1">

            <template v-if="$page.props.auth.user">
              <li>
                <Link :href="route('dashboard')" class="flex items-center gap-2 text-lg">
                  Início
                </Link>
              </li>
              <li>
                <Link :href="route('books.index')" class="flex items-center gap-2 text-lg">
                  Livros
                </Link>
              </li>
              <li>
                <Link :href="route('authors.index')" class="flex items-center gap-2 text-lg">
                  Autores
                </Link>
              </li>
              <li>
                <Link :href="route('publishers.index')" class="flex items-center gap-2 text-lg">
                  Editoras
                </Link>
              </li>

              <li>
                <Link :href="route('profile.show')"
                  class="btn btn-ghost btn-circle avatar placeholder hover:ring-2 hover:ring-primary transition-all"
                  title="Ir para o meu perfil">
                  <div
                    class="bg-primary text-primary-content rounded-full w-10 flex items-center justify-center font-bold 
                    text-lg uppercase px-2 text-center">
                    <span>{{ $page.props.auth.user.name.charAt(0) }}</span>
                  </div>
                </Link>
              </li>
            </template>

            <template v-else>
              <li>
                <Link :href="route('login')" class="btn btn-primary btn-sm text-white">
                  Entrar
                </Link>
              </li>
              <li>
                <Link :href="route('register')" class="btn btn-ghost btn-sm">
                  Registar
                </Link>
              </li>
            </template>
          </ul>
        </div>
      </div>

      <!-- Contenido de la página -->
      <div class="p-4">
        <slot />
      </div>
    </div>

    <div class="drawer-side z-50">
      <label for="my-drawer" aria-label="close sidebar" class="drawer-overlay"></label>

      <div v-if="$page.props.auth.user" class="menu p-4 w-80 min-h-full bg-base-200 text-base-content">
        <div class="mb-4 border-b border-base-300 pb-4">
          <h2 class="text-2xl font-bold px-4">Menu</h2>
          <p class="px-4 text-sm opacity-60">Olá, {{ $page.props.auth.user.name }}</p>
        </div>

        <ul class="space-y-2">
          <li>
            <Link :href="route('dashboard')" class="flex items-center gap-2 text-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
              </svg>
              Início
            </Link>
          </li>
          <li>
            <Link :href="route('books.index')" class="flex items-center gap-2 text-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
              </svg>
              Livros
            </Link>
          </li>
          <li>
            <Link :href="route('authors.index')" class="flex items-center gap-2 text-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Autores
            </Link>
          </li>
          <li>
            <Link :href="route('publishers.index')" class="flex items-center gap-2 text-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
              </svg>
              Editoras
            </Link>
          </li>


          <li>
            <Link :href="route('profile.show')" class="flex items-center gap-2 text-lg">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              </svg>
              Perfil
            </Link>
          </li>
        </ul>
      </div>

      <div v-else class="menu p-4 w-80 min-h-full bg-base-200 text-base-content">
        <div class="mb-4">
          <h2 class="text-2xl font-bold px-4">Livraria</h2>
        </div>
        <li>
          <Link :href="route('login')" class="btn btn-primary text-white mb-2">Entrar</Link>
        </li>
        <li>
          <Link :href="route('register')" class="btn btn-ghost">Registar</Link>
        </li>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

const drawerOpen = ref(false);

// Cerrar el drawer cuando cambie la página
router.on('navigate', () => {
  drawerOpen.value = false;
});
const logout = () => {
  router.post(route('logout'));
};
</script>