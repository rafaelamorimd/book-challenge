<template>
    <div class="relative">
        <MultiSelect
            v-model="selectedValues"
            :options="options"
            :optionLabel="optionLabel"
            :optionValue="optionValue"
            :placeholder="placeholder"
            :class="[
                'w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background',
                'focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2',
                'disabled:cursor-not-allowed disabled:opacity-50',
                'dark:border-[#27272A] dark:bg-transparent',
            ]"
            :panelClass="'dark:bg-[#18181B] dark:border-[#27272A]'"
            :filter="true"
            :showClear="true"
            :maxSelectedLabels="3"
            :selectedItemsLabel="{0: 'itens selecionados', one: 'item selecionado', other: 'itens selecionados'}"
            :emptyFilterMessage="'Nenhum resultado encontrado'"
            :emptyMessage="'Nenhuma opção disponível'"
            @change="onChange"
        />
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import MultiSelect from 'primevue/multiselect';

const props = defineProps({
    modelValue: {
        type: Array,
        default: () => [],
    },
    options: {
        type: Array,
        required: true,
    },
    optionLabel: {
        type: String,
        default: 'label',
    },
    optionValue: {
        type: String,
        default: 'value',
    },
    placeholder: {
        type: String,
        default: 'Selecione...',
    },
});

const emit = defineEmits(['update:modelValue']);

const selectedValues = ref(props.modelValue);

watch(() => props.modelValue, (newValue) => {
    selectedValues.value = newValue;
});

const onChange = (event: any) => {
    emit('update:modelValue', event.value);
};
</script>

<style>
.p-multiselect {
    @apply w-full;
}

.p-multiselect-label {
    @apply py-1;
}

.p-multiselect-token {
    @apply bg-primary text-primary-foreground;
}

.p-multiselect-token-icon {
    @apply text-primary-foreground;
}

.p-multiselect-panel {
    @apply border border-input bg-background;
}

.p-multiselect-item {
    @apply text-sm;
}

.p-multiselect-item.p-highlight {
    @apply bg-primary text-primary-foreground;
}

.p-multiselect-filter-container .p-inputtext {
    @apply w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background;
    @apply focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2;
    @apply dark:border-[#27272A] dark:bg-transparent;
}
</style>
