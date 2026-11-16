@extends('layouts.app')

@section('title', 'Biaya')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-[var(--text)]">Catatan biaya</h1>
            <p class="mt-1 text-[var(--muted)]">Lihat dan kelola pengeluaran kebun Anda.</p>
        </div>
        <a href="{{ route('biaya.create') }}" class="button-primary">Catat biaya</a>
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-[#c9d8f4] bg-[var(--green-soft)] px-4 py-3 font-medium text-[var(--green)]" role="status">
            {{ session('success') }}
        </div>
    @endif

    <form method="GET" action="{{ route('biaya.index') }}" class="page-surface mt-5 grid gap-4 p-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_auto_auto] lg:items-end">
        <div>
            <label for="bulan" class="block font-medium text-[var(--text)]">Bulan</label>
            <input id="bulan" name="bulan" type="month" value="{{ $bulan }}" class="form-control mt-2">
        </div>
        <div>
            <label for="filter-kebun" class="block font-medium text-[var(--text)]">Kebun</label>
            <select id="filter-kebun" name="kebun_id" class="form-control mt-2">
                <option value="">Semua kebun</option>
                @foreach ($kebuns as $kebun)
                    <option value="{{ $kebun->id }}" @selected((string) $selectedKebun === (string) $kebun->id)>{{ $kebun->nama }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="filter-kategori" class="block font-medium text-[var(--text)]">Kategori</label>
            <select id="filter-kategori" name="kategori" class="form-control mt-2">
                <option value="">Semua kategori</option>
                @foreach ($kategoriBiaya as $kategori)
                    <option value="{{ $kategori }}" @selected($selectedKategori === $kategori)>{{ $kategori }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="button-primary">Terapkan</button>
        <a href="{{ route('biaya.index') }}" class="button-secondary">Reset</a>
    </form>

    <section class="mt-5 grid gap-3 sm:grid-cols-2" aria-label="Ringkasan biaya untuk filter terpilih">
        <article class="stat-card !min-h-0">
            <p class="stat-title-row">Total biaya</p>
            <p class="stat-value">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Sesuai bulan, kebun, dan kategori terpilih</p>
        </article>
        <article class="stat-card !min-h-0">
            <p class="stat-title-row">Jumlah catatan</p>
            <p class="stat-value">{{ number_format($summary['jumlah_catatan'], 0, ',', '.') }}</p>
            <p class="mt-1 text-sm text-[var(--muted)]">Catatan biaya tersimpan</p>
        </article>
    </section>

    <section class="page-surface mt-3 p-4" aria-label="Rincian biaya per kategori">
        <h2 class="font-semibold text-[var(--text)]">Rincian per kategori</h2>
        <div class="mt-3 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
            @foreach ($summary['kategori'] as $kategori => $total)
                <div class="rounded-lg border border-[var(--border)] bg-[#f7f9fd] px-3 py-2">
                    <p class="text-sm text-[var(--muted)]">{{ $kategori }}</p>
                    <p class="numeric-data mt-1 font-semibold text-[var(--text)]">Rp {{ number_format($total, 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @if ($biayas->isEmpty())
        <section class="page-surface mt-5 flex flex-col items-center gap-4 p-6 text-center">
            <span class="sidebar-icon !h-16 !w-16 !flex-none">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3.5v17m4-13c0-1.8-1.8-3-4-3s-4 1.2-4 3 1.5 2.5 4 3.5 4 1.7 4 3.5-1.8 3-4 3-4-1.2-4-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <p class="font-medium text-[var(--text)]">Belum ada catatan biaya</p>
            <a href="{{ route('biaya.create') }}" class="button-primary">Catat biaya</a>
        </section>
    @else
        <div class="page-surface mt-5 hidden overflow-hidden md:block">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[820px] text-left">
                    <thead class="bg-[#f6f8fc] text-sm font-medium text-[var(--muted)]">
                        <tr>
                            <th scope="col" class="px-4 py-3">Tanggal</th>
                            <th scope="col" class="px-4 py-3">Kebun</th>
                            <th scope="col" class="px-4 py-3">Kategori</th>
                            <th scope="col" class="px-4 py-3 text-right">Nominal</th>
                            <th scope="col" class="px-4 py-3">Keterangan</th>
                            <th scope="col" class="px-4 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--border)]">
                        @foreach ($biayas as $biaya)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3">{{ $biaya->tanggal->format('d/m/Y') }}</td>
                                <td class="px-4 py-3 font-medium">{{ $biaya->kebun->nama }}</td>
                                <td class="px-4 py-3">
                                    {{ $biaya->kategori }}
                                    @if ($biaya->mode_hitung === \App\Models\Biaya::MODE_OTOMATIS)
                                        <span class="ml-1 rounded-full bg-[var(--green-soft)] px-2 py-1 text-xs font-medium text-[var(--green)]">Otomatis</span>
                                    @endif
                                </td>
                                <td class="numeric-data whitespace-nowrap px-4 py-3 text-right font-semibold">Rp {{ number_format((float) $biaya->nominal, 0, ',', '.') }}</td>
                                <td class="max-w-56 truncate px-4 py-3 text-[var(--muted)]">{{ $biaya->keterangan ?: '—' }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('biaya.edit', $biaya) }}" class="button-secondary !min-h-11 !w-11 !p-0" aria-label="Ubah catatan biaya {{ $biaya->tanggal->format('d/m/Y') }}">
                                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 10.7-10.7a2.1 2.1 0 0 0-3-3L5.5 16 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        </a>
                                        <form method="POST" action="{{ route('biaya.destroy', $biaya) }}" onsubmit="return confirm('Hapus catatan biaya ini? Data tidak dapat dikembalikan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="button-danger !min-h-11 !w-11 !p-0" aria-label="Hapus catatan biaya {{ $biaya->tanggal->format('d/m/Y') }}">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
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
            @foreach ($biayas as $biaya)
                <article class="p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold text-[var(--text)]">{{ $biaya->kategori }}</h2>
                            <p class="mt-1 text-sm text-[var(--muted)]">{{ $biaya->kebun->nama }} · {{ $biaya->tanggal->format('d/m/Y') }}</p>
                        </div>
                        <p class="numeric-data text-right font-semibold text-[var(--text)]">Rp {{ number_format((float) $biaya->nominal, 0, ',', '.') }}</p>
                    </div>
                    @if ($biaya->mode_hitung === \App\Models\Biaya::MODE_OTOMATIS)
                        <span class="mt-2 inline-flex rounded-full bg-[var(--green-soft)] px-2 py-1 text-xs font-medium text-[var(--green)]">Otomatis</span>
                    @endif
                    <p class="mt-3 text-sm text-[var(--muted)]">Keterangan <span class="block font-medium text-[var(--text)]">{{ $biaya->keterangan ?: '—' }}</span></p>
                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('biaya.edit', $biaya) }}" class="button-secondary flex-1">Ubah</a>
                        <form method="POST" action="{{ route('biaya.destroy', $biaya) }}" class="flex-1" onsubmit="return confirm('Hapus catatan biaya ini? Data tidak dapat dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="button-danger w-full">Hapus</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        @if ($biayas->hasPages())
            <div class="mt-5">{{ $biayas->links() }}</div>
        @endif
    @endif
@endsection
