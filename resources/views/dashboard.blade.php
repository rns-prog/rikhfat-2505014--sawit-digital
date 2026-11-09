@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <section class="dashboard-banner relative isolate overflow-hidden rounded-2xl px-5 py-6 text-white sm:px-7 sm:py-7">
        <svg class="pointer-events-none absolute -right-2 top-0 -z-10 h-full w-52 opacity-[0.08] sm:w-72" viewBox="0 0 220 180" fill="none" aria-hidden="true">
            <path d="M170 181V65m0 47c-9-35-35-51-72-49 10 29 32 48 72 49Zm0-21c13-34 40-48 76-42-13 30-36 45-76 42Zm0-46c-5-23-18-41-39-54m39 40c10-21 25-37 47-49M92 181V105m0 30c-7-24-25-35-52-33 7 20 23 33 52 33Zm0-14c9-23 27-33 52-29-9 20-25 31-52 29Z" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <div class="relative z-10 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold text-white sm:text-3xl">Halo, {{ ucwords(Auth::user()->name) }}</h1>
                <p class="mt-1 text-white/90">Ringkasan kebun Anda bulan ini</p>
            </div>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                <button type="button" disabled class="inline-flex min-h-11 items-center justify-center rounded-[10px] bg-white px-4 py-2 font-semibold text-[var(--green)] transition hover:bg-[var(--green-soft)] disabled:cursor-not-allowed disabled:opacity-85">Catat panen</button>
                <button type="button" disabled class="inline-flex min-h-11 items-center justify-center rounded-[10px] border border-white/80 bg-transparent px-4 py-2 font-semibold text-white transition hover:bg-white/10 disabled:cursor-not-allowed disabled:opacity-85">Catat biaya</button>
            </div>
        </div>
    </section>

    <section class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4" aria-label="Ringkasan bulan ini">
        <article class="summary-card">
            <span class="summary-card-icon">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3.5 10 12 3.5 20.5 10v9.5h-17V10Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                    <path d="M8.5 19.5v-6h7v6" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="summary-card-label">Jumlah kebun</h2>
            <p class="numeric-value">{{ number_format($jumlahKebun, 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-[var(--muted)]">kebun / blok</p>
        </article>
        <article class="summary-card">
            <span class="summary-card-icon">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 20.5V10m0 5c-1.6-4-4.8-6-9-5.8 1.1 4.1 4.1 6.1 9 6.3Zm0-2.3c1.7-4.1 5-6 9-5.5-1.2 4.1-4.2 6-9 6Zm0-6c-.5-2-1.7-3.7-3.6-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                    <circle cx="12" cy="19.5" r="2" fill="currentColor"/>
                </svg>
            </span>
            <h2 class="summary-card-label">Panen bulan ini</h2>
            <p class="numeric-value">0 <span class="text-lg font-normal">kg</span></p>
            <p class="mt-1 text-sm text-[var(--muted)]">Belum ada data</p>
        </article>
        <article class="summary-card">
            <span class="summary-card-icon">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3.5v17m4-13c0-1.8-1.8-3-4-3s-4 1.2-4 3 1.5 2.5 4 3.5 4 1.7 4 3.5-1.8 3-4 3-4-1.2-4-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <h2 class="summary-card-label">Biaya bulan ini</h2>
            <p class="numeric-value"><span class="text-lg font-normal">Rp</span> 0</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Belum ada data</p>
        </article>
        <article class="summary-card summary-card-profit">
            <span class="summary-card-icon">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m3.5 17 5.2-5.2 3.5 3.1 8.3-9m-6.5 0h6.5v6.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <h2 class="summary-card-label">Laba bulan ini</h2>
            <p class="numeric-value"><span class="text-lg font-normal">Rp</span> 0</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Belum ada data</p>
        </article>
    </section>

    @if ($jumlahKebun === 0)
        <section class="page-surface mt-6 flex flex-col items-start gap-4 p-5 sm:flex-row sm:items-center sm:p-6">
            <svg class="h-[72px] w-[72px] shrink-0" viewBox="0 0 80 80" fill="none" aria-hidden="true">
                <circle cx="40" cy="40" r="36" fill="#E8F4EA"/>
                <path d="M39 69c5-18 5-33 2-47" stroke="#1E7B3A" stroke-width="3.5" stroke-linecap="round"/>
                <path d="M41 28C32 19 24 17 17 19c6 6 13 10 23 11-11-8-16-15-16-23 8 5 13 11 17 22-1-13 2-22 8-29 3 9 2 18-5 30 9-11 17-17 27-17-4 8-11 15-26 20 13-4 23-3 29 3-9 4-19 4-31 1 11 5 17 12 19 19-9-3-17-10-22-21Z" fill="#1E7B3A" fill-opacity=".12" stroke="#1E7B3A" stroke-width="2" stroke-linejoin="round"/>
                <g fill="#F5A623">
                    <circle cx="39" cy="34" r="2.8"/>
                    <circle cx="44" cy="37" r="2.8"/>
                    <circle cx="39" cy="40" r="2.8"/>
                </g>
            </svg>
            <p class="flex-1 text-lg font-medium text-[var(--text)]">Mulai dengan menambah kebun</p>
            <a href="{{ route('kebun.create') }}" class="button-primary">Tambah kebun</a>
        </section>
    @else
        <section class="mt-6 grid grid-cols-1 gap-5 lg:grid-cols-[1.25fr_1fr]">
            <div>
                <h2 class="mb-3 text-lg font-semibold text-[var(--text)]">Grafik panen</h2>
                <div class="page-surface flex min-h-24 items-center gap-4 px-5 py-4">
                    <svg class="h-[72px] w-[72px] shrink-0" viewBox="0 0 80 80" fill="none" aria-hidden="true">
                        <circle cx="40" cy="40" r="36" fill="#E8F4EA"/>
                        <path d="M39 69c5-18 5-33 2-47" stroke="#1E7B3A" stroke-width="3.5" stroke-linecap="round"/>
                        <path d="M41 28C32 19 24 17 17 19c6 6 13 10 23 11-11-8-16-15-16-23 8 5 13 11 17 22-1-13 2-22 8-29 3 9 2 18-5 30 9-11 17-17 27-17-4 8-11 15-26 20 13-4 23-3 29 3-9 4-19 4-31 1 11 5 17 12 19 19-9-3-17-10-22-21Z" fill="#1E7B3A" fill-opacity=".12" stroke="#1E7B3A" stroke-width="2" stroke-linejoin="round"/>
                        <g fill="#F5A623">
                            <circle cx="39" cy="34" r="2.8"/>
                            <circle cx="44" cy="37" r="2.8"/>
                            <circle cx="39" cy="40" r="2.8"/>
                        </g>
                    </svg>
                    <p class="text-[var(--muted)]">Belum ada data</p>
                </div>
            </div>
            <div>
                <h2 class="mb-3 text-lg font-semibold text-[var(--text)]">Panen terbaru</h2>
                <div class="page-surface flex min-h-24 items-center gap-4 px-5 py-4">
                    <svg class="h-[72px] w-[72px] shrink-0" viewBox="0 0 80 80" fill="none" aria-hidden="true">
                        <circle cx="40" cy="40" r="36" fill="#E8F4EA"/>
                        <path d="M39 69c5-18 5-33 2-47" stroke="#1E7B3A" stroke-width="3.5" stroke-linecap="round"/>
                        <path d="M41 28C32 19 24 17 17 19c6 6 13 10 23 11-11-8-16-15-16-23 8 5 13 11 17 22-1-13 2-22 8-29 3 9 2 18-5 30 9-11 17-17 27-17-4 8-11 15-26 20 13-4 23-3 29 3-9 4-19 4-31 1 11 5 17 12 19 19-9-3-17-10-22-21Z" fill="#1E7B3A" fill-opacity=".12" stroke="#1E7B3A" stroke-width="2" stroke-linejoin="round"/>
                        <g fill="#F5A623">
                            <circle cx="39" cy="34" r="2.8"/>
                            <circle cx="44" cy="37" r="2.8"/>
                            <circle cx="39" cy="40" r="2.8"/>
                        </g>
                    </svg>
                    <p class="text-[var(--muted)]">Belum ada data</p>
                </div>
            </div>
        </section>
    @endif
@endsection
