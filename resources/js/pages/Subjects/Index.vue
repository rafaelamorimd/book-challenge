<template>
    <AppLayout title="Assuntos" :breadcrumbs="breadcrumbs">
        <Head title="Assuntos" />

        <template #header>
            <div class="flex w-full items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Assuntos</h2>
                <Link :href="route('subjects.create')" class="ml-auto">
                    <Button variant="default" class="gap-2"> Novo Assunto </Button>
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6">
                        <DataTable
                            :data="subjects"
                            :columns="columns"
                            :filterable="true"
                            :show-column-filter="true"
                            :show-status="true"
                            search-placeholder="Buscar assuntos..."
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

interface Subject {
    CodAs: number;
    Descricao: string;
}

defineProps<{
    subjects: Subject[];
}>();

const showDeleteDialog = ref(false);
const subjectToDelete = ref<Subject | null>(null);
const deleteMessage = ref('');

const columns = [
    {
        id: 'actions',
        header: 'Ações',
        accessorKey: 'actions',
        enableHiding: false,
        cell: ({ row }: { row: { original: Subject } }) =>
            h(
                'div',
                {
                    class: 'flex items-center gap-2',
                },
                [
                    h(
                        Link,
                        {
                            href: route('subjects.edit', { subject: row.original.CodAs }),
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
        id: 'CodAs',
        header: 'Código',
        accessorKey: 'CodAs',
        cell: ({ row }: { row: { original: Subject } }) => h('span', { class: 'font-medium' }, row.original.CodAs),
    },
    {
        id: 'Descricao',
        header: 'Descrição',
        accessorKey: 'Descricao',
        cell: ({ row }: { row: { original: Subject } }) => h('span', { class: 'font-medium' }, row.original.Descricao),
    },
];

const confirmDelete = (subject: Subject) => {
    subjectToDelete.value = subject;
    deleteMessage.value = `Tem certeza que deseja excluir o assunto "${subject.Descricao}"?`;
    showDeleteDialog.value = true;
};

const handleDelete = () => {
    if (subjectToDelete.value?.CodAs) {
        router.delete(route('subjects.destroy', { subject: subjectToDelete.value.CodAs.toString() }));
    }
};

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Assuntos',
        href: route('subjects.index'),
    },
];
</script>
