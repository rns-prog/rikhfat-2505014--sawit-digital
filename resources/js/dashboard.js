import Chart from 'chart.js/auto';

const chartColors = ['#1F4FB5', '#1FA45B', '#F59E0B', '#7C5CE7', '#E5484D'];

function formatValue(value, unit) {
    const formatted = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 }).format(value);

    return unit === 'Rp' ? `Rp ${formatted}` : `${formatted} ${unit}`.trim();
}

function compactValue(value, unit) {
    const amount = new Intl.NumberFormat('id-ID', {
        notation: 'compact',
        maximumFractionDigits: 1,
    }).format(value);

    return unit === 'Rp' ? `Rp ${amount}` : amount;
}

function initCharts() {
    document.querySelectorAll('[data-chart]').forEach((canvas) => {
        const type = canvas.dataset.chartType;
        const labels = JSON.parse(canvas.dataset.chartLabels);
        const values = JSON.parse(canvas.dataset.chartValues);
        const chartDatasets = canvas.dataset.chartDatasets
            ? JSON.parse(canvas.dataset.chartDatasets)
            : null;
        const unit = canvas.dataset.chartUnit || '';
        const isSparkline = canvas.dataset.chartSparkline === 'true';
        const isDoughnut = type === 'doughnut';
        const isHorizontal = type === 'horizontalBar';
        const isArea = type === 'area';
        const isLine = type === 'line' || isArea || type === 'sparkline';
        const context = canvas.getContext('2d');
        const areaGradient = context.createLinearGradient(0, 0, 0, canvas.clientHeight || 150);
        areaGradient.addColorStop(0, 'rgba(98, 177, 255, 0.48)');
        areaGradient.addColorStop(1, 'rgba(98, 177, 255, 0.015)');

        const dataset = {
            label: unit === 'Rp' ? 'Nominal' : 'Berat',
            data: values,
            borderWidth: isSparkline ? 2 : 2.5,
            borderColor: isDoughnut ? '#FFFFFF' : (isArea ? '#FFFFFF' : '#1F4FB5'),
            backgroundColor: isDoughnut
                ? chartColors
                : (isArea ? areaGradient : (isLine ? '#1F4FB5' : '#1F4FB5')),
            borderRadius: isDoughnut || isLine ? 0 : 5,
            maxBarThickness: isHorizontal ? 20 : 34,
            fill: isArea,
            tension: 0.38,
            pointRadius: isSparkline ? 0 : (isLine ? 3 : 0),
            pointHoverRadius: isSparkline ? 0 : 5,
            pointBackgroundColor: isArea ? '#FFFFFF' : '#1F4FB5',
            pointBorderColor: isArea ? '#FFFFFF' : '#1F4FB5',
        };

        const options = {
            responsive: true,
            maintainAspectRatio: false,
            animation: { duration: 450 },
            interaction: { intersect: false, mode: 'index' },
            plugins: {
                legend: {
                    display: isDoughnut || (chartDatasets !== null && chartDatasets.length > 1),
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'circle',
                        boxWidth: 7,
                        boxHeight: 7,
                        padding: 14,
                        color: '#6B7690',
                        font: { family: 'Figtree', size: 11 },
                    },
                },
                tooltip: {
                    enabled: !isSparkline,
                    callbacks: {
                        label: (context) => ` ${formatValue(context.parsed.y ?? context.parsed.x ?? context.parsed, unit)}`,
                    },
                },
            },
            scales: isSparkline ? {
                x: { display: false },
                y: { display: false },
            } : isDoughnut ? {} : isHorizontal ? {
                x: {
                    beginAtZero: true,
                    grid: { color: '#EEF1F7' },
                    ticks: {
                        color: '#8791A7',
                        maxTicksLimit: 5,
                        font: { family: 'Figtree', size: 10 },
                        callback: (value) => compactValue(value, unit),
                    },
                    border: { display: false },
                },
                y: {
                    grid: { display: false },
                    ticks: {
                        color: '#8791A7',
                        font: { family: 'Figtree', size: 11 },
                    },
                    border: { display: false },
                },
            } : {
                x: {
                    display: !isSparkline,
                    grid: { display: false },
                    ticks: {
                        color: '#8791A7',
                        font: { family: 'Figtree', size: 11 },
                    },
                    border: { display: false },
                },
                y: {
                    display: !isSparkline,
                    beginAtZero: true,
                    grid: { color: isArea ? 'rgba(255,255,255,0.12)' : '#EEF1F7' },
                    ticks: {
                        color: isArea ? 'rgba(255,255,255,0.72)' : '#8791A7',
                        maxTicksLimit: 5,
                        font: { family: 'Figtree', size: 10 },
                        callback: (value) => compactValue(value, unit),
                    },
                    border: { display: false },
                },
            },
        };

        if (isSparkline) {
            options.layout = { padding: 1 };
            options.elements = { line: { borderWidth: 2 } };
        }

        const datasets = chartDatasets
            ? chartDatasets.map((item, index) => ({
                ...dataset,
                label: item.label,
                data: item.values,
                backgroundColor: chartColors[index % chartColors.length],
                borderColor: chartColors[index % chartColors.length],
            }))
            : [dataset];

        new Chart(canvas, {
            type: isArea || isSparkline ? 'line' : (isHorizontal ? 'bar' : type),
            data: {
                labels,
                datasets,
            },
            options: {
                ...options,
                ...(isHorizontal ? { indexAxis: 'y' } : {}),
            },
        });
    });
}

function initCalendar() {
    const grid = document.querySelector('#calendar-grid');

    if (!grid) {
        return;
    }

    const monthLabel = document.querySelector('#calendar-month');
    const events = JSON.parse(grid.dataset.calendarEvents);
    const eventMap = new Map(events.map((event) => [event.date, event.type]));
    const now = new Date();
    const todayKey = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    let displayedMonth = new Date(now.getFullYear(), now.getMonth(), 1);
    const monthFormatter = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' });

    const renderCalendar = () => {
        const year = displayedMonth.getFullYear();
        const month = displayedMonth.getMonth();
        const firstWeekday = (new Date(year, month, 1).getDay() + 6) % 7;
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        const previousMonthDays = new Date(year, month, 0).getDate();

        monthLabel.textContent = monthFormatter.format(displayedMonth);
        grid.replaceChildren();

        for (let cell = 0; cell < 42; cell += 1) {
            let day = cell - firstWeekday + 1;
            let cellMonth = month;
            let cellYear = year;
            let muted = false;

            if (day < 1) {
                day = previousMonthDays + day;
                cellMonth -= 1;
                muted = true;
            } else if (day > daysInMonth) {
                day -= daysInMonth;
                cellMonth += 1;
                muted = true;
            }

            if (cellMonth < 0) {
                cellMonth = 11;
                cellYear -= 1;
            } else if (cellMonth > 11) {
                cellMonth = 0;
                cellYear += 1;
            }

            const dateKey = `${cellYear}-${String(cellMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const eventType = eventMap.get(dateKey);
            const dayCell = document.createElement('span');
            dayCell.className = 'calendar-day';

            if (muted) dayCell.classList.add('is-muted');
            if (dateKey === todayKey) dayCell.classList.add('is-today');
            if (eventType) dayCell.classList.add('has-event');
            if (eventType === 'pupuk') dayCell.classList.add('has-fertilizer');

            const number = document.createElement('span');
            number.className = 'calendar-day-number';
            number.textContent = String(day);
            dayCell.append(number);
            dayCell.setAttribute('aria-label', dateKey);
            grid.append(dayCell);
        }
    };

    document.querySelectorAll('[data-calendar-step]').forEach((button) => {
        button.addEventListener('click', () => {
            displayedMonth = new Date(
                displayedMonth.getFullYear(),
                displayedMonth.getMonth() + Number(button.dataset.calendarStep),
                1,
            );
            renderCalendar();
        });
    });

    renderCalendar();
}

function initSidebar() {
    const sidebar = document.querySelector('#app-sidebar');
    const toggle = document.querySelector('#sidebar-toggle');
    const overlay = document.querySelector('#sidebar-overlay');

    if (!sidebar || !toggle || !overlay) {
        return;
    }

    const closeSidebar = () => {
        sidebar.classList.remove('is-open');
        overlay.classList.remove('is-visible');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.setAttribute('aria-label', 'Buka menu');
    };

    toggle.addEventListener('click', () => {
        const isOpen = sidebar.classList.toggle('is-open');
        overlay.classList.toggle('is-visible', isOpen);
        toggle.setAttribute('aria-expanded', String(isOpen));
        toggle.setAttribute('aria-label', isOpen ? 'Tutup menu' : 'Buka menu');
    });

    overlay.addEventListener('click', closeSidebar);
    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeSidebar));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') closeSidebar();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initCharts();
    initCalendar();
    initSidebar();
});
