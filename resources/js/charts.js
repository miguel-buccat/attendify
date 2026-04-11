import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const themeColors = {
    primary: 'oklch(0.65 0.24 354)',
    success: 'oklch(0.72 0.19 154)',
    warning: 'oklch(0.82 0.17 85)',
    error: 'oklch(0.65 0.24 27)',
    info: 'oklch(0.72 0.15 230)',
    ghost: 'oklch(0.75 0.02 260)',
};

const palette = [themeColors.success, themeColors.warning, themeColors.error, themeColors.info, themeColors.primary, themeColors.ghost];

export function createPieChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;
    return new Chart(ctx, {
        type: 'pie',
        data: {
            labels: data.labels,
            datasets: [{
                data: data.values,
                backgroundColor: data.colors || palette.slice(0, data.labels.length),
                borderWidth: 0,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } },
            },
        },
    });
}

export function createBarChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;
    return new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: data.label || 'Value',
                data: data.values,
                backgroundColor: data.colors || themeColors.primary,
                borderRadius: 6,
                borderSkipped: false,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } },
            },
            plugins: {
                legend: { display: !!data.label },
            },
        },
    });
}

export function createLineChart(canvasId, data) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return null;
    return new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: data.label || 'Value',
                data: data.values,
                borderColor: data.color || themeColors.primary,
                backgroundColor: (data.color || themeColors.primary).replace(')', ', 0.1)').replace('oklch(', 'oklch('),
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointHoverRadius: 6,
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { callback: v => v + '%' } },
                x: { grid: { display: false } },
            },
            plugins: {
                legend: { display: false },
            },
        },
    });
}

// Auto-init: look for elements with [data-chart] attribute
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-chart]').forEach(el => {
        const type = el.dataset.chart;
        const data = JSON.parse(el.dataset.chartData);
        const canvasId = el.querySelector('canvas')?.id;
        if (!canvasId) return;

        if (type === 'pie') createPieChart(canvasId, data);
        else if (type === 'bar') createBarChart(canvasId, data);
        else if (type === 'line') createLineChart(canvasId, data);
    });
});
