<?php

namespace App\Http\Controllers;

use App\Models\Biaya;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    private const MONTH_NAMES = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $currentMonth = now()->startOfMonth();
        $previousMonth = $currentMonth->copy()->subMonth();
        $months = collect(range(5, 0))
            ->map(fn (int $offset): Carbon => $currentMonth->copy()->subMonths($offset));
        $monthKeys = $months->map(fn (Carbon $month): string => $month->format('Y-m'));
        $monthLabels = $months->map(fn (Carbon $month): string => self::MONTH_NAMES[$month->month - 1])->all();
        $rangeStart = $months->first()->toDateString();
        $rangeEnd = $currentMonth->copy()->endOfMonth()->toDateString();

        $panensSixMonths = $user->panens()
            ->with('kebun:id,nama,lokasi,luas_ha')
            ->whereDate('tanggal', '>=', $rangeStart)
            ->whereDate('tanggal', '<=', $rangeEnd)
            ->get(['id', 'kebun_id', 'tanggal', 'jumlah_tandan', 'berat_kg', 'harga_per_kg', 'pembeli']);

        $biayasSixMonths = $user->biayas()
            ->whereDate('tanggal', '>=', $rangeStart)
            ->whereDate('tanggal', '<=', $rangeEnd)
            ->get(['id', 'tanggal', 'kategori', 'nominal']);

        $harvestByMonth = $this->sumByMonth($panensSixMonths, fn ($panen): float => (float) $panen->berat_kg);
        $revenueByMonth = $this->sumByMonth($panensSixMonths, fn ($panen): float => (float) $panen->berat_kg * (float) $panen->harga_per_kg);
        $expenseByMonth = $this->sumByMonth($biayasSixMonths, fn ($biaya): float => (float) $biaya->nominal);
        $fertilizerByMonth = $this->sumByMonth(
            $biayasSixMonths->where('kategori', 'Pupuk'),
            fn ($biaya): float => (float) $biaya->nominal,
        );
        $wagesByMonth = $this->sumByMonth(
            $biayasSixMonths->where('kategori', 'Upah panen'),
            fn ($biaya): float => (float) $biaya->nominal,
        );

        $fillMissingMonths = fn (Collection $totals): array => $monthKeys
            ->map(fn (string $key): float => (float) ($totals[$key] ?? 0))
            ->all();

        $harvestValues = $fillMissingMonths($harvestByMonth);
        $revenueValues = $fillMissingMonths($revenueByMonth);
        $expenseValues = $fillMissingMonths($expenseByMonth);
        $fertilizerValues = $fillMissingMonths($fertilizerByMonth);
        $wagesValues = $fillMissingMonths($wagesByMonth);
        $profitValues = array_map(
            fn (int $index): float => $revenueValues[$index] - $expenseValues[$index],
            array_keys($monthKeys->all()),
        );

        $currentMonthKey = $currentMonth->format('Y-m');
        $previousMonthKey = $previousMonth->format('Y-m');
        $currentRevenue = (float) ($revenueByMonth[$currentMonthKey] ?? 0);
        $previousRevenue = (float) ($revenueByMonth[$previousMonthKey] ?? 0);
        $currentHarvest = (float) ($harvestByMonth[$currentMonthKey] ?? 0);
        $previousHarvest = (float) ($harvestByMonth[$previousMonthKey] ?? 0);
        $currentExpense = (float) ($expenseByMonth[$currentMonthKey] ?? 0);
        $previousExpense = (float) ($expenseByMonth[$previousMonthKey] ?? 0);
        $currentProfit = $currentRevenue - $currentExpense;
        $previousProfit = $previousRevenue - $previousExpense;

        $currentMonthPanens = $panensSixMonths->filter(
            fn ($panen): bool => $panen->tanggal->format('Y-m') === $currentMonthKey,
        );
        $currentMonthBiayas = $biayasSixMonths->filter(
            fn ($biaya): bool => $biaya->tanggal->format('Y-m') === $currentMonthKey,
        );
        $previousMonthPanens = $panensSixMonths->filter(
            fn ($panen): bool => $panen->tanggal->format('Y-m') === $previousMonthKey,
        );
        $previousMonthBiayas = $biayasSixMonths->filter(
            fn ($biaya): bool => $biaya->tanggal->format('Y-m') === $previousMonthKey,
        );

        $kebuns = $user->kebuns()->orderBy('nama')->get(['id', 'nama', 'lokasi', 'luas_ha']);
        $productivityByKebun = $currentMonthPanens->groupBy('kebun_id')
            ->map(fn (Collection $rows): float => (float) $rows->sum(fn ($row): float => (float) $row->berat_kg));
        $productivityValues = $kebuns
            ->map(fn ($kebun): float => (float) ($productivityByKebun[$kebun->id] ?? 0))
            ->all();

        $expensesByCategory = $currentMonthBiayas
            ->groupBy('kategori')
            ->map(fn (Collection $rows): float => (float) $rows->sum(fn ($row): float => (float) $row->nominal));
        $expenseCategories = Biaya::KATEGORI;
        $expenseCategoryValues = array_map(
            fn (string $category): float => (float) ($expensesByCategory[$category] ?? 0),
            $expenseCategories,
        );

        $latestHarvests = $user->panens()
            ->with('kebun:id,nama')
            ->latest('tanggal')
            ->latest('id')
            ->limit(5)
            ->get(['id', 'kebun_id', 'tanggal', 'berat_kg', 'harga_per_kg', 'pembeli']);

        $charts = [
            'harvest' => [
                'title' => 'Hasil panen per bulan',
                'labels' => $monthLabels,
                'values' => $harvestValues,
                'type' => 'bar',
                'unit' => 'kg',
                'hasData' => array_sum($harvestValues) > 0,
            ],
            'fertilizer' => [
                'title' => 'Pengeluaran pupuk per bulan',
                'labels' => $monthLabels,
                'values' => $fertilizerValues,
                'type' => 'line',
                'unit' => 'Rp',
                'hasData' => array_sum($fertilizerValues) > 0,
            ],
            'wages' => [
                'title' => 'Pengeluaran upah panen per bulan',
                'labels' => $monthLabels,
                'values' => $wagesValues,
                'type' => 'bar',
                'unit' => 'Rp',
                'hasData' => array_sum($wagesValues) > 0,
            ],
            'expenses' => [
                'title' => 'Komposisi biaya per kategori',
                'labels' => $expenseCategories,
                'values' => $expenseCategoryValues,
                'type' => 'doughnut',
                'unit' => 'Rp',
                'hasData' => array_sum($expenseCategoryValues) > 0,
            ],
            'productivity' => [
                'title' => 'Produktivitas per kebun',
                'labels' => $kebuns->pluck('nama')->all(),
                'values' => $productivityValues,
                'type' => 'horizontalBar',
                'unit' => 'kg',
                'hasData' => array_sum($productivityValues) > 0,
            ],
        ];

        return view('dashboard', [
            'kebuns' => $kebuns,
            'dashboard' => [
                'months' => $monthLabels,
                'revenue' => [
                    'total' => $currentRevenue,
                    'changePercent' => $this->changePercent(
                        $currentRevenue,
                        $previousRevenue,
                        $previousMonthPanens->isNotEmpty(),
                    ),
                    'values' => $revenueValues,
                    'hasData' => array_sum($revenueValues) > 0,
                ],
                'stats' => [
                    $this->stat('Panen bulan ini', $currentHarvest, 'kg', $previousHarvest, $previousMonthPanens->isNotEmpty(), $harvestValues),
                    $this->stat('Pengeluaran bulan ini', $currentExpense, 'Rp', $previousExpense, $previousMonthBiayas->isNotEmpty(), $expenseValues),
                    $this->stat(
                        'Laba bulan ini',
                        $currentProfit,
                        'Rp',
                        $previousProfit,
                        $previousMonthPanens->isNotEmpty() || $previousMonthBiayas->isNotEmpty(),
                        $profitValues,
                    ),
                ],
                'charts' => $charts,
                'latestHarvests' => $latestHarvests,
            ],
        ]);
    }

    /**
     * @param  Collection<int, mixed>  $records
     * @return Collection<string, float>
     */
    private function sumByMonth(Collection $records, callable $amount): Collection
    {
        return $records->groupBy(fn ($record): string => $record->tanggal->format('Y-m'))
            ->map(fn (Collection $rows): float => (float) $rows->sum($amount));
    }

    /**
     * @param  array<float>  $values
     * @return array<string, mixed>
     */
    private function stat(
        string $title,
        float $current,
        string $unit,
        float $previous,
        bool $previousHasData,
        array $values,
    ): array {
        return [
            'key' => str($title)->slug()->value(),
            'title' => $title,
            'value' => $unit === 'Rp' ? $this->compactRupiah($current) : number_format($current, 0, ',', '.'),
            'unit' => $unit === 'kg' ? 'kg' : '',
            'changePercent' => $this->changePercent($current, $previous, $previousHasData),
            'direction' => $current >= $previous ? 'up' : 'down',
            'values' => $values,
            'hasData' => array_sum($values) > 0,
        ];
    }

    private function changePercent(float $current, float $previous, bool $previousHasData): ?float
    {
        if (! $previousHasData || $previous == 0.0) {
            return null;
        }

        return round((($current - $previous) / abs($previous)) * 100, 1);
    }

    private function compactRupiah(float $amount): string
    {
        if (abs($amount) >= 1_000_000) {
            return 'Rp '.number_format($amount / 1_000_000, 1, ',', '.').' jt';
        }

        if (abs($amount) >= 1_000) {
            return 'Rp '.number_format($amount / 1_000, 1, ',', '.').' rb';
        }

        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
