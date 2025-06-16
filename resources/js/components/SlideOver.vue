<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { X } from 'lucide-vue-next';

defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        required: true,
    },
    subtitle: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['close']);

const closeSlide = () => {
    console.log('closeSlide');
    emit('close');
};
</script>

<template>
    <!-- Overlay com sua própria transição -->
    <Transition
        enter-active-class="transition ease-in-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in-out duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="show" @click.self="closeSlide" class="fixed inset-0 z-40 bg-black/30" />
    </Transition>

    <!-- Slide com sua própria transição -->
    <Transition
        enter-active-class="transition ease-in-out duration-300 transform"
        enter-from-class="translate-x-full"
        enter-to-class="translate-x-0"
        leave-active-class="transition ease-in-out duration-300 transform"
        leave-from-class="translate-x-0"
        leave-to-class="translate-x-full"
    >
        <div v-if="show" class="pointer-events-none fixed inset-y-0 right-0 z-50 flex pl-10">
            <div class="pointer-events-auto w-screen max-w-md">
                <div class="flex h-full flex-col bg-background">
                    <!-- Header -->
                    <div class="px-4 py-6 sm:px-6">
                        <div class="flex items-start justify-between">
                            <div>
                                <h2 class="text-xl font-semibold">{{ title }}</h2>
                                <p v-if="subtitle" class="mt-1 text-sm text-muted-foreground">
                                    {{ subtitle }}
                                </p>
                            </div>
                            <Button variant="ghost" size="icon" class="rounded-full" @click="closeSlide">
                                <X class="h-4 w-4" />
                            </Button>
                        </div>
                    </div>

                    <!-- Divisor -->
                    <div class="border-b dark:border-[#27272A]" />

                    <!-- Conteúdo -->
                    <div class="relative flex-1 px-4 sm:px-6">
                        <slot name="content" />
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
