<?php

namespace App\Support;

use Carbon\Carbon;

class DataContoh
{
    /**
     * @return array<string, mixed>
     */
    public static function dashboard(): array
    {
        $now = now();
        $months = collect(range(5, 0))
            ->map(fn (int $offset): Carbon => $now->copy()->subMonths($offset)->startOfMonth());
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthLabels = $months->map(fn (Carbon $month): string => $monthNames[$month->month - 1])->all();
        $upcomingDates = [
            $now->copy()->addDays(3),
            $now->copy()->addDays(7),
            $now->copy()->addDays(12),
        ];

        $latestHarvests = [
            ['days_ago' => 1, 'kebun' => 'Blok A', 'berat' => 1280, 'harga' => 2450, 'pembeli' => 'UD Sumber Rezeki'],
            ['days_ago' => 3, 'kebun' => 'Blok B', 'berat' => 960, 'harga' => 2450, 'pembeli' => 'CV Tani Makmur'],
            ['days_ago' => 5, 'kebun' => 'Blok A', 'berat' => 1420, 'harga' => 2500, 'pembeli' => 'UD Sumber Rezeki'],
            ['days_ago' => 8, 'kebun' => 'Blok C', 'berat' => 875, 'harga' => 2450, 'pembeli' => 'Koperasi Sawit Jaya'],
            ['days_ago' => 10, 'kebun' => 'Blok B', 'berat' => 1110, 'harga' => 2500, 'pembeli' => 'CV Tani Makmur'],
        ];

        foreach ($latestHarvests as &$harvest) {
            $harvest['tanggal'] = $now->copy()->subDays($harvest['days_ago'])->format('d/m/Y');
            $harvest['total'] = $harvest['berat'] * $harvest['harga'];
            unset($harvest['days_ago']);
        }
        unset($harvest);

        $upcoming = [
            ['date' => $upcomingDates[0], 'title' => 'Panen berikutnya', 'location' => 'Blok A', 'type' => 'panen'],
            ['date' => $upcomingDates[1], 'title' => 'Pemupukan', 'location' => 'Blok B', 'type' => 'pupuk'],
            ['date' => $upcomingDates[2], 'title' => 'Panen berikutnya', 'location' => 'Blok C', 'type' => 'panen'],
        ];

        return [
            'revenue' => [
                'total' => 48_750_000,
                'change' => 12.8,
                'months' => $monthLabels,
                'values' => [31_200_000, 35_800_000, 33_400_000, 39_650_000, 43_210_992, 48_750_000],
            ],
            'stats' => [
                [
                    'key' => 'panen',
                    'title' => 'Panen bulan ini',
                    'value' => '12.840',
                    'unit' => 'kg',
                    'change' => 8.4,
                    'direction' => 'up',
                    'values' => [7200, 8100, 7800, 9400, 11844, 12840],
                    'icon' => 'harvest',
                ],
                [
                    'key' => 'pengeluaran',
                    'title' => 'Pengeluaran bulan ini',
                    'value' => 'Rp 11,6 jt',
                    'unit' => '',
                    'change' => 3.2,
                    'direction' => 'down',
                    'values' => [9200000, 10400000, 9800000, 12100000, 11983471, 11600000],
                    'icon' => 'expense',
                ],
                [
                    'key' => 'laba',
                    'title' => 'Laba bulan ini',
                    'value' => 'Rp 37,1 jt',
                    'unit' => '',
                    'change' => 14.6,
                    'direction' => 'up',
                    'values' => [22000000, 25400000, 23600000, 27550000, 32417103, 37150000],
                    'icon' => 'profit',
                ],
            ],
            'charts' => [
                'harvest' => [
                    'title' => 'Hasil panen per bulan',
                    'labels' => $monthLabels,
                    'values' => [9800, 11200, 10500, 11900, 12350, 12840],
                    'type' => 'bar',
                    'unit' => 'kg',
                ],
                'fertilizer' => [
                    'title' => 'Pengeluaran pupuk per bulan',
                    'labels' => $monthLabels,
                    'values' => [2800000, 3100000, 2950000, 3350000, 3200000, 3600000],
                    'type' => 'line',
                    'unit' => 'Rp',
                ],
                'wages' => [
                    'title' => 'Pengeluaran upah panen',
                    'labels' => $monthLabels,
                    'values' => [4200000, 4700000, 4500000, 5100000, 5300000, 5650000],
                    'type' => 'bar',
                    'unit' => 'Rp',
                ],
                'expenses' => [
                    'title' => 'Komposisi biaya per kategori',
                    'labels' => ['Upah panen', 'Pupuk', 'Perawatan', 'Transportasi', 'Lainnya'],
                    'values' => [5650000, 3600000, 1250000, 780000, 320000],
                    'type' => 'doughnut',
                    'unit' => 'Rp',
                ],
                'productivity' => [
                    'title' => 'Produktivitas per kebun / blok',
                    'labels' => ['Blok A', 'Blok B', 'Blok C', 'Blok D'],
                    'values' => [4850, 3920, 2760, 1310],
                    'type' => 'horizontalBar',
                    'unit' => 'kg',
                ],
            ],
            'latestHarvests' => $latestHarvests,
            'nextSchedule' => [
                'daysUntil' => 3,
                'title' => 'Panen berikutnya',
                'location' => 'Blok A',
            ],
            'calendarEvents' => [
                ['date' => $upcomingDates[0]->toDateString(), 'type' => 'panen', 'title' => 'Panen · Blok A'],
                ['date' => $upcomingDates[1]->toDateString(), 'type' => 'pupuk', 'title' => 'Pemupukan · Blok B'],
                ['date' => $upcomingDates[2]->toDateString(), 'type' => 'panen', 'title' => 'Panen · Blok C'],
            ],
            'upcoming' => array_map(fn (array $item): array => [
                'date' => $item['date']->format('d/m'),
                'title' => $item['title'],
                'location' => $item['location'],
                'type' => $item['type'],
            ], $upcoming),
        ];
    }
}
