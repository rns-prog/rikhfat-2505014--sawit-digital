@extends('layouts.app')

@section('title', 'Jadwal pengingat')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-[var(--text)]">Jadwal pengingat</h1>
            <p class="mt-1 text-[var(--muted)]">Atur pengingat panen dan pemupukan kebun.</p>
        </div>
        <a href="{{ route('jadwal.create') }}" class="button-primary">Tambah jadwal</a>
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-[#c9d8f4] bg-[var(--green-soft)] px-4 py-3 font-medium text-[var(--green)]" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($jadwals->isEmpty())
        <section class="page-surface mt-5 flex flex-col items-center gap-4 p-6 text-center">
            <span class="sidebar-icon !h-16 !w-16 !flex-none" aria-hidden="true">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none">
                    <rect x="3.5" y="5" width="17" height="15.5" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M7.5 3.5v3M16.5 3.5v3M3.5 9.5h17M8 13h.01M12 13h.01M16 13h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            <p class="font-medium text-[var(--text)]">Belum ada jadwal</p>
            <a href="{{ route('jadwal.create') }}" class="button-primary">Tambah jadwal</a>
        </section>
    @else
        <div class="mt-5 grid gap-4 lg:grid-cols-2">
            @foreach ($jadwals as $jadwal)
                <article class="page-surface p-4 sm:p-5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex min-w-0 items-start gap-3">
                            <span @class([
                                'sidebar-icon !h-11 !w-11 !flex-none',
                                '!bg-[#E6EEFB] !text-[#1F4FB5]' => $jadwal->jenis === \App\Models\Jadwal::JENIS_PANEN,
                                '!bg-[#E6F6ED] !text-[#1FA45B]' => $jadwal->jenis === \App\Models\Jadwal::JENIS_PUPUK,
                            ]) aria-hidden="true">
                                @if ($jadwal->jenis === \App\Models\Jadwal::JENIS_PANEN)
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 20.5V10m0 5c-1.6-4-4.8-6-9-5.8 1.1 4.1 4.1 6.1 9 6.3Zm0-2.3c1.7-4.1 5-6 9-5.5-1.2 4.1-4.2 6-9 6Zm0-6c-.5-2-1.7-3.7-3.6-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @else
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none"><path d="M12 3.5v17m4-13c0-1.8-1.8-3-4-3s-4 1.2-4 3 1.5 2.5 4 3.5 4 1.7 4 3.5-1.8 3-4 3-4-1.2-4-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/></svg>
                                @endif
                            </span>
                            <div class="min-w-0">
                                <span @class([
                                    'inline-flex rounded-full px-2 py-1 text-xs font-medium',
                                    'bg-[#E6EEFB] text-[#1F4FB5]' => $jadwal->jenis === \App\Models\Jadwal::JENIS_PANEN,
                                    'bg-[#E6F6ED] text-[#1FA45B]' => $jadwal->jenis === \App\Models\Jadwal::JENIS_PUPUK,
                                ])>{{ $jadwal->jenis === \App\Models\Jadwal::JENIS_PANEN ? 'Panen' : 'Pemupukan' }}</span>
                                <h2 class="mt-1 truncate text-lg font-semibold text-[var(--text)]">{{ $jadwal->judul }}</h2>
                                <p class="mt-1 text-sm text-[var(--muted)]">{{ $jadwal->kebun?->nama ?? 'Semua kebun' }}</p>
                            </div>
                        </div>
                        <div class="flex shrink-0 gap-2">
                            <a href="{{ route('jadwal.edit', $jadwal) }}" class="button-secondary !min-h-11 !w-11 !p-0" aria-label="Ubah jadwal {{ $jadwal->judul }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m14 5 5 5M4 20l4.5-1 10.7-10.7a2.1 2.1 0 0 0-3-3L5.5 16 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                            </a>
                            <form method="POST" action="{{ route('jadwal.destroy', $jadwal) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-danger !min-h-11 !w-11 !p-0" aria-label="Hapus jadwal {{ $jadwal->judul }}">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-2 border-t border-[var(--border)] pt-3 sm:grid-cols-2">
                        <p class="text-sm text-[var(--muted)]">
                            Pola
                            <strong class="ml-1 font-medium text-[var(--text)]">
                                {{ $jadwal->ulang_setiap ? 'Setiap '.$jadwal->ulang_setiap.' '.$jadwal->ulang_satuan : 'Sekali' }}
                            </strong>
                        </p>
                        <p class="text-sm text-[var(--muted)]">
                            Berikutnya
                            <strong class="ml-1 font-medium text-[var(--text)]">
                                {{ $jadwal->tanggal_berikutnya?->format('d/m/Y') ?? 'Tidak ada jadwal berikutnya' }}
                            </strong>
                        </p>
                    </div>
                    @if ($jadwal->catatan)
                        <p class="mt-3 text-sm text-[var(--muted)]">{{ $jadwal->catatan }}</p>
                    @endif
                </article>
            @endforeach
        </div>
    @endif
@endsection
