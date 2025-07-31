<!-- resources/js/components/TurnoverChart.vue -->
<template>
    <div>
        <Bar :data="chartData" :options="chartOptions" />
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Bar } from 'vue-chartjs';

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
            backgroundColor: '#007abb',
            borderRadius: 6,
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
            border: {
                display: false,
            },
            ticks: {
                padding: 20,
            },
        },
    },
}));
</script>
