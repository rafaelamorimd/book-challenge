<script setup>
import { MoreHorizontal } from 'lucide-vue-next'
import { router } from "@inertiajs/vue3";
import { Trash2, SquarePen } from "lucide-vue-next";

import {
  DropdownMenu,
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  DropdownMenuTrigger,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
} from '@/components/ui/dropdown-menu'

import { Button } from '@/components/ui/button'
import { useClipboard } from '@vueuse/core';

const {copy} = useClipboard();

const props = defineProps({
  row: {
    type: Object,
    required: true,
    },
    show: {
        type: String,
        required: true,
    },
    delete: {
        type: String,
        required: true,
    },
});

const copyRowIdToClipboard = (id) => {
    copy(id);
    alert(`id copiado: ${id}`);
}

const handleDelete = (id) => {
    if (confirm('Tem certeza que deseja excluir este item?')) {
        router.delete(props.delete.replace(':id', id), {
            preserveScroll: true,
        })
    }
}

const handleShow = (id) => {
    router.get(props.show.replace(':id', id));
}

</script>

<template>
  <DropdownMenu>
    <DropdownMenuTrigger as-child>
      <Button variant="ghost" class="w-8 h-8 p-0 dark:hover:bg-[#27272A] dark:hover:text-slate-200">
        <span class="sr-only">Open menu</span>
        <MoreHorizontal class="w-4 h-4" />
      </Button>
    </DropdownMenuTrigger>
    <DropdownMenuContent align="end">
      <DropdownMenuLabel>Ações</DropdownMenuLabel>
      <DropdownMenuItem @click="copyRowIdToClipboard(row.original.id)">
        Copiar id
      </DropdownMenuItem>
      <DropdownMenuSeparator />
      <DropdownMenuItem @click="handleDelete(row.original.id)">
        <Trash2 class="w-4 h-4 mr-2" />
        Deletar
      </DropdownMenuItem>
      <DropdownMenuItem @click="handleShow(row.original.id)">
        <SquarePen class="w-4 h-4 mr-2"/>
        Editar
      </DropdownMenuItem>
    </DropdownMenuContent>
  </DropdownMenu>
</template>
