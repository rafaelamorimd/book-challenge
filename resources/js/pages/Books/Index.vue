<template>
    <AppLayout title="Livros" :breadcrumbs="breadcrumbs">
        <Head title="Livros" />

        <template #header>
            <div class="flex w-full items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Livros</h2>
                <Link :href="route('books.create')" class="ml-auto">
                    <Button variant="default" class="gap-2"> Novo Livro </Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <DataTable
                            :data="books"
                            :columns="columns"
                            :filterable="true"
                            :show-column-filter="true"
                            :show-status="true"
                            search-placeholder="Buscar livros por Título ou Editora..."
                            :paginate="true"
                            class="w-full"
                        >
                            <template v-for="column in columns" :key="column.id" #[column.accessorKey]="{ row }">
                                <component :is="column.cell" :row="{ original: row }" />
                            </template>
                        </DataTable>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDialog
            v-model:show="showDeleteDialog"
            title="Confirmar Exclusão"
            :message="deleteMessage"
            confirm-button-class="bg-red-600 hover:bg-red-700"
            @confirm="handleDelete"
        />
    </AppLayout>
</template>

<script setup lang="ts">
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import DataTable from '@/components/DataTable.vue';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { PencilIcon, TrashIcon } from 'lucide-vue-next';
import { h, ref } from 'vue';

interface Book {
    Codl: number;
    Titulo: string;
    Editora: string;
    Edicao: string;
    AnoPublicacao: number;
    valor: number;
    authors?: Array<{ CodAu: number; Nome: string }>;
    subjects?: Array<{ codAs: number; Descricao: string }>;
}

defineProps<{
    books: Book[];
}>();

const showDeleteDialog = ref(false);
const bookToDelete = ref<Book | null>(null);
const deleteMessage = ref('');

const columns = [
    {
        id: 'actions',
        header: 'Ações',
        accessorKey: 'actions',
        cell: ({ row }: { row: { original: Book } }) =>
            h(
                'div',
                {
                    class: 'flex items-center gap-2',
                },
                [
                    h(
                        Link,
                        {
                            href: route('books.edit', { book: row.original.Codl }),
                            class: 'text-gray-500 hover:text-gray-700',
                        },
                        () =>
                            h(
                                Button,
                                {
                                    variant: 'secondary',
                                    size: 'icon',
                                    class: 'border border-gray-500/20',
                                    title: 'Editar',
                                },
                                () => h(PencilIcon, { class: 'w-4 h-4' }),
                            ),
                    ),

                    h(
                        Button,
                        {
                            variant: 'secondary',
                            size: 'icon',
                            class: 'text-gray-500 hover:text-gray-700 border border-gray-500/20',
                            onClick: () => confirmDelete(row.original),
                        },
                        () => h(TrashIcon, { class: 'w-4 h-4' }),
                    ),
                ],
            ),
    },
    {
        id: 'Titulo',
        header: 'Título',
        accessorKey: 'Titulo',
        cell: ({ row }: { row: { original: Book } }) => h('span', { class: 'font-medium' }, row.original.Titulo),
    },
    {
        id: 'Editora',
        header: 'Editora',
        accessorKey: 'Editora',
        cell: ({ row }: { row: { original: Book } }) => h('span', { class: 'text-gray-600' }, row.original.Editora),
    },
    {
        id: 'Edicao',
        header: 'Edição',
        accessorKey: 'Edicao',
        cell: ({ row }: { row: { original: Book } }) => h('span', { class: 'text-gray-600' }, row.original.Edicao),
    },
    {
        id: 'AnoPublicacao',
        header: 'Ano de Publicação',
        accessorKey: 'AnoPublicacao',
        cell: ({ row }: { row: { original: Book } }) => h('span', { class: 'text-gray-600' }, row.original.AnoPublicacao),
    },
    {
        id: 'valor',
        header: 'Valor',
        accessorKey: 'valor',
        cell: ({ row }: { row: { original: Book } }) =>
            h('span', { class: 'text-gray-600' }, new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(row.original.valor)),
    },
    {
        id: 'autores',
        header: 'Autores',
        accessorKey: 'authors',
        cell: ({ row }: { row: { original: Book } }) =>
            h('span', { class: 'text-gray-600' }, row.original.authors?.map((author) => author.Nome).join(', ') || '-'),
    },
    {
        id: 'assuntos',
        header: 'Assuntos',
        accessorKey: 'subjects',
        cell: ({ row }: { row: { original: Book } }) =>
            h('span', { class: 'text-gray-600' }, row.original.subjects?.map((subject) => subject.Descricao).join(', ') || '-'),
    },
];

const confirmDelete = (book: Book) => {
    bookToDelete.value = book;
    deleteMessage.value = `Tem certeza que deseja excluir o livro "${book.Titulo}"?`;
    showDeleteDialog.value = true;
};

const handleDelete = () => {
    if (bookToDelete.value) {
        router.delete(route('books.destroy', { book: bookToDelete.value.Codl }));
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Livros',
        href: route('books.index'),
    },
];
</script>
