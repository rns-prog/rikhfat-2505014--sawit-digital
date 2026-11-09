@extends('layouts.app')

@section('title', 'Kebun')

@section('content')
    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#7a846f]">Data usaha</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-[#263a2f]">Kebun dan blok</h1>
            <p class="mt-2 text-sm text-[#788173]">Kelola lokasi dan luas kebun milik Anda.</p>
        </div>
        <a href="{{ route('kebun.create') }}" class="inline-flex items-center justify-center rounded-full bg-[#315c3d] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#244a30]">
            <span class="mr-2 text-lg leading-none" aria-hidden="true">+</span> Tambah kebun
        </a>
    </div>

    @if (session('success'))
        <div class="mt-6 rounded-2xl border border-[#d5e4d1] bg-[#edf5e9] px-5 py-4 text-sm font-medium text-[#315c3d]" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if ($kebuns->isEmpty())
        <section class="mt-8 rounded-3xl border border-dashed border-[#d8d9cb] bg-[#fffefa] px-6 py-14 text-center">
            <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[#eef2e9] text-2xl text-[#315c3d]" aria-hidden="true">⌂</span>
            <h2 class="mt-5 text-lg font-bold text-[#344839]">Belum ada kebun tercatat</h2>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#788173]">Tambahkan kebun atau blok pertama agar data usaha Anda mulai tersusun.</p>
            <a href="{{ route('kebun.create') }}" class="mt-6 inline-flex rounded-full bg-[#315c3d] px-5 py-3 text-sm font-semibold text-white hover:bg-[#244a30]">Tambah kebun</a>
        </section>
    @else
        <section class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
            @foreach ($kebuns as $kebun)
                <article class="flex flex-col rounded-3xl border border-[#e7e6dc] bg-[#fffefa] p-5 shadow-[0_8px_24px_rgba(47,65,48,0.04)]">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#8a8c7c]">Kebun / blok</p>
                            <h2 class="mt-2 text-xl font-bold text-[#315c3d]">{{ $kebun->nama }}</h2>
                        </div>
                        <span class="rounded-xl bg-[#eef2e9] px-3 py-2 text-sm font-bold text-[#52634f]">{{ number_format((float) $kebun->luas_ha, 2, ',', '.') }} <span class="text-xs font-medium">ha</span></span>
                    </div>
                    <div class="mt-5 border-t border-[#eeede5] pt-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-[#919386]">Lokasi</p>
                        <p class="mt-1 text-sm leading-6 text-[#556253]">{{ $kebun->lokasi }}</p>
                    </div>
                    <div class="mt-auto flex items-center justify-between gap-4 pt-6">
                        <a href="{{ route('kebun.edit', $kebun) }}" class="rounded-full border border-[#d9dfd3] px-4 py-2 text-sm font-semibold text-[#315c3d] transition hover:bg-[#f1f4ec]">Ubah</a>
                        <form method="POST" action="{{ route('kebun.destroy', $kebun) }}" onsubmit="return confirm('Hapus kebun ini beserta semua catatan terkait? Data tidak dapat dikembalikan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="rounded-full px-4 py-2 text-sm font-semibold text-[#9a5643] transition hover:bg-[#f9efea]">Hapus</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </section>

        @if ($kebuns->hasPages())
            <div class="mt-8">{{ $kebuns->links() }}</div>
        @endif
    @endif
@endsection
