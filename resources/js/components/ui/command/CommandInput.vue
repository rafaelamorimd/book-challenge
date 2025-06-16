<script setup>
import { cn } from '@/lib/utils';
import { Search } from 'lucide-vue-next';
import { ComboboxInput, useForwardProps } from 'radix-vue';
import { computed } from 'vue';

defineOptions({
  inheritAttrs: false,
});

const props = defineProps({
  modelValue: { type: String, required: false },
  type: { type: String, required: false },
  disabled: { type: Boolean, required: false },
  autoFocus: { type: Boolean, required: false },
  asChild: { type: Boolean, required: false },
  as: { type: null, required: false },
  class: { type: null, required: false },
});

const emit = defineEmits(['update:modelValue']);

const delegatedProps = computed(() => {
  const { class: _, modelValue: __, ...delegated } = props;
  return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);

const handleInput = (event) => {
  emit('update:modelValue', event.target.value);
};
</script>

<template>
  <div class="flex items-center border-b px-3 py-2" cmdk-input-wrapper>
    <Search class="mr-2 h-4 w-4 shrink-0 opacity-50" />
    <ComboboxInput
      v-bind="{ ...forwardedProps, ...$attrs }"
      :value="modelValue"
      @input="handleInput"
      auto-focus
      :class="
        cn(
          'flex h-8 w-full border-none outline-none rounded-md bg-transparent text-sm placeholder:text-muted-foreground disabled:cursor-not-allowed disabled:opacity-50',
          props.class,
        )
      "
    />
  </div>
</template>
