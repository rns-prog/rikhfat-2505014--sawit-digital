@extends('layouts.app')

@section('title', 'Ubah catatan panen')

@section('content')
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('panen.index') }}" class="mb-4 inline-flex min-h-11 items-center font-medium text-[var(--green)] hover:text-[var(--green-hover)]">← Kembali ke catatan panen</a>
        <section class="page-surface p-5 sm:p-7">
            <h1 class="text-3xl font-semibold text-[var(--text)]">Ubah catatan panen</h1>
            <p class="mt-1 text-[var(--muted)]">Perbarui data panen untuk {{ $panen->kebun->nama }}.</p>
            @include('panen._form')
        </section>
    </div>
@endsection
