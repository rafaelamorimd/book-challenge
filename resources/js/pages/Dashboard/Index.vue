<template>
    <AppLayout title="Dashboard" :breadcrumbs="breadcrumbs">
        <Head title="Dashboard" />

        <template #header>
            <h2 class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Cards -->
                <div class="mb-8 grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                    <!-- Card Total de Livros -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="rounded-full bg-blue-100 p-3 text-blue-500 dark:bg-blue-900 dark:text-blue-300">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total de Livros</h2>
                                    <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ props.books }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Total de Autores -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="rounded-full bg-green-100 p-3 text-green-500 dark:bg-green-900 dark:text-green-300">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total de Autores</h2>
                                    <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ props.authors }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Total de Assuntos -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="rounded-full bg-purple-100 p-3 text-purple-500 dark:bg-purple-900 dark:text-purple-300">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Total de Assuntos</h2>
                                    <p class="text-2xl font-semibold text-gray-800 dark:text-gray-200">{{ props.subjects }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Relatório -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="rounded-full bg-red-100 p-3 text-red-500 dark:bg-red-900 dark:text-red-300">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-sm font-medium text-gray-600 dark:text-gray-400">Relatório de Autores</h2>
                                    <Link
                                        :href="route('reports.authors')"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="mt-2 inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300"
                                    >
                                        Gerar Relatório
                                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráfico de Livros por Assunto -->
                <div class="mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <BooksBySubjectChart :books-by-subject="props.booksBySubject" />
                    </div>
                </div>

                <!-- Tabela de Últimos Livros -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <h3 class="mb-4 text-lg font-medium text-gray-900 dark:text-gray-100">Últimos Livros Adicionados</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                        >
                                            Título
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                        >
                                            Autor
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                        >
                                            Editora
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                        >
                                            Valor
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                    <tr v-for="livro in props.lastBooks" :key="livro.Codl" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">
                                            {{ livro.Titulo }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ getAuthors(livro.authors) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ livro.Editora }}
                                        </td>
                                        <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                            {{ new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(livro.valor) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import BooksBySubjectChart from '@/components/Charts/BooksBySubjectChart.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    books: number;
    authors: number;
    subjects: number;
    lastBooks: Array<{
        Codl: number;
        Titulo: string;
        Editora: string;
        valor: number;
        authors: Array<{
            Nome: string;
        }>;
    }>;
    booksBySubject: Array<{
        Descricao: string;
        books_count: number;
    }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

const getAuthors = (authors: Array<{ Nome: string }>) => {
    return authors.map((author) => author.Nome).join(', ');
};
</script>
