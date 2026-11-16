@extends('layouts.app')

@section('title', 'Laporan untung-rugi')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-[var(--text)]">Laporan untung-rugi</h1>
            <p class="mt-1 text-[var(--muted)]">Ringkasan hasil dan pengeluaran kebun Anda.</p>
        </div>
        <a
            href="{{ route('laporan.pdf', request()->query()) }}"
            class="button-primary"
        >Unduh PDF</a>
    </div>

    <form method="GET" action="{{ route('laporan') }}" class="page-surface mt-5 grid gap-4 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto_auto] lg:items-end">
        <div>
            <label for="tahun" class="block font-medium text-[var(--text)]">Tahun</label>
            <select id="tahun" name="tahun" class="form-control mt-2">
                @foreach ($years as $year)
                    <option value="{{ $year }}" @selected($report['year'] === $year)>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="mode" class="block font-medium text-[var(--text)]">Tampilan</label>
            <select id="mode" name="mode" class="form-control mt-2">
                <option value="bulan" @selected($report['mode'] === 'bulan')>Per bulan</option>
                <option value="tahun" @selected($report['mode'] === 'tahun')>Per tahun</option>
            </select>
        </div>
        <div>
            <label for="kebun_id" class="block font-medium text-[var(--text)]">Kebun</label>
            <select id="kebun_id" name="kebun_id" class="form-control mt-2">
                <option value="">Semua kebun</option>
                @foreach ($report['kebuns'] as $kebun)
                    <option value="{{ $kebun->id }}" @selected((string) request('kebun_id') === (string) $kebun->id)>{{ $kebun->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="button-primary">Terapkan</button>
        <a href="{{ route('laporan') }}" class="button-secondary">Reset</a>
    </form>

    @if (! $report['hasData'])
        <div class="page-surface mt-5 flex items-center gap-3 border-dashed p-4 text-[var(--muted)]" role="status">
            <span class="sidebar-icon !h-10 !w-10 !flex-none" aria-hidden="true">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                    <path d="M4 19.5V4.5m0 15h16M7 15l4-4 3 2 5-6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <p>Belum ada data panen atau biaya untuk pilihan ini. Nilai laporan ditampilkan 0.</p>
        </div>
    @endif

    <section class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4" aria-label="Ringkasan untung-rugi">
        <article class="stat-card !min-h-0">
            <p class="stat-title-row">Total panen</p>
            <p class="stat-value">{{ number_format($report['summary']['panen_kg'], 0, ',', '.') }} kg</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Hasil panen tahun {{ $report['year'] }}</p>
        </article>
        <article class="stat-card !min-h-0">
            <p class="stat-title-row">Total pendapatan</p>
            <p class="stat-value">Rp {{ number_format($report['summary']['pendapatan'], 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Nilai seluruh hasil panen</p>
        </article>
        <article class="stat-card !min-h-0">
            <p class="stat-title-row">Total biaya</p>
            <p class="stat-value">Rp {{ number_format($report['summary']['biaya'], 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Pengeluaran kebun</p>
        </article>
        <article @class(['stat-card !min-h-0', 'border-l-4 !border-l-[#1FA45B]' => $report['summary']['laba'] >= 0, 'border-l-4 !border-l-[#E5484D]' => $report['summary']['laba'] < 0])>
            <p class="stat-title-row">Laba bersih</p>
            <p @class(['stat-value', '!text-[#1FA45B]' => $report['summary']['laba'] >= 0, '!text-[#E5484D]' => $report['summary']['laba'] < 0])>
                Rp {{ number_format($report['summary']['laba'], 0, ',', '.') }}
            </p>
            <p class="mt-1 text-sm text-[var(--muted)]">Pendapatan dikurangi biaya</p>
        </article>
    </section>

    <section class="charts-section mt-6" aria-labelledby="report-charts-heading">
        <div class="charts-section-title">
            <h2 id="report-charts-heading">Grafik laporan</h2>
        </div>
        <div class="charts-grid">
            <article class="chart-card">
                <div class="card-heading"><h3>Pendapatan, biaya, dan laba per bulan</h3></div>
                @if ($report['charts']['monthly']['hasData'])
                    <div class="chart-plot">
                        <x-chart
                            id="laporan-monthly-chart"
                            type="bar"
                            :labels="$report['charts']['monthly']['labels']"
                            :values="[]"
                            :datasets="$report['charts']['monthly']['datasets']"
                            unit="Rp"
                        />
                    </div>
                @else
                    <div class="chart-empty-state">
                        <span class="chart-empty-icon" aria-hidden="true">—</span>
                        <span>Belum ada data untuk ditampilkan</span>
                    </div>
                @endif
            </article>
            <article class="chart-card">
                <div class="card-heading"><h3>Komposisi biaya per kategori</h3></div>
                @if ($report['charts']['categories']['hasData'])
                    <div class="chart-plot">
                        <x-chart
                            id="laporan-category-chart"
                            type="doughnut"
                            :labels="$report['charts']['categories']['labels']"
                            :values="$report['charts']['categories']['values']"
                            unit="Rp"
                        />
                    </div>
                @else
                    <div class="chart-empty-state">
                        <span class="chart-empty-icon" aria-hidden="true">—</span>
                        <span>Belum ada data biaya per kategori</span>
                    </div>
                @endif
            </article>
        </div>
    </section>

    <section class="charts-section mt-6" aria-labelledby="report-period-heading">
        <div class="charts-section-title">
            <h2 id="report-period-heading">Rekap {{ $report['mode'] === 'bulan' ? 'per bulan' : 'per tahun' }}</h2>
        </div>
        <div class="page-surface overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left">
                    <thead class="bg-[#f6f8fc] text-sm font-medium text-[var(--muted)]">
                        <tr>
                            <th scope="col" class="px-4 py-3">Periode</th>
                            <th scope="col" class="px-4 py-3 text-right">Panen (kg)</th>
                            <th scope="col" class="px-4 py-3 text-right">Pendapatan</th>
                            <th scope="col" class="px-4 py-3 text-right">Biaya</th>
                            <th scope="col" class="px-4 py-3 text-right">Laba bersih</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @foreach ($report['periodRows'] as $row)
                            <tr>
                                <th scope="row" class="px-4 py-3 font-medium text-[var(--text)]">{{ $row['periode'] }}</th>
                                <td class="numeric-data whitespace-nowrap px-4 py-3 text-right">{{ number_format($row['panen_kg'], 0, ',', '.') }}</td>
                                <td class="numeric-data whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format($row['pendapatan'], 0, ',', '.') }}</td>
                                <td class="numeric-data whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format($row['biaya'], 0, ',', '.') }}</td>
                                <td @class(['numeric-data whitespace-nowrap px-4 py-3 text-right font-semibold', 'text-[#1FA45B]' => $row['laba'] >= 0, 'text-[#E5484D]' => $row['laba'] < 0])>Rp {{ number_format($row['laba'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="border-t-2 border-[var(--border)] bg-[#f6f8fc] font-semibold">
                        <tr>
                            <th scope="row" class="px-4 py-3">Total {{ $report['year'] }}</th>
                            <td class="numeric-data px-4 py-3 text-right">{{ number_format($report['summary']['panen_kg'], 0, ',', '.') }}</td>
                            <td class="numeric-data px-4 py-3 text-right">Rp {{ number_format($report['summary']['pendapatan'], 0, ',', '.') }}</td>
                            <td class="numeric-data px-4 py-3 text-right">Rp {{ number_format($report['summary']['biaya'], 0, ',', '.') }}</td>
                            <td @class(['numeric-data px-4 py-3 text-right', 'text-[#1FA45B]' => $report['summary']['laba'] >= 0, 'text-[#E5484D]' => $report['summary']['laba'] < 0])>Rp {{ number_format($report['summary']['laba'], 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>

    <section class="charts-section mt-6" aria-labelledby="report-estates-heading">
        <div class="charts-section-title">
            <h2 id="report-estates-heading">Perbandingan kebun / blok</h2>
        </div>
        <div class="page-surface overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <thead class="bg-[#f6f8fc] text-sm font-medium text-[var(--muted)]">
                        <tr>
                            <th scope="col" class="px-4 py-3">Kebun</th>
                            <th scope="col" class="px-4 py-3 text-right">Total kg</th>
                            <th scope="col" class="px-4 py-3 text-right">Pendapatan</th>
                            <th scope="col" class="px-4 py-3 text-right">Biaya</th>
                            <th scope="col" class="px-4 py-3 text-right">Laba</th>
                            <th scope="col" class="px-4 py-3 text-right">Produktivitas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @forelse ($report['estateRows'] as $estate)
                            <tr>
                                <th scope="row" class="px-4 py-3 font-medium text-[var(--text)]">
                                    {{ $estate['nama'] }}
                                    @if ($report['bestEstateName'] === $estate['nama'])
                                        <span class="ml-1 rounded-full bg-[#E6EEFB] px-2 py-1 text-xs font-medium text-[#1F4FB5]">Paling untung</span>
                                    @endif
                                </th>
                                <td class="numeric-data px-4 py-3 text-right">{{ number_format($estate['panen_kg'], 0, ',', '.') }}</td>
                                <td class="numeric-data px-4 py-3 text-right">Rp {{ number_format($estate['pendapatan'], 0, ',', '.') }}</td>
                                <td class="numeric-data px-4 py-3 text-right">Rp {{ number_format($estate['biaya'], 0, ',', '.') }}</td>
                                <td @class(['numeric-data px-4 py-3 text-right font-semibold', 'text-[#1FA45B]' => $estate['laba'] >= 0, 'text-[#E5484D]' => $estate['laba'] < 0])>Rp {{ number_format($estate['laba'], 0, ',', '.') }}</td>
                                <td class="numeric-data px-4 py-3 text-right">{{ $estate['produktivitas'] === null ? '—' : number_format($estate['produktivitas'], 2, ',', '.').' kg/ha' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-4 py-5 text-center text-[var(--muted)]">Belum ada kebun untuk ditampilkan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="charts-section mt-6" aria-labelledby="report-category-heading">
        <div class="charts-section-title">
            <h2 id="report-category-heading">Rincian biaya per kategori</h2>
        </div>
        <div class="page-surface overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-[#f6f8fc] text-sm font-medium text-[var(--muted)]">
                    <tr>
                        <th scope="col" class="px-4 py-3">Kategori</th>
                        <th scope="col" class="px-4 py-3 text-right">Total</th>
                        <th scope="col" class="px-4 py-3 text-right">Persentase</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @foreach ($report['categoryRows'] as $row)
                        <tr>
                            <th scope="row" class="px-4 py-3 font-medium text-[var(--text)]">{{ $row['kategori'] }}</th>
                            <td class="numeric-data px-4 py-3 text-right">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                            <td class="numeric-data px-4 py-3 text-right">{{ number_format($row['persentase'], 1, ',', '.') }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
