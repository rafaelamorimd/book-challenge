<template>
    <AppLayout title="Livros" :breadcrumbs="breadcrumbs">
        <Head :title="props.book?.codl ? 'Editar Livro' : 'Novo Livro'" />

        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                {{ props.book?.codl ? 'Editar Livro' : 'Novo Livro' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden border bg-white px-4 pb-4 shadow-sm sm:rounded-lg dark:border-[#27272A] dark:bg-[#18181B]">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-2">
                                <InputLabel for="Titulo" class="dark:text-gray-200">Título</InputLabel>
                                <Input
                                    id="Titulo"
                                    type="text"
                                    class="mt-1 dark:border-[#3F3F46] dark:bg-[#27272A] dark:text-gray-200 dark:placeholder-gray-400"
                                    v-model="form.titulo"
                                    autofocus
                                />
                            </div>
                            <div v-if="form.errors.titulo" class="mb-4 text-sm text-red-500 dark:text-red-400">
                                {{ form.errors.titulo }}
                            </div>
                            <div class="mb-2">
                                <InputLabel for="subjects" class="dark:text-gray-200">Assuntos</InputLabel>
                                <MultiSelect
                                    id="subjects"
                                    v-model="form.subjects"
                                    :options="subjectOptions"
                                    placeholder="Selecione os assuntos..."
                                    class="dark:border-[#3F3F46] dark:bg-[#27272A] dark:text-gray-200"
                                />
                            </div>
                            <div v-if="form.errors.subjects" class="mb-4 text-sm text-red-500 dark:text-red-400">
                                {{ form.errors.subjects }}
                            </div>
                            <div class="mb-2">
                                <InputLabel for="authors" class="dark:text-gray-200">Autores</InputLabel>
                                <MultiSelect
                                    id="authors"
                                    v-model="form.authors"
                                    :options="authorOptions"
                                    placeholder="Selecione os autores..."
                                    class="dark:border-[#3F3F46] dark:bg-[#27272A] dark:text-gray-200"
                                />
                            </div>
                            <div v-if="form.errors.authors" class="mb-4 text-sm text-red-500 dark:text-red-400">
                                {{ form.errors.authors }}
                            </div>
                            <div class="mb-2">
                                <InputLabel for="Editora" class="dark:text-gray-200">Editora</InputLabel>
                                <Input
                                    id="Editora"
                                    type="text"
                                    class="mt-1 dark:border-[#3F3F46] dark:bg-[#27272A] dark:text-gray-200 dark:placeholder-gray-400"
                                    v-model="form.editora"
                                />
                            </div>
                            <div v-if="form.errors.editora" class="mb-4 text-sm text-red-500 dark:text-red-400">
                                {{ form.errors.editora }}
                            </div>
                            <div class="mb-2">
                                <InputLabel for="Edicao" class="dark:text-gray-200">Edição</InputLabel>
                                <Input
                                    id="Edicao"
                                    type="number"
                                    class="mt-1 dark:border-[#3F3F46] dark:bg-[#27272A] dark:text-gray-200 dark:placeholder-gray-400"
                                    v-model="form.edicao"
                                    min="1"
                                    @input="form.edicao = String(form.edicao).replace(/[^0-9]/g, '')"
                                />
                            </div>
                            <div v-if="form.errors.edicao" class="mb-4 text-sm text-red-500 dark:text-red-400">
                                {{ form.errors.edicao }}
                            </div>
                            <div class="mb-2">
                                <InputLabel for="AnoPublicacao" class="dark:text-gray-200">Ano de Publicação</InputLabel>
                                <Input
                                    id="AnoPublicacao"
                                    type="number"
                                    class="mt-1 dark:border-[#3F3F46] dark:bg-[#27272A] dark:text-gray-200 dark:placeholder-gray-400"
                                    v-model="form.anoPublicacao"
                                    min="1000"
                                    :max="currentYear"
                                />
                            </div>
                            <div v-if="form.errors.anoPublicacao" class="mb-4 text-sm text-red-500 dark:text-red-400">
                                {{ form.errors.anoPublicacao }}
                            </div>
                            <div class="mb-2">
                                <InputLabel for="valor" class="dark:text-gray-200">Valor</InputLabel>
                                <Input
                                    id="valor"
                                    type="text"
                                    class="mt-1 dark:border-[#3F3F46] dark:bg-[#27272A] dark:text-gray-200 dark:placeholder-gray-400"
                                    v-model="form.valor"
                                    @input="validateNumericInput"
                                    placeholder="R$ 0,00"
                                />
                            </div>
                            <div v-if="form.errors.valor" class="mb-4 text-sm text-red-500 dark:text-red-400">
                                {{ form.errors.valor }}
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div class="w-6/12">
                                    <Link :href="route('books.index')">
                                        <SecondaryButton>Cancelar</SecondaryButton>
                                    </Link>
                                </div>
                                <div class="w-6/12">
                                    <PrimaryButton type="submit" :disabled="form.processing">
                                        {{ props.book?.codl ? 'Atualizar' : 'Salvar' }}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import InputLabel from '@/components/InputLabel.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SecondaryButton from '@/components/SecondaryButton.vue';
import MultiSelect from '@/components/ui/MultiSelect.vue';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

interface Author {
    CodAu: number;
    Nome: string;
}

interface Subject {
    CodAs: number;
    Descricao: string;
}

const props = defineProps<{
    book?: {
        codl: number;
        titulo: string;
        editora: string;
        edicao: number;
        anoPublicacao: number;
        valor: string;
        authors?: Author[];
        subjects?: Subject[];
    };
    allAuthors: Author[];
    allSubjects: Subject[];
}>();

const currentYear = new Date().getFullYear();

const authorOptions = computed(
    () =>
        props.allAuthors?.map((author) => ({
            value: author.CodAu,
            label: author.Nome,
        })) ?? [],
);

const subjectOptions = computed(
    () =>
        props.allSubjects?.map((subject) => ({
            value: subject.CodAs,
            label: subject.Descricao,
        })) ?? [],
);

const formatCurrency = (value: string | number): string => {
    if (!value || value === '') return '';

    const numericValue = value.toString().replace(/\D/g, '');

    if (!numericValue) return '';

    const floatValue = parseFloat(numericValue) / 100;

    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(floatValue);
};

const unformatCurrency = (value: string): string => {
    if (!value) return '';
    return value.replace(/\D/g, '');
};

const form = useForm({
    codl: props.book?.codl ?? null,
    titulo: props.book?.titulo ?? '',
    editora: props.book?.editora ?? '',
    edicao: Number(props.book?.edicao ?? null),
    anoPublicacao: Number(props.book?.anoPublicacao ?? null),
    valor: props.book?.valor ? formatCurrency(props.book.valor) : '',
    authors: props.book?.authors ? props.book.authors.map((author) => author.CodAu) : [],
    subjects: props.book?.subjects ? props.book.subjects.map((subject) => subject.CodAs) : [],
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Livros',
        href: route('books.index'),
    },
    {
        title: props.book?.codl ? 'Editar Livro' : 'Novo Livro',
        href: route('books.create'),
    },
];

const validateNumericInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    const value = input.value.replace(/\D/g, '');

    if (!value) {
        form.valor = '';
        return;
    }

    form.valor = formatCurrency(value);
};

const submit = () => {
    if (form.valor) {
        form.valor = (parseFloat(unformatCurrency(form.valor)) / 100).toFixed(2);
    } else {
        form.valor = '';
    }

    if (props.book?.codl) {
        form.put(route('books.update', props.book.codl));
    } else {
        form.post(route('books.store'));
    }
};
</script>
