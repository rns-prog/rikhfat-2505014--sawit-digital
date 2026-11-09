@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <section class="relative overflow-hidden rounded-[2rem] bg-[#315c3d] px-6 py-10 text-white shadow-sm sm:px-10 sm:py-12">
        <div class="relative z-10 max-w-2xl">
            <p class="mb-3 text-sm font-semibold uppercase tracking-[0.18em] text-[#d3dfc5]">Catatan kebun Anda</p>
            <h1 class="text-3xl font-bold tracking-tight sm:text-4xl">Selamat datang, {{ Auth::user()->name }}.</h1>
            <p class="mt-4 max-w-xl text-base leading-7 text-[#e2eadb]">Mulai dengan mencatat kebun atau blok yang Anda kelola. Data tersimpan rapi untuk membantu pencatatan hasil usaha.</p>
            <a href="{{ route('kebun.index') }}" class="mt-7 inline-flex items-center rounded-full bg-[#f1d59a] px-5 py-3 text-sm font-bold text-[#3d4c32] transition hover:bg-[#f8e4b7]">Lihat daftar kebun <span class="ml-2" aria-hidden="true">→</span></a>
        </div>
        <div class="pointer-events-none absolute -right-12 -top-20 h-72 w-72 rounded-full border-[34px] border-white/10 sm:right-6 sm:top-[-8rem]"></div>
        <div class="pointer-events-none absolute -bottom-28 right-24 h-56 w-56 rounded-full border-[26px] border-[#d7b97e]/20"></div>
    </section>

    <section class="mt-6 grid gap-4 sm:grid-cols-2">
        <article class="rounded-3xl border border-[#e7e6dc] bg-[#fffefa] p-6">
            <p class="text-sm font-medium text-[#788173]">Kebun dan blok tercatat</p>
            <p class="mt-3 text-4xl font-bold tracking-tight text-[#315c3d]">{{ $jumlahKebun }}</p>
            <p class="mt-2 text-sm text-[#788173]">Jumlah kebun yang terhubung ke akun Anda.</p>
        </article>
        <article class="flex flex-col justify-between rounded-3xl border border-[#e7e6dc] bg-[#f0eee3] p-6">
            <div>
                <p class="text-sm font-semibold text-[#725b39]">Langkah awal</p>
                <p class="mt-2 text-lg font-bold text-[#3d4c32]">Tambahkan kebun pertama Anda</p>
                <p class="mt-1 text-sm leading-6 text-[#788173]">Isi nama, lokasi, dan luas kebun untuk mulai menyusun catatan.</p>
            </div>
            <a href="{{ route('kebun.create') }}" class="mt-5 inline-flex w-fit items-center rounded-full bg-[#315c3d] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#244a30]">Tambah kebun <span class="ml-2" aria-hidden="true">+</span></a>
        </article>
    </section>
@endsection
