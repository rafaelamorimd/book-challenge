<script setup lang="ts">
import { ref, watch, onMounted } from "vue";
import { Check, ChevronsUpDown, X } from "lucide-vue-next";
import { Button } from "@/components/ui/button";
import { useDebounceFn } from "@vueuse/core";
import axios from "axios";
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandItem,
    CommandList,
} from "@/components/ui/command";
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from "@/components/ui/popover";

interface Item {
    [key: string]: any;
    id: string | number;
    name: string;
}

const props = withDefaults(defineProps<{
    modelValue: Item | Item[] | null;
    model?: string;
    options?: Item[];
    labelField?: string;
    valueField?: string;
    placeholder?: string;
    filters?: Record<string, any>;
    multiple?: boolean;
    disabled?: boolean;
    initialId?: string | number | null;
}>(), {
    options: () => [],
    labelField: 'name',
    valueField: 'id',
    placeholder: 'Selecione uma opção...',
    filters: () => ({}),
    multiple: false,
    disabled: false,
    initialId: null
});

const emit = defineEmits<{
    "update:modelValue": [value: Item | Item[]];
    "filter-changed": [value: Item];
    "selected": [value: Item];
}>();

const open = ref(false);
const items = ref<Item[]>([]);
const loading = ref(false);
const searchQuery = ref("");
const selectedItems = ref<Item[]>([]);
const inputValue = ref('');

onMounted(async () => {
    if (props.multiple) {
        selectedItems.value = Array.isArray(props.modelValue)
            ? [...props.modelValue]
            : [];
    } else if (props.modelValue && typeof props.modelValue === "object") {
        selectedItems.value = [props.modelValue as Item];
    }
    await fetchInitialData();
});

const fetchInitialData = async () => {
    if (props.options.length > 0) {
        items.value = [...props.options];
        return;
    }

    if (!props.model) return;

    loading.value = true;
    try {
        const response = await axios.get(`/autocomplete/${props.model}`, {
            params: {
                filters: props.filters,
                initialId: props.initialId,
            },
        });
        items.value = response.data || [];
        if (props.initialId) {
            const initialItem = items.value.find(
                (item) => item[props.valueField] == props.initialId,
            );
            if (initialItem) {
                emit("update:modelValue", initialItem);
            }
        }
    } catch (error) {
        console.error("Erro ao buscar dados iniciais:", error);
        items.value = [];
    } finally {
        loading.value = false;
    }
};

const searchItems = useDebounceFn(async (value: string) => {
    if (props.options.length > 0) {
        if (value) {
            items.value = props.options.filter((item) =>
                item[props.labelField]
                    .toLowerCase()
                    .includes(value.toLowerCase()),
            );
        } else {
            items.value = [...props.options];
        }
        return;
    }

    if (value && props.model) {
        loading.value = true;
        try {
            const response = await axios.get(`/autocomplete/${props.model}`, {
                params: {
                    query: value,
                    filters: props.filters,
                    labelField: props.labelField,
                },
            });
            items.value = response.data || [];
        } catch (error) {
            console.error("Erro ao buscar dados:", error);
            items.value = [];
        } finally {
            loading.value = false;
        }
    } else {
        await fetchInitialData();
    }
}, 300);

const handleSearch = (value: string) => {
    searchQuery.value = value;
    searchItems(value);
};

const isSelected = (item: Item): boolean => {
    if (props.multiple) {
        return selectedItems.value.some(
            (selected) => selected[props.valueField] === item[props.valueField],
        );
    }
    return (props.modelValue as Item)?.[props.valueField] === item[props.valueField];
};

const selectItem = (item: Item) => {
    if (props.multiple) {
        const index = selectedItems.value.findIndex(
            (selected) => selected[props.valueField] === item[props.valueField],
        );
        if (index === -1) {
            selectedItems.value.push(item);
        } else {
            selectedItems.value.splice(index, 1);
        }
        emit("update:modelValue", selectedItems.value);
    } else {
        emit("update:modelValue", item);
        emit("selected", item);
        open.value = false;
    }
    searchQuery.value = "";
};

const removeItem = (item: Item) => {
    const index = selectedItems.value.findIndex(
        (selected) => selected[props.valueField] === item[props.valueField],
    );
    if (index !== -1) {
        selectedItems.value.splice(index, 1);
        emit("update:modelValue", selectedItems.value);
    }
};

watch(
    () => props.filters,
    async () => {
        await fetchInitialData();
    },
    { deep: true },
);

watch(() => props.model, (val) => { inputValue.value = val || ''; });
</script>

<template>
    <div class="relative">
        <div
            v-if="multiple && selectedItems.length > 0"
            class="mb-2 flex flex-wrap gap-2"
        >
            <div
                v-for="item in selectedItems"
                :key="item[valueField]"
                class="flex items-center gap-1 rounded-md bg-primary/10 px-2 py-1 text-sm"
            >
                <span>{{ item[labelField] }}</span>
                <button
                    @click="removeItem(item)"
                    :disabled="disabled"
                    class="text-muted-foreground hover:text-foreground"
                    :class="{ 'cursor-not-allowed opacity-50': disabled }"
                >
                    <X class="h-3 w-3" />
                </button>
            </div>
        </div>

        <Popover v-model:open="open">
            <PopoverTrigger as-child>
                <Button
                    variant="outline"
                    role="combobox"
                    :aria-expanded="open"
                    :disabled="disabled"
                    class="w-full justify-between"
                >
                    <span v-if="!multiple">
                        {{ (props.modelValue as Item)?.[props.labelField] || props.placeholder }}
                    </span>
                    <span v-else>
                        {{ props.placeholder }}
                    </span>
                    <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-[--radix-popover-trigger-width] p-0">
                <Command>
                    <CommandInput
                        :model-value="searchQuery"
                        @update:model-value="handleSearch"
                        :placeholder="props.placeholder"
                    />
                    <CommandList>
                        <CommandEmpty>
                            <div
                                v-if="loading"
                                class="flex items-center justify-center py-6"
                            >
                                <div
                                    class="h-4 w-4 animate-spin rounded-full border-2 border-primary border-t-transparent"
                                ></div>
                                <span class="ml-2">Buscando...</span>
                            </div>
                            <div v-else>Nenhum resultado encontrado.</div>
                        </CommandEmpty>
                        <CommandGroup>
                            <CommandItem
                                v-for="item in items"
                                :key="item[valueField]"
                                :value="item"
                                @select="
                                    () => {
                                        selectItem(item);
                                        emit('filter-changed', item);
                                    }
                                "
                            >
                                <Check
                                    :class="[
                                        'mr-2 h-4 w-4',
                                        isSelected(item)
                                            ? 'opacity-100'
                                            : 'opacity-0',
                                    ]"
                                />
                                {{ item[labelField] }}
                            </CommandItem>
                        </CommandGroup>
                    </CommandList>
                </Command>
            </PopoverContent>
        </Popover>
    </div>
</template>
