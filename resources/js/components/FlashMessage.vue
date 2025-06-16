<script setup lang="ts">
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';
import { toast, Toaster } from 'vue-sonner';

const page = usePage();
const toastType = ref<'success' | 'error' | null>(null);

const showFlashMessages = () => {
    const message = page.props.flash;
    const errors = page.props.errors ?? {};

    if (page.props.flash.success) {
        toastType.value = 'success';
        toast.success(message.success, {
            onDismiss: () => {
                toastType.value = null;
            },
        });
    }

    if (page.props.flash.error) {
        toastType.value = 'error';
        toast.error(message.error, {
            onDismiss: () => {
                toastType.value = null;
            },
        });
    }

    if (Object.keys(errors).length > 0) {
        toastType.value = 'error';
        toast.error(Object.values(errors)[0], {
            onDismiss: () => {
                toastType.value = null;
            },
        });
    }

    setTimeout(() => {
        page.props.flash.success = null;
        page.props.flash.error = null;
        page.props.errors = null;
    }, 300);
};

watch(
    () => page.props,
    () => {
        showFlashMessages();
    },
    { immediate: true, deep: true },
);

onMounted(() => {
    showFlashMessages();
});
</script>

<template>
    <Toaster
        position="top-right"
        richColors
        :expand="false"
        :duration="4000"
        :toastOptions="{
            style: {
                maxWidth: '400px',
                width: 'auto',
                padding: '12px 16px',
                fontSize: '14px',
                borderRadius: '12px',
                boxShadow: '0 4px 12px rgba(0, 0, 0, 0.12)',
                backgroundColor: toastType === 'success' ? '#10B981' : toastType === 'error' ? '#EF4444' : undefined,
                color: toastType ? '#FFFFFF' : undefined,
                fontWeight: '500',
                lineHeight: '1.4',
                border: '2px solid',
                borderColor: toastType === 'success' ? '#059669' : toastType === 'error' ? '#DC2626' : undefined,
                display: 'flex',
                alignItems: 'center',
                gap: '12px',
            },
            icon: {
                style: {
                    marginRight: '0',
                    marginLeft: '0',
                },
            },
        }"
        :class="[
            '!fixed !top-4 !right-4 !z-50 !rounded-xl',
            {
                '!border-green-600 !bg-green-500': toastType === 'success',
                '!border-red-600 !bg-red-500': toastType === 'error',
            },
        ]"
    />
</template>
