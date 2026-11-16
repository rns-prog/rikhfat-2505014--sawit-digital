<?php

namespace App\Services;

use App\Models\Biaya;
use App\Models\Kebun;
use App\Models\Panen;
use App\Models\User;
use Illuminate\Support\Carbon;

class LaporanService
{
    private const BULAN = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember',
    ];

    /**
     * @return array<int, int>
     */
    public function availableYears(User $user): array
    {
        $firstHarvest = $user->panens()->orderBy('tanggal')->value('tanggal');
        $firstExpense = $user->biayas()->orderBy('tanggal')->value('tanggal');
        $years = collect([$firstHarvest, $firstExpense])
            ->filter()
            ->map(fn (string $date): int => Carbon::parse($date)->year);
        $firstYear = min(now()->year, $years->min() ?? now()->year);

        return range(now()->year, $firstYear);
    }

    /**
     * @return array<string, mixed>
     */
    public function calculate(User $user, int $year, string $mode, ?int $kebunId = null): array
    {
        $panenQuery = $user->panens()
            ->with('kebun:id,nama,lokasi,luas_ha')
            ->whereDate('tanggal', '>=', "{$year}-01-01")
            ->whereDate('tanggal', '<=', "{$year}-12-31");
        $biayaQuery = $user->biayas()
            ->with('kebun:id,nama,lokasi,luas_ha')
            ->whereDate('tanggal', '>=', "{$year}-01-01")
            ->whereDate('tanggal', '<=', "{$year}-12-31");

        if ($kebunId !== null) {
            $panenQuery->where('kebun_id', $kebunId);
            $biayaQuery->where('kebun_id', $kebunId);
        }

        $panens = $panenQuery->get([
            'id',
            'kebun_id',
            'tanggal',
            'berat_kg',
            'harga_per_kg',
        ]);
        $biayas = $biayaQuery->get(['id', 'kebun_id', 'tanggal', 'kategori', 'nominal']);
        $monthly = collect(range(1, 12))->mapWithKeys(fn (int $month): array => [
            $month => [
                'periode' => self::BULAN[$month - 1],
                'panen_kg' => 0.0,
                'pendapatan' => 0.0,
                'biaya' => 0.0,
                'laba' => 0.0,
            ],
        ]);
        $estateTotals = $user->kebuns()
            ->when($kebunId !== null, fn ($query) => $query->whereKey($kebunId))
            ->orderBy('nama')
            ->get(['id', 'nama', 'luas_ha'])
            ->mapWithKeys(fn (Kebun $kebun): array => [
                $kebun->id => [
                    'nama' => $kebun->nama,
                    'luas_ha' => $kebun->luas_ha,
                    'panen_kg' => 0.0,
                    'pendapatan' => 0.0,
                    'biaya' => 0.0,
                    'laba' => 0.0,
                    'produktivitas' => null,
                ],
            ]);

        foreach ($panens as $panen) {
            $month = $panen->tanggal->month;
            $weight = (float) $panen->berat_kg;
            $income = $weight * (float) $panen->harga_per_kg;
            $monthTotals = $monthly->get($month);
            $monthTotals['panen_kg'] += $weight;
            $monthTotals['pendapatan'] += $income;
            $monthly->put($month, $monthTotals);

            if ($estateTotals->has($panen->kebun_id)) {
                $estate = $estateTotals->get($panen->kebun_id);
                $estate['panen_kg'] += $weight;
                $estate['pendapatan'] += $income;
                $estateTotals->put($panen->kebun_id, $estate);
            }
        }

        foreach ($biayas as $biaya) {
            $monthTotals = $monthly->get($biaya->tanggal->month);
            $monthTotals['biaya'] += (float) $biaya->nominal;
            $monthly->put($biaya->tanggal->month, $monthTotals);

            if ($estateTotals->has($biaya->kebun_id)) {
                $estate = $estateTotals->get($biaya->kebun_id);
                $estate['biaya'] += (float) $biaya->nominal;
                $estateTotals->put($biaya->kebun_id, $estate);
            }
        }

        foreach ($monthly as $month => $totals) {
            $totals['laba'] = $totals['pendapatan'] - $totals['biaya'];
            $monthly->put($month, $totals);
        }

        foreach ($estateTotals as $id => $totals) {
            $totals['laba'] = $totals['pendapatan'] - $totals['biaya'];
            $totals['produktivitas'] = $totals['luas_ha'] && (float) $totals['luas_ha'] > 0
                ? $totals['panen_kg'] / (float) $totals['luas_ha']
                : null;
            $estateTotals->put($id, $totals);
        }

        $estateRows = $estateTotals->values()
            ->sort(fn (array $left, array $right): int => ($right['laba'] <=> $left['laba'])
                ?: strcasecmp($left['nama'], $right['nama']))
            ->values();
        $categoryTotals = collect(Biaya::KATEGORI)
            ->mapWithKeys(fn (string $category): array => [
                $category => (float) $biayas
                    ->where('kategori', $category)
                    ->sum(fn (Biaya $biaya): float => (float) $biaya->nominal),
            ]);
        $totalPanen = (float) $panens->sum(fn (Panen $panen): float => (float) $panen->berat_kg);
        $totalPendapatan = (float) $panens->sum(fn (Panen $panen): float => (float) $panen->berat_kg * (float) $panen->harga_per_kg);
        $totalBiaya = (float) $biayas->sum(fn (Biaya $biaya): float => (float) $biaya->nominal);
        $totalLaba = $totalPendapatan - $totalBiaya;
        $monthlyRows = $monthly->values();
        $periodRows = $mode === 'tahun'
            ? collect([[
                'periode' => (string) $year,
                'panen_kg' => $totalPanen,
                'pendapatan' => $totalPendapatan,
                'biaya' => $totalBiaya,
                'laba' => $totalLaba,
            ]])
            : $monthlyRows;

        return [
            'year' => $year,
            'mode' => $mode,
            'selectedKebun' => $kebunId === null
                ? null
                : $user->kebuns()->find($kebunId),
            'kebuns' => $user->kebuns()->orderBy('nama')->get(['id', 'nama']),
            'summary' => [
                'panen_kg' => $totalPanen,
                'pendapatan' => $totalPendapatan,
                'biaya' => $totalBiaya,
                'laba' => $totalLaba,
            ],
            'periodRows' => $periodRows,
            'monthlyRows' => $monthlyRows,
            'estateRows' => $estateRows,
            'bestEstateName' => $panens->isNotEmpty() || $biayas->isNotEmpty()
                ? $estateRows->first()['nama'] ?? null
                : null,
            'categoryRows' => $categoryTotals->map(fn (float $total, string $category): array => [
                'kategori' => $category,
                'total' => $total,
                'persentase' => $totalBiaya > 0 ? ($total / $totalBiaya) * 100 : 0,
            ])->values(),
            'charts' => [
                'monthly' => [
                    'labels' => $monthly->pluck('periode')->all(),
                    'datasets' => [
                        ['label' => 'Pendapatan', 'values' => $monthly->pluck('pendapatan')->all()],
                        ['label' => 'Biaya', 'values' => $monthly->pluck('biaya')->all()],
                        ['label' => 'Laba bersih', 'values' => $monthly->pluck('laba')->all()],
                    ],
                    'hasData' => $panens->isNotEmpty() || $biayas->isNotEmpty(),
                ],
                'categories' => [
                    'labels' => $categoryTotals->keys()->all(),
                    'values' => $categoryTotals->values()->all(),
                    'hasData' => $totalBiaya > 0,
                ],
            ],
            'hasData' => $panens->isNotEmpty() || $biayas->isNotEmpty(),
        ];
    }
}
