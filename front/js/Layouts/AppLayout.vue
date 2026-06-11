<template>
  <div class="min-h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
    <!-- Navbar -->
    <nav class="bg-indigo-700 dark:bg-indigo-900 shadow-lg">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-8">
            <Link :href="route('dashboard')" class="text-white font-bold text-xl tracking-tight">
              ServiceHub
            </Link>
            <div class="hidden md:flex space-x-1">
              <NavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</NavLink>
              <NavLink v-if="can('companies.view')" :href="route('companies.index')" :active="route().current('companies.*')">Empresas</NavLink>
              <NavLink v-if="can('projects.view')" :href="route('projects.index')" :active="route().current('projects.*')">Projetos</NavLink>
              <NavLink v-if="can('tickets.view')" :href="route('tickets.index')" :active="route().current('tickets.*')">Tickets</NavLink>
              <NavLink v-if="isManager()" :href="route('admin.users.index')" :active="route().current('admin.users.*')" class="border-l border-indigo-500 ml-2 pl-2">Gerenciamento de Usuários</NavLink>
              <NavLink v-if="isManager()" :href="route('admin.roles.index')" :active="route().current('admin.roles.*')">Permissões</NavLink>
            </div>
          </div>

          <div class="flex items-center space-x-2">
            <ThemeToggle />
            <Link
              :href="route('profile.edit')"
              class="px-3 py-2 text-indigo-200 hover:text-white text-sm rounded-lg hover:bg-indigo-600 transition-colors"
            >
              {{ $page.props.auth.user?.name }}
            </Link>
            <form @submit.prevent="logout">
              <button
                type="submit"
                class="px-3 py-2 text-indigo-200 hover:text-white text-sm rounded-lg hover:bg-indigo-600 transition-colors"
              >
                Sair
              </button>
            </form>
          </div>
        </div>
      </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4">
      <div
        v-if="$page.props.flash?.success"
        class="mt-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-700 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg flex justify-between items-center"
      >
        <span>{{ $page.props.flash.success }}</span>
        <button @click="$page.props.flash.success = null" class="text-green-600 dark:text-green-400 ml-4">✕</button>
      </div>
      <div
        v-if="$page.props.flash?.error"
        class="mt-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-700 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg flex justify-between items-center"
      >
        <span>{{ $page.props.flash.error }}</span>
        <button @click="$page.props.flash.error = null" class="text-red-600 dark:text-red-400 ml-4">✕</button>
      </div>
    </div>

    <!-- Page Header -->
    <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow-sm mt-0">
      <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
        <slot name="header" />
      </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
      <slot />
    </main>
  </div>
</template>

<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';

const page = usePage();

function can(permission) {
  return page.props.auth.user?.permissions?.includes(permission);
}

function isManager() {
  return page.props.auth.user?.roles?.includes('manager');
}

function logout() {
  router.post(route('logout'));
}
</script>
