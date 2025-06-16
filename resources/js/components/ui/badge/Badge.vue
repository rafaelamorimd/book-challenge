<script setup>
import { cn } from '@/lib/utils';
import { badgeVariants } from '@/components/ui/badge';

const props = defineProps({
  variant: { type: null, required: false },
  class: { type: null, required: false },
});
</script>

<template>
  <div :class="cn(badgeVariants({ variant }), props.class)">
    <slot />
  </div>
</template>
