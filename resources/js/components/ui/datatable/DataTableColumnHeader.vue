<script setup>
import {
  ArrowDownIcon,
  ArrowUpIcon,
  ChevronsUpDown,
  EyeOff,
} from 'lucide-vue-next'

import { cn } from '@/lib/utils'
import { Button } from '@/components/ui/button'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

const props = defineProps({
  column: {
    type: Object,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
})
</script>

<script>
export default {
  inheritAttrs: false,
}
</script>

<template>
  <div v-if="column.getCanSort()" :class="cn('flex items-center space-x-2', $attrs.class ?? '')">
    <DropdownMenu>
      <DropdownMenuTrigger as-child>
        <Button variant="ghost" size="sm" class="-ml-3 h-8 data-[state=open]:bg-accent data-[state=open]:dark:bg-[#27272A] data-[state=open]:dark:text-[#A1A1AA] dark:hover:bg-[#27272A] dark:hover:text-[#A1A1AA]">
          <span>{{ title }}</span>
          <ArrowDownIcon v-if="column.getIsSorted() === 'desc'" class="w-4 h-4 ml-2" />
          <ArrowUpIcon v-else-if="column.getIsSorted() === 'asc'" class="w-4 h-4 ml-2" />
          <ChevronsUpDown v-else class="w-4 h-4 ml-2" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="start" class="dark:bg-[#09090B] dark:border-[#27272A] dark:text-slate-200">
        <DropdownMenuItem @click="column.toggleSorting(false)">
          <ArrowUpIcon class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
          Asc
        </DropdownMenuItem>
        <DropdownMenuItem @click="column.toggleSorting(true)">
          <ArrowDownIcon class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
          Desc
        </DropdownMenuItem>
        <DropdownMenuSeparator class="dark:bg-[#27272a]"/>
        <DropdownMenuItem @click="column.toggleVisibility(false)">
          <EyeOff class="mr-2 h-3.5 w-3.5 text-muted-foreground/70" />
          Hide
        </DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  </div>

  <div v-else :class="$attrs.class">
    {{ title }}
  </div>
</template>
