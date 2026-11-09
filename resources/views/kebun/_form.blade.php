<form method="POST" action="{{ $kebun->exists ? route('kebun.update', $kebun) : route('kebun.store') }}" class="mt-6 space-y-5">
    @csrf
    @if ($kebun->exists)
        @method('PUT')
    @endif

    <div>
        <label for="nama" class="block font-medium text-[var(--text)]">Nama kebun / blok</label>
        <input id="nama" name="nama" type="text" value="{{ old('nama', $kebun->nama) }}" required maxlength="255" autofocus class="form-control mt-2" placeholder="Contoh: Kebun Utara">
        @error('nama')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="lokasi" class="block font-medium text-[var(--text)]">Lokasi</label>
        <input id="lokasi" name="lokasi" type="text" value="{{ old('lokasi', $kebun->lokasi) }}" required maxlength="255" class="form-control mt-2" placeholder="Nama desa atau lokasi kebun">
        @error('lokasi')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="luas_ha" class="block font-medium text-[var(--text)]">Luas kebun (hektare)</label>
        <div class="relative mt-2">
            <input id="luas_ha" name="luas_ha" type="number" value="{{ old('luas_ha', $kebun->luas_ha) }}" required min="0.01" max="99999999.99" step="0.01" class="form-control numeric-data pr-14" placeholder="0.00">
            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-[var(--muted)]">ha</span>
        </div>
        @error('luas_ha')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-[var(--border)] pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('kebun.index') }}" class="button-secondary">Batal</a>
        <button type="submit" class="button-primary">{{ $kebun->exists ? 'Simpan perubahan' : 'Simpan kebun' }}</button>
    </div>
</form>
