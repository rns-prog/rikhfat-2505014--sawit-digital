@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard-columns">
        <div class="dashboard-center">
            @php($sample = $dataContoh)

            <div class="schedule-strip">
                <div class="schedule-strip-message">
                    <span class="schedule-countdown">{{ $sample['nextSchedule']['daysUntil'] }} hari lagi</span>
                    <span>{{ $sample['nextSchedule']['title'] }} di {{ $sample['nextSchedule']['location'] }}</span>
                </div>
                <a href="#jadwal">Lihat jadwal</a>
            </div>

            <section class="revenue-hero" aria-label="Pendapatan bulan ini">
                <svg class="hero-watermark" viewBox="0 0 220 220" fill="none" aria-hidden="true">
                    <path d="M110 220V83m0 54C99 97 70 79 30 83c10 33 37 53 80 55Zm0-24c15-40 45-56 87-49-15 35-41 53-87 49Zm0-51c-6-27-21-48-46-63m46 47c12-25 30-44 56-58M58 220v-82m0 33c-7-25-25-37-54-35 8 22 25 35 54 35Z" stroke="white" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="110" cy="145" r="7" fill="#F59E0B"/>
                    <circle cx="122" cy="153" r="7" fill="#F59E0B"/>
                    <circle cx="108" cy="161" r="7" fill="#F59E0B"/>
                </svg>
                <div class="revenue-hero-top">
                    <div>
                        <p class="revenue-hero-label">Total pendapatan bulan ini</p>
                        <p class="revenue-hero-value">Rp {{ number_format($sample['revenue']['total'], 0, ',', '.') }}</p>
                        <div class="revenue-hero-foot">
                            <span class="trend-positive">
                                <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <path d="m3 10 4-4 3 3 3-4m-4 0h4v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Naik {{ number_format($sample['revenue']['change'], 1, ',', '.') }}%
                            </span>
                            <span>dari bulan lalu</span>
                            <span class="sample-badge">Data contoh</span>
                        </div>
                    </div>
                </div>
                <div class="hero-chart">
                    <x-chart id="revenue-chart" type="area" :labels="$sample['revenue']['months']" :values="$sample['revenue']['values']" unit="Rp" />
                </div>
            </section>

            <section class="stats-grid" aria-label="Statistik bulan ini">
                @foreach ($sample['stats'] as $stat)
                    <article class="stat-card">
                        <div class="stat-title-row">
                            <span>{{ $stat['title'] }}</span>
                            <span class="sample-badge">Data contoh</span>
                        </div>
                        <p class="stat-value">{{ $stat['value'] }} {{ $stat['unit'] }}</p>
                        <div class="stat-bottom">
                            <p @class(['stat-change', 'is-down' => $stat['direction'] === 'down'])>
                                @if ($stat['direction'] === 'up')
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3 10 4-4 3 3 3-4m-4 0h4v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <strong>Naik {{ number_format($stat['change'], 1, ',', '.') }}%</strong>
                                @else
                                    <svg class="h-3.5 w-3.5" viewBox="0 0 16 16" fill="none" aria-hidden="true"><path d="m3 6 4 4 3-3 3 4m-4 0h4V7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    <strong>Turun {{ number_format($stat['change'], 1, ',', '.') }}%</strong>
                                @endif
                                <span>vs bulan lalu</span>
                            </p>
                            <div class="stat-sparkline">
                                <x-chart :id="'sparkline-'.$stat['key']" type="sparkline" :labels="$sample['revenue']['months']" :values="$stat['values']" sparkline />
                            </div>
                        </div>
                    </article>
                @endforeach
            </section>

            <section class="charts-section" aria-labelledby="charts-title">
                <div class="charts-section-title">
                    <h2 id="charts-title">Grafik</h2>
                </div>
                <div class="charts-grid">
                    @foreach (['harvest', 'fertilizer', 'wages', 'expenses', 'productivity'] as $chartKey)
                        @php($chart = $sample['charts'][$chartKey])
                        <article @class(['chart-card', 'chart-card-wide' => $chartKey === 'productivity'])>
                            <div class="card-heading">
                                <h3>{{ $chart['title'] }}</h3>
                                <span class="sample-badge">Data contoh</span>
                            </div>
                            <div class="chart-plot">
                                <x-chart :id="'chart-'.$chartKey" :type="$chart['type']" :labels="$chart['labels']" :values="$chart['values']" :unit="$chart['unit']" />
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <section class="charts-section" aria-labelledby="latest-harvests-title">
                <div class="charts-section-title">
                    <h2 id="latest-harvests-title">Panen terbaru</h2>
                    <span class="sample-badge">Data contoh</span>
                </div>
                <div class="harvest-table-wrap">
                    <table class="harvest-table">
                        <thead>
                            <tr>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Kebun</th>
                                <th scope="col">Berat</th>
                                <th scope="col">Harga / kg</th>
                                <th scope="col">Total</th>
                                <th scope="col">Pembeli</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sample['latestHarvests'] as $harvest)
                                <tr>
                                    <td>{{ $harvest['tanggal'] }}</td>
                                    <td>{{ $harvest['kebun'] }}</td>
                                    <td class="numeric-data">{{ number_format($harvest['berat'], 0, ',', '.') }} kg</td>
                                    <td class="numeric-data">Rp {{ number_format($harvest['harga'], 0, ',', '.') }}</td>
                                    <td class="table-amount">Rp {{ number_format($harvest['total'], 0, ',', '.') }}</td>
                                    <td>{{ $harvest['pembeli'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="harvest-mobile-list">
                    @foreach ($sample['latestHarvests'] as $harvest)
                        <article class="harvest-mobile-item">
                            <strong>{{ $harvest['kebun'] }} · {{ $harvest['tanggal'] }}</strong>
                            <span>{{ number_format($harvest['berat'], 0, ',', '.') }} kg · Rp {{ number_format($harvest['harga'], 0, ',', '.') }}/kg</span>
                            <span>{{ $harvest['pembeli'] }}</span>
                            <span class="harvest-mobile-total">Rp {{ number_format($harvest['total'], 0, ',', '.') }}</span>
                        </article>
                    @endforeach
                </div>
                <p class="data-note">Data panen pada tabel ini adalah data contoh.</p>
            </section>
        </div>

        <aside class="dashboard-right">
            <section class="right-card" aria-labelledby="my-estates-title">
                <div class="card-heading">
                    <h2 id="my-estates-title">Kebun saya</h2>
                    <a href="{{ route('kebun.index') }}" class="text-link">Lihat semua</a>
                </div>
                @if ($kebuns->isEmpty())
                    <p class="empty-note">Belum ada kebun. Tambahkan kebun untuk mulai mengelola catatan.</p>
                @else
                    <div class="estate-list">
                        @foreach ($kebuns as $kebun)
                            <div class="estate-item">
                                <span class="estate-avatar" aria-hidden="true">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none">
                                        <path d="M3 20.5h18M5.5 20V9.5h13V20M8 9.5V6h8v3.5M9 13h2m2 0h2m-6 3h2m2 0h2" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="estate-meta">
                                    <strong>{{ $kebun->nama }}</strong>
                                    <span>{{ $kebun->lokasi }}</span>
                                </span>
                                <span class="estate-size">{{ number_format((float) $kebun->luas_ha, 2, ',', '.') }} ha</span>
                            </div>
                        @endforeach
                    </div>
                @endif
                <a href="{{ route('kebun.create') }}" class="button-primary mt-4 w-full">Tambah kebun</a>
            </section>

            <section id="jadwal" class="right-card" aria-labelledby="schedule-title">
                <div class="card-heading">
                    <h2 id="schedule-title">Jadwal</h2>
                    <span class="sample-badge">Data contoh</span>
                </div>
                <div class="flex items-center justify-between gap-2">
                    <span id="calendar-month" class="calendar-month" aria-live="polite"></span>
                    <div class="calendar-controls">
                        <button type="button" data-calendar-step="-1" aria-label="Bulan sebelumnya">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m12 4-6 6 6 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                        <button type="button" data-calendar-step="1" aria-label="Bulan berikutnya">
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m8 4 6 6-6 6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                        </button>
                    </div>
                </div>
                <div class="calendar-weekdays mt-3" aria-hidden="true">
                    <span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span>
                </div>
                <div id="calendar-grid" class="calendar-grid" data-calendar-events="{{ json_encode($sample['calendarEvents'], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"></div>
                <div class="calendar-legend">
                    <span><i class="legend-dot"></i> Panen</span>
                    <span><i class="legend-dot is-fertilizer"></i> Pemupukan</span>
                </div>
                <h3 class="upcoming-title">Akan datang</h3>
                <div class="upcoming-list">
                    @foreach ($sample['upcoming'] as $event)
                        <div class="upcoming-item">
                            <span @class(['upcoming-date', 'is-harvest' => $event['type'] === 'panen', 'is-fertilizer' => $event['type'] === 'pupuk'])>{{ $event['date'] }}</span>
                            <span class="upcoming-copy">
                                <strong>{{ $event['title'] }}</strong>
                                <span>{{ $event['location'] }}</span>
                            </span>
                        </div>
                    @endforeach
                </div>
                <p class="data-note">Jadwal ini adalah data contoh.</p>
            </section>
        </aside>
    </div>
@endsection
