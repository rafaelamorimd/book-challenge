<script setup>
import { computed } from 'vue'
import { Check, PlusCircle } from 'lucide-vue-next'

import { Badge } from '@/components/ui/badge'
import { Button } from '@/components/ui/button'
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, CommandSeparator } from '@/components/ui/command'

import {
  Popover,
  PopoverContent,
  PopoverTrigger,
} from '@/components/ui/popover'
import { Separator } from '@/components/ui/separator'
import { cn } from '@/lib/utils'

const props = defineProps({
  column: {
    type: Object,
    required: true,
  },
  title: {
    type: String,
    required: true,
  },
  options: {
    type: Array,
    required: true,
  },
  startOptions: {
    type: Array,
    required: false,
  }
})

const facets = computed(() => props.column?.getFacetedUniqueValues())
const selectedValues = computed(() => new Set(props.startOptions ?? []))
</script>

<template>
  <Popover>
    <PopoverTrigger as-child>
      <Button variant="outline" size="sm" class="w-full h-8 border-dashed dark:bg-transparent dark:hover:bg-[#27272A] dark:hover:text-slate-200 dark:text-slate-200 dark:border-[#27272A] ">
        <PlusCircle class="mr-2 h-4 w-4 " />
        {{ title }}
        <template v-if="selectedValues.size > 0">
          <Separator orientation="vertical" class="mx-2 h-4 dark:bg-[#27272A]" />
          <Badge variant="secondary" class="rounded-sm px-1 font-normal lg:hidden">
            {{ selectedValues.size }}
          </Badge>
          <div class="hidden space-x-1 lg:flex">
            <Badge v-if="selectedValues.size > 2" variant="secondary" class="rounded-sm px-1 font-normal">
              {{ selectedValues.size }} selected
            </Badge>

            <template v-else>
              <Badge v-for="option in options
                .filter((option) => selectedValues.has(option.value))" :key="option.value" variant="secondary"
                class="rounded-sm px-1 font-normal">
                {{ option.label }}
              </Badge>
            </template>
          </div>
        </template>
      </Button>
    </PopoverTrigger>
    <PopoverContent class="w-full p-0 dark:bg-[#27272A] dark:border-[#27272A]" align="start">
      <Command
        :filter-function="(list, term) => list.filter(i => i.label.toLowerCase()?.includes(term))">
        <CommandInput :placeholder="title" />
        <CommandList class="border-[#27272A]">
          <CommandEmpty>
            Sem resultados.
          </CommandEmpty>
          <CommandGroup>
            <CommandItem v-for="option in options" :key="option.value" :value="option" @select="() => {
              const isSelected = selectedValues.has(option.value)
              if (isSelected) {
                selectedValues.delete(option.value)
                $emit('filter-changed', selectedValues)
              }
              else {
                selectedValues.add(option.value)
                $emit('filter-changed', selectedValues)
                console.log('passouSE', selectedValues)
              }
              const filterValues = Array.from(selectedValues)
              column?.setFilterValue(
                filterValues.length ? filterValues : undefined,
              )
            }">
              <div :class="cn(
                'mr-2 flex h-4 w-4 items-center justify-center rounded-sm border border-primary',
                selectedValues.has(option.value)
                  ? 'bg-primary text-primary-foreground'
                  : 'opacity-50 [&_svg]:invisible',
              )">
                <Check :class="cn('h-4 w-4')" />
              </div>
              <!-- <option.icon v-if="option.icon" class="mr-2 h-4 w-4 text-muted-foreground" /> -->
              <span>{{ option.label }}</span>
              <span v-if="facets?.get(option.value)"
                class="ml-auto flex h-4 w-4 items-center justify-center font-mono text-xs">
                {{ facets.get(option.value) }}
              </span>
            </CommandItem>
          </CommandGroup>

          <template v-if="selectedValues.size > 0">
            <CommandSeparator />
            <CommandGroup>
              <CommandItem :value="{ label: 'Clear filters' }" class="justify-center text-center"
                @select="() => {
                  column?.setFilterValue(undefined)
                  $emit('filter-changed', {})
                }">
                Limpar filtros
              </CommandItem>
            </CommandGroup>
          </template>
        </CommandList>
      </Command>
    </PopoverContent>
  </Popover>
</template>
