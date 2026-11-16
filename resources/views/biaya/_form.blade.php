<form
    id="biaya-form"
    method="POST"
    action="{{ $biaya->exists ? route('biaya.update', $biaya) : route('biaya.store') }}"
    data-preview-url="{{ route('biaya.preview') }}"
    class="mt-6 grid gap-5 sm:grid-cols-2"
>
    @csrf
    @if ($biaya->exists)
        @method('PUT')
    @endif

    <div>
        <label for="tanggal" class="block font-medium text-[var(--text)]">Tanggal biaya</label>
        <input id="tanggal" name="tanggal" type="date" value="{{ old('tanggal', $biaya->exists ? $biaya->tanggal->format('Y-m-d') : today()->toDateString()) }}" required max="{{ today()->toDateString() }}" class="form-control mt-2">
        @error('tanggal')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kebun_id" class="block font-medium text-[var(--text)]">Kebun</label>
        <select id="kebun_id" name="kebun_id" required class="form-control mt-2">
            <option value="">Pilih kebun</option>
            @foreach ($kebuns as $kebun)
                <option value="{{ $kebun->id }}" @selected((string) old('kebun_id', $biaya->kebun_id) === (string) $kebun->id)>{{ $kebun->nama }} — {{ $kebun->lokasi }}</option>
            @endforeach
        </select>
        @error('kebun_id')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kategori" class="block font-medium text-[var(--text)]">Kategori</label>
        <select id="kategori" name="kategori" required class="form-control mt-2">
            <option value="">Pilih kategori</option>
            @foreach ($kategoriBiaya as $kategori)
                <option value="{{ $kategori }}" @selected(old('kategori', $biaya->kategori) === $kategori)>{{ $kategori }}</option>
            @endforeach
        </select>
        @error('kategori')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div id="cara-hitung-group">
        <label for="mode_hitung" class="block font-medium text-[var(--text)]">Cara hitung upah</label>
        <select id="mode_hitung" name="mode_hitung" class="form-control mt-2">
            <option value="manual" @selected(old('mode_hitung', $biaya->mode_hitung ?? 'manual') === 'manual')>Manual</option>
            <option value="otomatis" @selected(old('mode_hitung', $biaya->mode_hitung) === 'otomatis')>Otomatis dari berat panen</option>
        </select>
        @error('mode_hitung')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div id="nominal-group">
        <label for="nominal" class="block font-medium text-[var(--text)]">Nominal (Rp)</label>
        <input id="nominal" name="nominal" type="number" value="{{ old('nominal', $biaya->nominal) }}" required min="0.01" step="0.01" inputmode="decimal" class="form-control numeric-data mt-2" placeholder="Contoh: 450000">
        @error('nominal')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
        <p id="nominal-auto-note" class="mt-2 hidden text-sm text-[var(--muted)]">Nominal dihitung otomatis dan tidak dapat diubah.</p>
    </div>

    <div id="upah-otomatis-fields" class="hidden rounded-xl border border-[var(--border)] bg-[#f7f9fd] p-4 sm:col-span-2">
        <h2 class="font-semibold text-[var(--text)]">Hitung upah dari panen</h2>
        <p class="mt-1 text-sm text-[var(--muted)]">Berat diambil dari catatan panen kebun ini dalam rentang tanggal.</p>
        <div class="mt-4 grid gap-4 sm:grid-cols-3">
            <div>
                <label for="tarif_per_kg" class="block font-medium text-[var(--text)]">Tarif per kg (Rp)</label>
                <input id="tarif_per_kg" name="tarif_per_kg" type="number" value="{{ old('tarif_per_kg', $biaya->tarif_per_kg) }}" min="0.01" step="0.01" inputmode="decimal" class="form-control numeric-data mt-2" placeholder="Contoh: 200">
                @error('tarif_per_kg')
                    <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="tanggal_mulai" class="block font-medium text-[var(--text)]">Tanggal mulai</label>
                <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai', $biaya->tanggal_mulai?->format('Y-m-d')) }}" class="form-control mt-2">
                @error('tanggal_mulai')
                    <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="tanggal_selesai" class="block font-medium text-[var(--text)]">Tanggal selesai</label>
                <input id="tanggal_selesai" name="tanggal_selesai" type="date" value="{{ old('tanggal_selesai', $biaya->tanggal_selesai?->format('Y-m-d')) }}" class="form-control mt-2">
                @error('tanggal_selesai')
                    <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <div class="mt-4 rounded-lg border border-[var(--border)] bg-white px-4 py-3" aria-live="polite">
            <p class="text-sm font-medium text-[var(--muted)]">Perkiraan upah</p>
            <p id="upah-preview-detail" class="numeric-data mt-1 font-semibold text-[var(--text)]">Pilih kebun, tarif, dan rentang tanggal untuk melihat perhitungan.</p>
            <p id="upah-preview-message" class="mt-1 text-sm text-[var(--muted)]"></p>
        </div>
    </div>

    <div class="sm:col-span-2">
        <label for="keterangan" class="block font-medium text-[var(--text)]">Keterangan <span class="font-normal text-[var(--muted)]">(opsional)</span></label>
        <textarea id="keterangan" name="keterangan" rows="3" maxlength="255" class="form-control mt-2" placeholder="Contoh: Pembelian pupuk NPK">{{ old('keterangan', $biaya->keterangan) }}</textarea>
        @error('keterangan')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-[var(--border)] pt-5 sm:col-span-2 sm:flex-row sm:justify-end">
        <a href="{{ route('biaya.index') }}" class="button-secondary">Batal</a>
        <button id="biaya-submit" type="submit" class="button-primary">{{ $biaya->exists ? 'Simpan perubahan' : 'Simpan catatan biaya' }}</button>
    </div>
</form>
