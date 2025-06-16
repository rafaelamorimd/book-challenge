<template>
    <AppLayout title="Relatório por Autor" :breadcrumbs="breadcrumbs">
        <Head title="Relatório por Autor" />

        <template #header>
            <h2 class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">Relatório por Autor</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <div class="mb-6 flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="rounded-full bg-blue-100 p-3 text-blue-500 dark:bg-blue-900 dark:text-blue-300">
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
                                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Relatório Detalhado por Autor</h2>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Visualize todos os livros agrupados por autor</p>
                                </div>
                            </div>
                            <button
                                @click="downloadPdf"
                                class="inline-flex items-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none"
                            >
                                <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                Baixar PDF
                            </button>
                        </div>

                        <div
                            v-for="author in authors"
                            :key="author.authorId"
                            class="mb-8 overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700"
                        >
                            <div class="bg-gray-50 px-6 py-4 dark:bg-gray-700">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ author.authorName }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">ID: {{ author.authorId }}</p>
                                    </div>
                                    <span
                                        class="rounded-full bg-blue-100 px-3 py-1 text-sm font-medium text-blue-800 dark:bg-blue-900 dark:text-blue-300"
                                    >
                                        {{ author.books.length }} {{ author.books.length === 1 ? 'livro' : 'livros' }}
                                    </span>
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                        <tr>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                            >
                                                #
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                            >
                                                Livro
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
                                                Edição
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                            >
                                                Ano Pub.
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                            >
                                                Valor (R$)
                                            </th>
                                            <th
                                                scope="col"
                                                class="px-6 py-3 text-left text-xs font-medium tracking-wider text-gray-500 uppercase dark:text-gray-300"
                                            >
                                                Assuntos
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-800">
                                        <tr v-if="!author.books.length" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                                Nenhum livro encontrado.
                                            </td>
                                        </tr>
                                        <tr v-for="book in author.books" :key="book.bookId" class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">{{ book.bookId }}</td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-900 dark:text-gray-100">{{ book.bookTitle }}</td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ book.publisher }}</td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ book.edition }}</td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {{ book.publicationYear }}
                                            </td>
                                            <td class="px-6 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                                {{ formatCurrency(book.amount) }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                                <div class="flex flex-wrap gap-1">
                                                    <span
                                                        v-for="subject in book.subjects"
                                                        :key="subject"
                                                        class="inline-flex items-center rounded-full bg-purple-100 px-2.5 py-0.5 text-xs font-medium text-purple-800 dark:bg-purple-900 dark:text-purple-300"
                                                    >
                                                        {{ subject }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Author {
    authorId: number;
    authorName: string;
    books: Array<{
        bookId: number;
        bookTitle: string;
        publisher: string;
        edition: string;
        publicationYear: string;
        amount: number | string;
        subjects: string[];
    }>;
}

defineProps<{
    authors: Author[];
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Relatório por Autor',
        href: '/reports/authors',
    },
];

const formatCurrency = (value: number | string): string => {
    if (value === null || value === undefined) return 'R$ 0,00';

    const numericValue = typeof value === 'string' ? parseFloat(value.replace(',', '.')) : value;

    if (isNaN(numericValue)) return 'R$ 0,00';

    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(numericValue);
};

const downloadPdf = () => {
    window.location.href = route('reports.authors.download');
};
</script>

<style scoped>
@media print {
    button {
        display: none;
    }

    table {
        page-break-inside: avoid;
    }
}
</style>
