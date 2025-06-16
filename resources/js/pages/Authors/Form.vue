<template>
    <AppLayout title="Autores" :breadcrumbs="breadcrumbs">
        <Head :title="props.author?.CodAu ? 'Editar Autor' : 'Novo Autor'" />

        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                {{ props.author?.CodAu ? 'Editar Autor' : 'Novo Autor' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden border bg-white px-4 pb-4 shadow-sm sm:rounded-lg dark:border-[#27272A] dark:bg-transparent">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-2">
                                <InputLabel for="Nome">Nome</InputLabel>
                                <Input id="Nome" type="text" class="mt-1" v-model="form.Nome" autofocus />
                            </div>
                            <div v-if="form.errors.Nome" class="mb-4 text-sm text-red-600">
                                {{ form.errors.Nome }}
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div class="w-6/12">
                                    <Link :href="route('authors.index')">
                                        <SecondaryButton>Cancelar</SecondaryButton>
                                    </Link>
                                </div>
                                <div class="w-6/12">
                                    <PrimaryButton type="submit" :disabled="form.processing">
                                        {{ props.author?.CodAu ? 'Atualizar' : 'Salvar' }}
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
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    author: Object,
});

const form = useForm({
    CodAu: props.author?.CodAu || '',
    Nome: props.author?.Nome || '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Autores',
        href: route('authors.index'),
    },
    {
        title: props.author?.CodAu ? 'Editar Autor' : 'Novo Autor',
        href: route('authors.create'),
    },
];

const submit = () => {
    if (props.author?.CodAu) {
        form.put(route('authors.update', props.author.CodAu));
    } else {
        form.post(route('authors.store'));
    }
};
</script>
