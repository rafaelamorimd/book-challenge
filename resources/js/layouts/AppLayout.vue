<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <FlashMessage />
            <nav class="border-b border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')"> Dashboard </NavLink>
                                <NavLink :href="route('books.index')" :active="route().current('books.*')"> Livros </NavLink>
                                <NavLink :href="route('authors.index')" :active="route().current('authors.*')"> Autores </NavLink>
                                <NavLink :href="route('subjects.index')" :active="route().current('subjects.*')"> Assuntos </NavLink>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden">
                    <div class="space-y-1 pt-2 pb-3">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')"> Dashboard </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('books.index')" :active="route().current('books.*')"> Livros </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('authors.index')" :active="route().current('authors.*')"> Autores </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('subjects.index')" :active="route().current('subjects.*')"> Assuntos </ResponsiveNavLink>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white shadow dark:bg-gray-800">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup lang="ts">
import ApplicationLogo from '@/components/ApplicationLogo.vue';
import FlashMessage from '@/components/FlashMessage.vue';
import NavLink from '@/components/NavLink.vue';
import ResponsiveNavLink from '@/components/ResponsiveNavLink.vue';
import type { BreadcrumbItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

interface Props {
    title?: string;
    breadcrumbs?: BreadcrumbItem[];
}

defineProps<Props>();

const showingNavigationDropdown = ref(false);
</script>
