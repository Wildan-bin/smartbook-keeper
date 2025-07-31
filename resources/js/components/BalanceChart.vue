<!-- resources/js/components/BalanceChart.vue -->
<template>
    <div>
        <Line :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Line } from 'vue-chartjs';

const props = defineProps({
    labels: {
        type: Array,
        required: true,
    },
    values: {
        type: Array,
        required: true,
    },
    tooltips: {
        type: Array,
        required: true,
    },
});

const chartData = computed(() => ({
    labels: props.labels,
    datasets: [
        {
            data: props.values,
            borderColor: '#007abb',
            backgroundColor: 'rgba(15, 217, 45, 0.1)',
            borderWidth: 6,
            tension: 0.4,
            fill: true,
            pointRadius: 2,
            label: '',
        },
    ],
}));

const chartOptions = computed(() => ({
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        tooltip: {
            callbacks: {
                label: (context) => props.tooltips[context.dataIndex],
            },
        },
        legend: {
            display: false,
        },
    },
    scales: {
        x: {
            grid: {
                display: false,
            },
            border: {
                display: false,
            },
            ticks: {
                padding: 20,
            },
        },
        y: {
            beginAtZero: true,
            grid: {
                display: true
            },
            border: {
                display: false,
            },
            ticks: {
                stepSize: 20000000, // Tampilkan 20, 40, 60, ...
                beginAtZero: true,
                padding: 20,
            },
        },
    },
}));
</script>
