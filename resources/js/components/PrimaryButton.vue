<script setup lang="ts">
withDefaults(
    defineProps<{
        type?: 'button' | 'submit' | 'reset';
    }>(),
    {
        type: 'button',
    },
);
</script>

<template>
    <button
        :type="type"
        class="w-full rounded-md bg-orange-600 px-6 py-1.5 text-white transition-colors duration-200 hover:bg-orange-700 hover:text-white"
    >
        <slot />
    </button>
</template>
