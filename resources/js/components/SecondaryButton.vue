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
        class="w-full rounded-md border border-gray-300 bg-transparent px-6 py-1.5 text-gray-700 transition-colors duration-200 hover:border-purple-600 hover:bg-purple-50 hover:text-purple-600 dark:border-gray-600 dark:text-gray-300 dark:hover:border-purple-400 dark:hover:bg-purple-900/20 dark:hover:text-purple-400"
    >
        <slot />
    </button>
</template>
