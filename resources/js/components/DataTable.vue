<script setup lang="ts">
import { router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import { debounce } from 'lodash';
import { ArrowDownToLine } from 'lucide-vue-next';
import { onMounted, ref, watch } from 'vue';

import { FlexRender, getCoreRowModel, getFilteredRowModel, getPaginationRowModel, getSortedRowModel, useVueTable } from '@tanstack/vue-table';

import { valueUpdater } from '@/lib/utils';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Pagination,
    PaginationEllipsis,
    PaginationFirst,
    PaginationLast,
    PaginationList,
    PaginationListItem,
    PaginationNext,
    PaginationPrev,
} from '@/components/ui/pagination';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

import DateRange from '@/components/DateRange.vue';
import SlideOver from '@/components/SlideOver.vue';
import AutoComplete from '@/components/ui/autocomplete/AutoComplete.vue';
import DataTableFacetedFilter from '@/components/ui/datatable/DataTableFacetedFilter.vue';

const props = defineProps({
    data: {
        type: Object,
        default: () => ({}),
        required: true,
    },
    columns: {
        type: Array,
        required: true,
    },
    paginate: {
        type: Boolean,
        default: true,
    },
    showStatus: {
        type: Boolean,
        default: false,
    },
    statuses: {
        type: Array,
        default: () => [],
    },
    showColumnFilter: {
        type: Boolean,
        default: false,
    },
    searchPlaceholder: {
        type: String,
        default: 'Buscar...',
    },
    filters: {
        type: Array,
        default: () => [],
    },
    report: {
        type: Boolean,
        default: false,
    },
    reportFilters: {
        type: Array,
        default: () => [],
    },
    reportModel: {
        type: String,
        default: '',
    },
    reportLabels: {
        type: Array,
        default: () => [],
    },
});

const sorting = ref([]);
const columnFilters = ref([]);
const rowSelection = ref({});
const searchTerm = ref('');
const searchInput = ref(null);
const showReport = ref(false);
const fieldReportModel = ref(
    props.reportFilters.reduce(
        (acc, filter) => ({
            ...acc,
            [filter.key]: {
                key: filter.key,
                type: filter.type,
                label: filter.label,
                value: null,
            },
        }),
        {},
    ),
);

const page = usePage();

onMounted(() => {
    if (page.url.includes('search=')) {
        searchInput.value?.$el?.focus();
    }
});

const handleSearch = debounce((value) => {
    router.get(
        route(route().current()),
        {
            search: value,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}, 300);

watch(searchTerm, (newValue) => {
    handleSearch(newValue);
});

const tableData = ref(props.data.data);

const handleFilterInputChange = (value, key, type) => {
    const oldParams = route().params;
    switch (type) {
        case 'autocomplete':
            delete oldParams[key];
            router.get(
                route(route().current()),
                {
                    ...oldParams,
                    [key]: value.id,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
            break;
        case 'faceted':
            const selectedValues = Array.from(value);
            if (selectedValues.length === 0) {
                const oldParams = route().params;
                delete oldParams[key];
                router.get(
                    route(route().current()),
                    {
                        ...oldParams,
                    },
                    {
                        preserveState: true,
                        preserveScroll: true,
                    },
                );
            }
            const query = selectedValues.map((item) => `${item}`).join(',');
            router.get(
                route(route().current()),
                {
                    ...route().params,
                    [key]: query,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
            break;
        case 'date_range':
            router.get(
                route(route().current()),
                {
                    ...route().params,
                    [key]: value.start + ',' + value.end,
                },
                {
                    preserveState: true,
                    preserveScroll: true,
                },
            );
            break;
    }
};

const table = useVueTable({
    data: tableData.value,
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnFilters),
    getFilteredRowModel: getFilteredRowModel(),
    onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
        get rowSelection() {
            return rowSelection.value;
        },
    },
});

onMounted(() => {
    router.on('finish', () => {
        hasRouteParams.value = Object.keys(route().params).length > 0;
    });
});

const hasRouteParams = ref(Object.keys(route().params).length > 0);

const filtersModel = ref({});

const handleGenerateReport = () => {
    router.post(
        route('report.generate'),
        {
            filters: fieldReportModel.value,
            model: props.reportModel,
            labels: props.reportLabels,
        },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                updateReports();
            },
        },
    );
};

const updateReports = () => {
    axios.get(route('reports.all')).then((response) => {
        reports.value = response.data;
    });
};

const reports = ref([]);
watch(
    showReport,
    (newValue) => {
        if (newValue) {
            updateReports();
        }
    },
    {
        immediate: true,
    },
);

const clearFilters = () => {
    const currentRoute = route().current();
    if (currentRoute) {
        router.visit(route(currentRoute), {
            preserveState: false,
            preserveScroll: true,
            replace: true,
        });
    }
};
</script>

<template>
    <div class="h-full flex-1 flex-col md:flex">
        <div class="flex items-start justify-between gap-x-4 py-4">
            <div class="flex grid w-full grid-cols-12 items-center gap-x-4 gap-y-2">
                <Input ref="searchInput" :placeholder="searchPlaceholder" v-model="searchTerm" class="col-span-4 h-8 w-full dark:border-[#27272A]" />
                <div v-for="filter in filters" :key="filter.key" :class="[filter.colspan ? `col-span-${filter.colspan}` : 'col-span-3']">
                    <AutoComplete
                        v-if="filter.type === 'autocomplete'"
                        v-model="filtersModel[filter.key]"
                        @filter-changed="(value) => handleFilterInputChange(value, filter.key, filter.type)"
                        :model="filter.model"
                        :filters="filter.filters"
                        :placeholder="filter.placeholder"
                    />
                    <DataTableFacetedFilter
                        v-if="filter.type === 'faceted'"
                        :start-options="route().params?.[filter.key] ? new Set(route().params[filter.key].split(',').map(Number)) : new Set()"
                        :column="table.getColumn(filter.key)"
                        title="Situação"
                        :options="filter.options"
                        @filter-changed="(value) => handleFilterInputChange(value, filter.key, filter.type)"
                    />
                    <DateRange
                        v-if="filter.type === 'date_range'"
                        v-model="filtersModel[filter.key]"
                        :class="filter.colspan ? `col-span-${filter.colspan}` : 'col-span-3'"
                        @update:dateRange="(value) => handleFilterInputChange(value, filter.key, filter.type)"
                    />
                </div>
            </div>

            <div class="flex w-[340px] flex-wrap items-center justify-end gap-x-4 gap-y-2">
                <Button variant="outline" class="h-8" @click="clearFilters"> Limpar Filtro </Button>
                <SlideOver :show="showReport" title="Relatórios" subtitle="Selecione o relatório que deseja gerar" @close="showReport = false">
                    <template #content>
                        <div class="mt-4">
                            <div class="grid grid-cols-12 gap-4">
                                <div
                                    v-for="filter in reportFilters"
                                    :key="filter.key"
                                    :class="[filter.colspan ? `col-span-${filter.colspan}` : 'col-span-3']"
                                >
                                    <label>{{ filter.label }}</label>
                                    <AutoComplete
                                        v-if="filter.type === 'autocomplete'"
                                        v-model="fieldReportModel[filter.key].value"
                                        :model="filter.model"
                                        :filters="filter.filters"
                                        :placeholder="filter.placeholder"
                                        :multiple="filter.multiple ?? false"
                                    />
                                    <DataTableFacetedFilter
                                        v-if="filter.type === 'faceted'"
                                        :column="table.getColumn(filter.key)"
                                        title="Situação"
                                        :options="filter.options"
                                        @filter-changed="(value) => (fieldReportModel[filter.key].value = value)"
                                    />
                                    <DateRange
                                        v-if="filter.type === 'date_range'"
                                        v-model="fieldReportModel[filter.key].value"
                                        :class="filter.colspan ? `col-span-${filter.colspan}` : 'col-span-12'"
                                    />
                                </div>
                            </div>
                            <Button class="mt-4 w-full" @click="handleGenerateReport"> Gerar Relatório </Button>

                            <div class="mt-6 border-t pt-4">
                                <h3 class="mb-3 text-sm font-medium">Relatórios Disponíveis</h3>

                                <div v-if="reports.length" class="space-y-2">
                                    <div
                                        v-for="report in reports"
                                        :key="report.id"
                                        class="flex items-center justify-between rounded-md p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                                    >
                                        <span class="text-sm">{{ report.name }}</span>
                                        <a :href="route('report.get', report.id)" target="_blank">
                                            <Button variant="ghost" size="sm" class="h-8 w-8 p-0">
                                                <ArrowDownToLine class="h-4 w-4" />
                                            </Button>
                                        </a>
                                    </div>
                                </div>
                                <div v-else class="py-4 text-center text-sm text-gray-500 dark:text-gray-400">Sem relatórios disponíveis.</div>
                            </div>
                        </div>
                    </template>
                </SlideOver>
                <Button variant="outline" class="h-8" v-if="report" @click="() => (showReport = true)">
                    <ArrowDownToLine class="mr-2 h-4 w-4" />
                    Relatórios
                </Button>
            </div>
        </div>
        <div class="rounded-md border dark:border-[#27272A]">
            <Table>
                <TableHeader>
                    <TableRow class="dark:border-[#27272A]" v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                        <TableHead v-for="header in headerGroup.headers" :key="header.id">
                            <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <template v-if="data.data?.length">
                        <TableRow v-for="row in data.data" :key="row.id">
                            <TableCell v-for="column in table.getAllColumns().filter((col) => col.getIsVisible())" :key="column.id">
                                <slot v-if="$slots[column.columnDef.accessorKey]" :name="column.columnDef.accessorKey" :row="row" />
                                <template v-else>
                                    {{
                                        column.columnDef.cell
                                            ? column.columnDef.cell({ row: { original: row } })
                                            : (row[column.columnDef.accessorKey] ?? '-')
                                    }}
                                </template>
                            </TableCell>
                        </TableRow>
                    </template>
                    <template v-else>
                        <TableRow>
                            <TableCell :colSpan="columns.length" class="h-24 text-center"> Sem resultados. </TableCell>
                        </TableRow>
                    </template>
                </TableBody>
            </Table>
        </div>

        <div class="mt-4 flex justify-end">
            <div v-if="paginate">
                <Pagination :total="data?.total" :sibling-count="1" :page="data?.from" :show-edges="true">
                    <PaginationList class="flex items-center gap-1">
                        <PaginationFirst @click="router.get(data.first_page_url ?? '')" />
                        <PaginationPrev @click="router.get(data.prev_page_url ?? '')" />

                        <template v-for="(item, index) in data.links?.slice(1, -1) || []">
                            <PaginationListItem v-if="item.url" :key="index" :value="item.value" as-child>
                                <Button
                                    @click="router.visit(`${data.path}?page=${item.label}`)"
                                    class="h-10 w-10 p-0"
                                    :variant="item.active ? 'default' : 'outline'"
                                >
                                    {{ item.label }}
                                </Button>
                            </PaginationListItem>
                            <PaginationEllipsis v-else :key="item.type" :index="index" />
                        </template>

                        <PaginationNext :disabled="!data.next_page_url" @click="router.get(data.next_page_url ?? '')" />
                        <PaginationLast :disabled="data.current_page == data.last_page" @click="router.get(data.last_page_url ?? '')" />
                    </PaginationList>
                </Pagination>
            </div>

            <div v-else class="flex items-center justify-end space-x-2">
                <Button variant="outline" size="sm" :disabled="!data.prev_page_url" @click="router.get(data.prev_page_url ?? '')"> Anterior </Button>
                <Button variant="outline" size="sm" :disabled="!data.next_page_url" @click="router.get(data.next_page_url ?? '')"> Próximo </Button>
            </div>
        </div>
    </div>
</template>
