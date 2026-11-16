<form id="panen-form" method="POST" action="{{ $panen->exists ? route('panen.update', $panen) : route('panen.store') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
    @csrf
    @if ($panen->exists)
        @method('PUT')
    @endif

    <div>
        <label for="tanggal" class="block font-medium text-[var(--text)]">Tanggal panen</label>
        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $panen->exists ? $panen->tanggal->format('Y-m-d') : today()->toDateString()) }}" required max="{{ today()->toDateString() }}" class="form-control mt-2">
        @error('tanggal')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kebun_id" class="block font-medium text-[var(--text)]">Kebun</label>
        <select id="kebun_id" name="kebun_id" required class="form-control mt-2">
            <option value="">Pilih kebun</option>
            @foreach ($kebuns as $kebun)
                <option value="{{ $kebun->id }}" @selected((string) old('kebun_id', $panen->kebun_id) === (string) $kebun->id)>
                    {{ $kebun->nama }} — {{ $kebun->lokasi }}
                </option>
            @endforeach
        </select>
        @error('kebun_id')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="jumlah_tandan" class="block font-medium text-[var(--text)]">Jumlah tandan</label>
        <input id="jumlah_tandan" name="jumlah_tandan" type="number" value="{{ old('jumlah_tandan', $panen->jumlah_tandan) }}" required min="0" step="1" inputmode="numeric" class="form-control numeric-data mt-2" placeholder="Contoh: 85">
        @error('jumlah_tandan')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="berat_kg" class="block font-medium text-[var(--text)]">Berat panen (kg)</label>
        <input id="berat_kg" name="berat_kg" type="number" value="{{ old('berat_kg', $panen->berat_kg) }}" required min="0.01" step="0.01" inputmode="decimal" class="form-control numeric-data mt-2" placeholder="Contoh: 1280">
        @error('berat_kg')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="harga_per_kg" class="block font-medium text-[var(--text)]">Harga per kg (Rp)</label>
        <input id="harga_per_kg" name="harga_per_kg" type="number" value="{{ old('harga_per_kg', $panen->harga_per_kg) }}" required min="0.01" step="0.01" inputmode="decimal" class="form-control numeric-data mt-2" placeholder="Contoh: 2850">
        @error('harga_per_kg')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="pembeli" class="block font-medium text-[var(--text)]">Pembeli <span class="font-normal text-[var(--muted)]">(opsional)</span></label>
        <input id="pembeli" name="pembeli" type="text" value="{{ old('pembeli', $panen->pembeli) }}" maxlength="100" class="form-control mt-2" placeholder="Nama pembeli">
        @error('pembeli')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-lg border border-[var(--border)] bg-[#f7f9fd] px-4 py-3 sm:col-span-2">
        <p class="text-sm font-medium text-[var(--muted)]">Perkiraan pendapatan</p>
        <p id="pendapatan-preview" class="numeric-data mt-1 text-2xl font-semibold text-[var(--text)]" aria-live="polite">Rp 0</p>
        <p class="mt-1 text-xs text-[var(--muted)]">Berat panen dikali harga per kg</p>
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-[var(--border)] pt-5 sm:col-span-2 sm:flex-row sm:justify-end">
        <a href="{{ route('panen.index') }}" class="button-secondary">Batal</a>
        <button type="submit" class="button-primary">{{ $panen->exists ? 'Simpan perubahan' : 'Simpan catatan panen' }}</button>
    </div>
</form>
