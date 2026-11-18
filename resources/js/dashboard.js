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
    const selectedDateLabel = document.querySelector('#calendar-selected-date');
    const selectedEventsList = document.querySelector('#calendar-day-events-list');
    const eventsUrl = grid.dataset.calendarEventsUrl;
    let events = JSON.parse(grid.dataset.calendarEvents);
    let eventMap = new Map();
    const now = new Date();
    const todayKey = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    const initialMonth = grid.dataset.calendarMonth?.split('-').map(Number);
    let displayedMonth = initialMonth
        ? new Date(initialMonth[0], initialMonth[1] - 1, 1)
        : new Date(now.getFullYear(), now.getMonth(), 1);
    let selectedDate = todayKey;
    let activeRequest;
    const monthFormatter = new Intl.DateTimeFormat('id-ID', { month: 'long', year: 'numeric' });
    const dateFormatter = new Intl.DateTimeFormat('id-ID', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });

    const setEvents = (nextEvents) => {
        events = nextEvents;
        eventMap = new Map();
        events.forEach((event) => {
            const dateEvents = eventMap.get(event.date) || [];
            dateEvents.push(event);
            eventMap.set(event.date, dateEvents);
        });
    };

    const renderSelectedDate = () => {
        const selected = new Date(`${selectedDate}T00:00:00`);
        selectedDateLabel.textContent = dateFormatter.format(selected);
        selectedEventsList.replaceChildren();

        const dateEvents = eventMap.get(selectedDate) || [];

        if (dateEvents.length === 0) {
            const empty = document.createElement('p');
            empty.className = 'empty-note';
            empty.textContent = 'Tidak ada jadwal pada tanggal ini.';
            selectedEventsList.append(empty);
            return;
        }

        dateEvents.forEach((event) => {
            const item = document.createElement('div');
            item.className = 'selected-day-item';
            const marker = document.createElement('span');
            marker.className = `legend-dot ${event.type === 'pupuk' ? 'is-fertilizer' : 'is-harvest'}`;
            marker.setAttribute('aria-hidden', 'true');
            const copy = document.createElement('span');
            copy.className = 'upcoming-copy';
            const title = document.createElement('strong');
            title.textContent = event.title;
            const estate = document.createElement('span');
            estate.textContent = event.kebun || 'Semua kebun';
            copy.append(title, estate);
            item.append(marker, copy);
            selectedEventsList.append(item);
        });
    };

    const loadEventsForMonth = async () => {
        if (!eventsUrl) {
            return;
        }

        activeRequest?.abort();
        activeRequest = new AbortController();
        const monthKey = `${displayedMonth.getFullYear()}-${String(displayedMonth.getMonth() + 1).padStart(2, '0')}`;
        grid.setAttribute('aria-busy', 'true');

        try {
            const url = new URL(eventsUrl, window.location.origin);
            url.searchParams.set('bulan', monthKey);
            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                signal: activeRequest.signal,
            });

            if (!response.ok) {
                throw new Error('Jadwal kalender tidak dapat dimuat.');
            }

            const result = await response.json();
            setEvents(result.events);
            renderCalendar();
            renderSelectedDate();
        } catch (error) {
            if (error.name !== 'AbortError') {
                selectedEventsList.replaceChildren();
                const message = document.createElement('p');
                message.className = 'empty-note';
                message.textContent = error.message || 'Jadwal kalender tidak dapat dimuat.';
                selectedEventsList.append(message);
            }
        } finally {
            grid.removeAttribute('aria-busy');
        }
    };

    setEvents(events);

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
            const dateEvents = eventMap.get(dateKey) || [];
            const hasHarvest = dateEvents.some((event) => event.type === 'panen');
            const hasFertilizer = dateEvents.some((event) => event.type === 'pupuk');
            const dayCell = document.createElement('button');
            dayCell.type = 'button';
            dayCell.className = 'calendar-day';

            if (muted) dayCell.classList.add('is-muted');
            if (dateKey === todayKey) dayCell.classList.add('is-today');
            if (hasHarvest) dayCell.classList.add('has-harvest');
            if (hasFertilizer) dayCell.classList.add('has-fertilizer');
            if (dateKey === selectedDate) dayCell.classList.add('is-selected');

            const number = document.createElement('span');
            number.className = 'calendar-day-number';
            number.textContent = String(day);
            const markers = document.createElement('span');
            markers.className = 'calendar-event-markers';
            if (hasHarvest) {
                const marker = document.createElement('i');
                marker.className = 'is-harvest';
                markers.append(marker);
            }
            if (hasFertilizer) {
                const marker = document.createElement('i');
                marker.className = 'is-fertilizer';
                markers.append(marker);
            }
            dayCell.append(number, markers);
            dayCell.setAttribute('aria-label', `${dateKey}${dateEvents.length ? `, ${dateEvents.length} jadwal` : ''}`);
            dayCell.setAttribute('aria-pressed', String(dateKey === selectedDate));
            dayCell.addEventListener('click', () => {
                selectedDate = dateKey;
                if (muted) {
                    displayedMonth = new Date(cellYear, cellMonth, 1);
                    renderCalendar();
                    loadEventsForMonth();
                } else {
                    renderCalendar();
                    renderSelectedDate();
                }
            });
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
            loadEventsForMonth();
        });
    });

    renderCalendar();
    renderSelectedDate();
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
