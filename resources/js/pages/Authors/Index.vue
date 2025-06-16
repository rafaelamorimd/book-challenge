<template>
    <AppLayout title="Autores" :breadcrumbs="breadcrumbs">
        <Head title="Autores" />

        <template #header>
            <div class="flex w-full items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Autores</h2>
                <Link :href="route('authors.create')" class="ml-auto">
                    <Button variant="default" class="gap-2"> Novo Autor </Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <DataTable
                            :data="authors"
                            :columns="columns"
                            :filterable="true"
                            :show-column-filter="true"
                            :show-status="true"
                            search-placeholder="Buscar por nome..."
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

interface Authors {
    CodAu: number;
    Nome: string;
}

defineProps<{
    authors: Authors[];
}>();

const showDeleteDialog = ref(false);
const authorToDelete = ref<Authors | null>(null);
const deleteMessage = ref('');

const columns = [
    {
        id: 'actions',
        header: 'Ações',
        accessorKey: 'actions',
        enableHiding: false,
        cell: ({ row }: { row: { original: Authors } }) =>
            h(
                'div',
                {
                    class: 'flex items-center gap-2',
                },
                [
                    h(
                        Link,
                        {
                            href: route('authors.edit', { author: row.original.CodAu }),
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
        id: 'CodAu',
        header: 'Código',
        accessorKey: 'CodAu',
        cell: ({ row }: { row: { original: Authors } }) => h('span', { class: 'font-medium' }, row.original.CodAu),
    },
    {
        id: 'Nome',
        header: 'Nome',
        accessorKey: 'Nome',
        cell: ({ row }: { row: { original: Authors } }) => h('span', { class: 'font-medium' }, row.original.Nome),
    },
];

const confirmDelete = (author: Authors) => {
    authorToDelete.value = author;
    deleteMessage.value = `Tem certeza que deseja excluir o autor "${author.Nome}"?`;
    showDeleteDialog.value = true;
};

const handleDelete = () => {
    if (authorToDelete.value) {
        router.delete(route('authors.destroy', { author: authorToDelete.value.CodAu }));
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Autores',
        href: route('authors.index'),
    },
];
</script>
