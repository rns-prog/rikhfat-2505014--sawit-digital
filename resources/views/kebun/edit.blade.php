@extends('layouts.app')

@section('title', 'Ubah Kebun')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('kebun.index') }}" class="text-sm font-semibold text-[#64745d] hover:text-[#315c3d]">← Kembali ke daftar kebun</a>
        <div class="mt-5 rounded-3xl border border-[#e7e6dc] bg-[#fffefa] p-6 shadow-[0_8px_24px_rgba(47,65,48,0.04)] sm:p-8">
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#7a846f]">Kebun / blok</p>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#263a2f]">Ubah data kebun</h1>
            <p class="mt-2 text-sm leading-6 text-[#788173]">Perbarui nama, lokasi, atau luas kebun.</p>
            @include('kebun._form')
        </div>
    </div>
@endsection
