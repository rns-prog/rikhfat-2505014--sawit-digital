@extends('layouts.app')

@section('title', 'Ubah catatan biaya')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('biaya.index') }}" class="mb-4 inline-flex min-h-11 items-center font-medium text-[var(--green)] hover:text-[var(--green-hover)]">← Kembali ke catatan biaya</a>
        <section class="page-surface p-5 sm:p-7">
            <h1 class="text-3xl font-semibold text-[var(--text)]">Ubah catatan biaya</h1>
            <p class="mt-1 text-[var(--muted)]">Perbarui biaya untuk {{ $biaya->kebun->nama }}.</p>
            @include('biaya._form')
        </section>
    </div>
@endsection
