<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';

interface Props {
    show: boolean;
    title?: string;
    message?: string;
    confirmText?: string;
    cancelText?: string;
    confirmButtonClass?: string;
}

withDefaults(defineProps<Props>(), {
    title: 'Confirmar Exclusão',
    message: 'Tem certeza que deseja excluir este item?',
    confirmText: 'Excluir',
    cancelText: 'Cancelar',
    confirmButtonClass: 'bg-red-600 hover:bg-red-700',
});

const emit = defineEmits<{
    'update:show': [value: boolean];
    confirm: [];
    cancel: [];
}>();

const handleClose = () => {
    emit('update:show', false);
    emit('cancel');
};

const handleConfirm = () => {
    emit('confirm');
    emit('update:show', false);
};
</script>

<template>
    <Dialog :open="show" @update:open="handleClose">
        <DialogContent>
            <DialogHeader>
                <DialogTitle>{{ title }}</DialogTitle>
            </DialogHeader>
            <div class="py-4">
                <p>{{ message }}</p>
            </div>
            <DialogFooter class="flex justify-end gap-2">
                <Button variant="outline" @click="handleClose">
                    {{ cancelText }}
                </Button>
                <Button @click="handleConfirm" :class="confirmButtonClass">
                    {{ confirmText }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
