<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { RangeCalendar } from '@/components/ui/range-calendar';
import { cn } from '@/lib/utils';
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
    startDate: {
        type: String,
    },
    endDate: {
        type: String,
    },
});

const emit = defineEmits(['update:dateRange', 'update:modelValue']);

const df = new DateFormatter('pt-BR', {
    dateStyle: 'medium',
});

const value = ref({
    start: props.startDate ? new CalendarDate(...props.startDate.split('-').map(Number)) : null,
    end: props.endDate ? new CalendarDate(...props.endDate.split('-').map(Number)) : null,
});

watch(
    value,
    (newValue) => {
        if (newValue.start && newValue.end) {
            emit('update:dateRange', {
                start: newValue.start.toString(),
                end: newValue.end.toString(),
            });
            emit('update:modelValue', {
                start: newValue.start.toString(),
                end: newValue.end.toString(),
            });
        }
    },
    { deep: true },
);
</script>

<template>
    <div>
        <Popover>
            <PopoverTrigger as-child>
                <Button variant="outline" :class="cn('w-full justify-start text-left font-normal', !value && 'text-muted-foreground')">
                    <CalendarIcon class="h-4 w-4" />
                    <template v-if="value.start">
                        <template v-if="value.end">
                            {{ df.format(value.start.toDate(getLocalTimeZone())) }} -
                            {{ df.format(value.end.toDate(getLocalTimeZone())) }}
                        </template>
                        <template v-else>
                            {{ df.format(value.start.toDate(getLocalTimeZone())) }}
                        </template>
                    </template>
                    <template v-else> Selecione um período </template>
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-auto p-0">
                <RangeCalendar v-model="value" initial-focus :number-of-months="2" locale="pt-BR" />
            </PopoverContent>
        </Popover>
    </div>
</template>
