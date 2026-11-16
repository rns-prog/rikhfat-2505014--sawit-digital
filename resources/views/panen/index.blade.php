@extends('layouts.app')

@section('title', 'Panen')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-[var(--text)]">Catatan panen</h1>
            <p class="mt-1 text-[var(--muted)]">Lihat dan kelola hasil panen kebun Anda.</p>
        </div>
        <a href="{{ route('panen.create') }}" class="button-primary">Catat panen</a>
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-[#c9d8f4] bg-[var(--green-soft)] px-4 py-3 font-medium text-[var(--green)]" role="status">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('panen.index') }}" class="page-surface mt-5 grid gap-4 p-4 sm:grid-cols-[1fr_1fr_auto_auto] sm:items-end">
        <div>
            <label for="bulan" class="block font-medium text-[var(--text)]">Bulan</label>
            <input id="bulan" name="bulan" type="month" value="{{ $bulan }}" class="form-control mt-2">
        </div>
        <div>
            <label for="kebun_id" class="block font-medium text-[var(--text)]">Kebun</label>
            <select id="kebun_id" name="kebun_id" class="form-control mt-2">
                <option value="">Semua kebun</option>
                @foreach ($kebuns as $kebun)
                    <option value="{{ $kebun->id }}" @selected((string) $selectedKebun === (string) $kebun->id)>{{ $kebun->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="button-primary">Terapkan</button>
        <a href="{{ route('panen.index') }}" class="button-secondary">Reset</a>
    </form>

    <section class="mt-5 grid gap-3 sm:grid-cols-3" aria-label="Ringkasan panen untuk filter terpilih">
        <article class="stat-card !min-h-0">
            <p class="stat-title-row">Total berat</p>
            <p class="stat-value">{{ number_format((float) $summary->total_berat, 2, ',', '.') }} kg</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Sesuai bulan dan kebun terpilih</p>
        </article>
        <article class="stat-card !min-h-0">
            <p class="stat-title-row">Total pendapatan</p>
            <p class="stat-value">Rp {{ number_format((float) $summary->total_pendapatan, 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Berat dikali harga per kg</p>
        </article>
        <article class="stat-card !min-h-0">
            <p class="stat-title-row">Jumlah catatan</p>
            <p class="stat-value">{{ number_format((int) $summary->jumlah_catatan, 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Catatan panen tersimpan</p>
        </article>
    </section>

    @if ($panens->isEmpty())
        <section class="page-surface mt-5 flex flex-col items-center gap-4 p-6 text-center">
            <span class="sidebar-icon !h-16 !w-16 !flex-none">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 20.5V10m0 5c-1.6-4-4.8-6-9-5.8 1.1 4.1 4.1 6.1 9 6.3Zm0-2.3c1.7-4.1 5-6 9-5.5-1.2 4.1-4.2 6-9 6Zm0-6c-.5-2-1.7-3.7-3.6-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <p class="font-medium text-[var(--text)]">Belum ada catatan panen</p>
            <a href="{{ route('panen.create') }}" class="button-primary">Catat panen</a>
        </section>
    @else
        <div class="page-surface mt-5 hidden overflow-hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <thead class="bg-[#f6f8fc] text-sm font-medium text-[var(--muted)]">
                        <tr>
                            <th scope="col" class="px-4 py-3">Tanggal</th>
                            <th scope="col" class="px-4 py-3">Kebun</th>
                            <th scope="col" class="px-4 py-3 text-right">Jumlah tandan</th>
                            <th scope="col" class="px-4 py-3 text-right">Berat</th>
                            <th scope="col" class="px-4 py-3 text-right">Harga / kg</th>
                            <th scope="col" class="px-4 py-3 text-right">Pendapatan</th>
                            <th scope="col" class="px-4 py-3">Pembeli</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @foreach ($panens as $panen)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3">{{ $panen->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-medium">{{ $panen->kebun->nama }}</td>
                                <td class="numeric-data whitespace-nowrap px-4 py-3 text-right">{{ number_format($panen->jumlah_tandan, 0, ',', '.') }}</td>
                                <td class="numeric-data whitespace-nowrap px-4 py-3 text-right">{{ number_format((float) $panen->berat_kg, 2, ',', '.') }} kg</td>
                                <td class="numeric-data whitespace-nowrap px-4 py-3 text-right">Rp {{ number_format((float) $panen->harga_per_kg, 0, ',', '.') }}</td>
                                <td class="numeric-data whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format($panen->pendapatan, 0, ',', '.') }}</td>
                                <td class="max-w-48 truncate px-4 py-3 text-[var(--muted)]">{{ $panen->pembeli ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('panen.edit', $panen) }}" class="button-secondary !min-h-11 !w-11 !p-0" aria-label="Ubah catatan panen {{ $panen->tanggal->format('d/m/Y') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="m14 5 5 5M4 20l4.5-1 10.7-10.7a2.1 2.1 0 0 0-3-3L5.5 16 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('panen.destroy', $panen) }}" onsubmit="return confirm('Hapus catatan panen ini? Data tidak dapat dikembalikan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button-danger !min-h-11 !w-11 !p-0" aria-label="Hapus catatan panen {{ $panen->tanggal->format('d/m/Y') }}">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
        <div class="page-surface mt-5 divide-y divide-[var(--border)] md:hidden">
            @foreach ($panens as $panen)
                <article class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-[var(--text)]">{{ $panen->kebun->nama }}</h2>
                            <p class="mt-1 text-sm text-[var(--muted)]">{{ $panen->tanggal->format('d/m/Y') }} · {{ number_format($panen->jumlah_tandan, 0, ',', '.') }} tandan</p>
                        </div>
                        <p class="numeric-data text-right font-semibold text-[var(--text)]">Rp {{ number_format($panen->pendapatan, 0, ',', '.') }}</p>
                    </div>
                    <div class="mt-3 grid grid-cols-2 gap-2 text-sm">
                        <p class="text-[var(--muted)]">Berat <span class="numeric-data block font-medium text-[var(--text)]">{{ number_format((float) $panen->berat_kg, 2, ',', '.') }} kg</span></p>
                        <p class="text-[var(--muted)]">Harga / kg <span class="numeric-data block font-medium text-[var(--text)]">Rp {{ number_format((float) $panen->harga_per_kg, 0, ',', '.') }}</span></p>
                        <p class="col-span-2 text-[var(--muted)]">Pembeli <span class="block font-medium text-[var(--text)]">{{ $panen->pembeli ?: '—' }}</span></p>
                    </div>
                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('panen.edit', $panen) }}" class="button-secondary flex-1">Ubah</a>
                        <form method="POST" action="{{ route('panen.destroy', $panen) }}" class="flex-1" onsubmit="return confirm('Hapus catatan panen ini? Data tidak dapat dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-danger w-full">Hapus</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($panens->hasPages())
            <div class="mt-5">{{ $panens->links() }}</div>
        @endif
    @endif
@endsection
