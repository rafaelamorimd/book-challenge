<template>
    <AppLayout title="Assuntos" :breadcrumbs="breadcrumbs">
        <Head :title="props.subject?.CodAs ? 'Editar Assunto' : 'Novo Assunto'" />

        <template #header>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                {{ props.subject?.CodAs ? 'Editar Assunto' : 'Novo Assunto' }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden border bg-white px-4 pb-4 shadow-sm sm:rounded-lg dark:border-[#27272A] dark:bg-transparent">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div class="mb-2">
                                <InputLabel for="Descricao">Descrição</InputLabel>
                                <Input id="Descricao" type="text" class="mt-1" v-model="form.Descricao" autofocus />
                            </div>
                            <div v-if="form.errors.Descricao" class="mb-4 text-sm text-red-600">
                                {{ form.errors.Descricao }}
                            </div>

                            <div class="mt-4 flex items-center justify-between gap-4">
                                <div class="w-6/12">
                                    <Link :href="route('subjects.index')">
                                        <SecondaryButton>Cancelar</SecondaryButton>
                                    </Link>
                                </div>
                                <div class="w-6/12">
                                    <PrimaryButton type="submit" :disabled="form.processing">
                                        {{ props.subject?.CodAs ? 'Atualizar' : 'Salvar' }}
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
    subject: Object,
});

const form = useForm({
    CodAs: props.subject?.CodAs || '',
    Descricao: props.subject?.Descricao || '',
});

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Assuntos',
        href: route('subjects.index'),
    },
    {
        title: props.subject?.CodAs ? 'Editar Assunto' : 'Novo Assunto',
        href: route('subjects.create'),
    },
];

const submit = () => {
    if (props.subject?.CodAs) {
        form.put(route('subjects.update', props.subject.CodAs));
    } else {
        form.post(route('subjects.store'));
    }
};
</script>
