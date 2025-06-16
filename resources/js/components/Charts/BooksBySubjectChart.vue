<template>
    <div class="h-96">
        <div v-if="!props.booksBySubject?.length" class="flex h-full items-center justify-center text-gray-500">
            Nenhum dado disponível para exibir
        </div>
        <Bar v-else-if="chartData" :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup lang="ts">
import { BarElement, CategoryScale, Chart as ChartJS, Legend, LinearScale, Title, Tooltip } from 'chart.js';
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

interface Props {
    booksBySubject: Array<{
        CodAs: number;
        Descricao: string;
        books_count: number;
    }>;
}

const props = defineProps<Props>();

const chartData = computed(() => {
    const colors = [
        'rgba(30, 64, 175, 0.5)',
        'rgba(37, 99, 235, 0.5)',
        'rgba(59, 130, 246, 0.5)',
        'rgba(96, 165, 250, 0.5)',
        'rgba(16, 185, 129, 0.5)',
        'rgba(34, 197, 94, 0.5)',
        'rgba(74, 222, 128, 0.5)',
        'rgba(5, 150, 105, 0.5)',
        'rgba(6, 182, 212, 0.5)',
    ];

    const borderColors = colors.map((color) => color.replace('0.5', '1'));

    // Ordena os assuntos por quantidade de livros em ordem decrescente
    const sortedSubjects = [...props.booksBySubject].sort((a, b) => b.books_count - a.books_count);

    return {
        labels: sortedSubjects.map((subject) => subject.Descricao),
        datasets: [
            {
                label: 'Quantidade de Livros',
                data: sortedSubjects.map((subject) => subject.books_count),
                backgroundColor: colors,
                borderColor: borderColors,
                borderWidth: 1,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'top' as const,
        },
        title: {
            display: true,
            text: 'Distribuição de Livros por Assunto',
            font: {
                size: 16,
            },
        },
    },
    scales: {
        y: {
            beginAtZero: true,
            ticks: {
                stepSize: 1,
            },
        },
    },
};
</script>
