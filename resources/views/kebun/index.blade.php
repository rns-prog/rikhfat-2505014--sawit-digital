@extends('layouts.app')

@section('title', 'Kebun')

@section('content')
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-3xl font-semibold text-[var(--text)]">Kebun dan blok</h1>
            <p class="mt-1 text-[var(--muted)]">Daftar kebun yang Bapak kelola.</p>
        </div>
        <a href="{{ route('kebun.create') }}" class="button-primary">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            Tambah kebun
        </a>
    </div>

    @if (session('success'))
        <div class="mt-5 rounded-xl border border-[#b8d8c0] bg-[var(--green-soft)] px-4 py-3 font-medium text-[var(--green)]" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($kebuns->isEmpty())
        <section class="page-surface mt-6 flex flex-col items-center gap-4 px-5 py-8 text-center sm:flex-row sm:text-left">
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
            <p class="flex-1 font-medium text-[var(--text)]">Belum ada kebun. Mulai dengan menambah kebun.</p>
            <a href="{{ route('kebun.create') }}" class="button-primary">Tambah kebun</a>
        </section>
    @else
        <div class="page-surface mt-6 overflow-hidden">
            <table class="hidden w-full text-left md:table">
                <thead class="bg-[#f4f7f4] text-[var(--muted)]">
                    <tr class="text-sm font-medium">
                        <th scope="col" class="px-5 py-3">Nama kebun / blok</th>
                        <th scope="col" class="px-5 py-3">Lokasi</th>
                        <th scope="col" class="numeric-data px-5 py-3 text-right">Luas</th>
                        <th scope="col" class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border)]">
                    @foreach ($kebuns as $kebun)
                        <tr class="text-[var(--text)]">
                            <th scope="row" class="px-5 py-4 text-left font-medium">{{ $kebun->nama }}</th>
                            <td class="px-5 py-4 text-[var(--muted)]">{{ $kebun->lokasi }}</td>
                            <td class="numeric-data px-5 py-4 text-right">{{ number_format((float) $kebun->luas_ha, 2, ',', '.') }} ha</td>
                            <td class="px-5 py-3">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('kebun.edit', $kebun) }}" class="button-secondary !min-h-11 !w-11 !p-0" aria-label="Ubah kebun {{ $kebun->nama }}">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="m14 5 5 5M4 20l4.5-1 10.7-10.7a2.1 2.1 0 0 0-3-3L5.5 16 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('kebun.destroy', $kebun) }}" onsubmit="return confirm('Hapus kebun ini beserta semua catatan terkait? Data tidak dapat dikembalikan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="button-danger !min-h-11 !w-11 !p-0" aria-label="Hapus kebun {{ $kebun->nama }}">
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

            <div class="divide-y divide-[var(--border)] md:hidden">
                @foreach ($kebuns as $kebun)
                    <article class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <h2 class="font-semibold text-[var(--text)]">{{ $kebun->nama }}</h2>
                            <p class="numeric-data shrink-0 text-right font-semibold text-[var(--green)]">{{ number_format((float) $kebun->luas_ha, 2, ',', '.') }} ha</p>
                        </div>
                        <p class="mt-1 text-[var(--muted)]">{{ $kebun->lokasi }}</p>
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('kebun.edit', $kebun) }}" class="button-secondary flex-1">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="m14 5 5 5M4 20l4.5-1 10.7-10.7a2.1 2.1 0 0 0-3-3L5.5 16 4 20Z" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Ubah
                            </a>
                            <form method="POST" action="{{ route('kebun.destroy', $kebun) }}" class="flex-1" onsubmit="return confirm('Hapus kebun ini beserta semua catatan terkait? Data tidak dapat dikembalikan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="button-danger w-full">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 7h16M9 7V4h6v3m3 0-1 13H7L6 7m4 4v5m4-5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        @if ($kebuns->hasPages())
            <div class="mt-5">{{ $kebuns->links() }}</div>
        @endif
    @endif
@endsection
