@extends('layouts.app')

@section('title', 'Tambah kebun')

@section('content')
    <div class="mx-auto max-w-2xl">
        <a href="{{ route('kebun.index') }}" class="mb-4 inline-flex min-h-11 items-center font-medium text-[var(--green)] hover:text-[var(--green-hover)]">← Kembali ke daftar kebun</a>
        <section class="page-surface p-5 sm:p-7">
            <h1 class="text-3xl font-semibold text-[var(--text)]">Tambah kebun</h1>
            <p class="mt-1 text-[var(--muted)]">Isi nama, lokasi, dan luas kebun.</p>
            @include('kebun._form')
        </section>
    </div>
@endsection
