@extends('layouts.app')

@section('title', 'Catat panen')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('panen.index') }}" class="mb-4 inline-flex min-h-11 items-center font-medium text-[var(--green)] hover:text-[var(--green-hover)]">← Kembali ke catatan panen</a>
        @if ($kebuns->isEmpty())
            <section class="page-surface p-6">
                <h1 class="text-2xl font-semibold text-[var(--text)]">Tambah kebun dulu</h1>
                <p class="mt-2 text-[var(--muted)]">Catatan panen perlu terhubung ke kebun. Tambahkan kebun sebelum mencatat panen.</p>
                <a href="{{ route('kebun.create') }}" class="button-primary mt-5">Tambah kebun</a>
            </section>
        @else
            <section class="page-surface p-5 sm:p-7">
                <h1 class="text-3xl font-semibold text-[var(--text)]">Catat panen</h1>
                <p class="mt-1 text-[var(--muted)]">Masukkan hasil panen dan harga jualnya.</p>
                @include('panen._form')
            </section>
        @endif
    </div>
@endsection
