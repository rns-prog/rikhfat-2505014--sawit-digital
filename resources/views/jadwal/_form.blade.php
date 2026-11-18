<form id="jadwal-form" method="POST" action="{{ $jadwal->exists ? route('jadwal.update', $jadwal) : route('jadwal.store') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
    @csrf
    @if ($jadwal->exists)
        @method('PUT')
    @endif

    <div>
        <label for="jenis" class="block font-medium text-[var(--text)]">Jenis jadwal</label>
        <select id="jenis" name="jenis" required class="form-control mt-2">
            <option value="panen" @selected(old('jenis', $jadwal->jenis) === 'panen')>Panen</option>
            <option value="pupuk" @selected(old('jenis', $jadwal->jenis) === 'pupuk')>Pemupukan</option>
        </select>
        @error('jenis')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="judul" class="block font-medium text-[var(--text)]">Judul</label>
        <input id="judul" name="judul" type="text" value="{{ old('judul', $jadwal->judul) }}" required maxlength="100" class="form-control mt-2" placeholder="Contoh: Panen Blok A">
        @error('judul')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="kebun_id" class="block font-medium text-[var(--text)]">Kebun <span class="font-normal text-[var(--muted)]">(opsional)</span></label>
        <select id="kebun_id" name="kebun_id" class="form-control mt-2">
            <option value="">Semua kebun</option>
            @foreach ($kebuns as $kebun)
                <option value="{{ $kebun->id }}" @selected((string) old('kebun_id', $jadwal->kebun_id) === (string) $kebun->id)>{{ $kebun->nama }} — {{ $kebun->lokasi }}</option>
            @endforeach
        </select>
        @error('kebun_id')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="tanggal_mulai" class="block font-medium text-[var(--text)]">Tanggal mulai</label>
        <input id="tanggal_mulai" name="tanggal_mulai" type="date" value="{{ old('tanggal_mulai', $jadwal->exists ? $jadwal->tanggal_mulai->format('Y-m-d') : today()->toDateString()) }}" required class="form-control mt-2">
        @error('tanggal_mulai')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="ulang_setiap" class="block font-medium text-[var(--text)]">Ulangi setiap <span class="font-normal text-[var(--muted)]">(opsional)</span></label>
        <div class="mt-2 grid grid-cols-[1fr_1fr] gap-2">
            <input id="ulang_setiap" name="ulang_setiap" type="number" value="{{ old('ulang_setiap', $jadwal->ulang_setiap) }}" min="1" step="1" inputmode="numeric" class="form-control" placeholder="2">
            <select id="ulang_satuan" name="ulang_satuan" class="form-control">
                <option value="">Tidak berulang</option>
                <option value="minggu" @selected(old('ulang_satuan', $jadwal->ulang_satuan) === 'minggu')>Minggu</option>
                <option value="bulan" @selected(old('ulang_satuan', $jadwal->ulang_satuan) === 'bulan')>Bulan</option>
            </select>
        </div>
        @error('ulang_setiap')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
        @error('ulang_satuan')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
        <p class="mt-2 text-sm text-[var(--muted)]">Contoh: panen setiap 2 minggu, dimulai dari Senin ini.</p>
    </div>

    <div>
        <label for="tanggal_berakhir" class="block font-medium text-[var(--text)]">Tanggal berakhir <span class="font-normal text-[var(--muted)]">(opsional)</span></label>
        <input id="tanggal_berakhir" name="tanggal_berakhir" type="date" value="{{ old('tanggal_berakhir', $jadwal->tanggal_berakhir?->format('Y-m-d')) }}" class="form-control mt-2">
        @error('tanggal_berakhir')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="catatan" class="block font-medium text-[var(--text)]">Catatan <span class="font-normal text-[var(--muted)]">(opsional)</span></label>
        <textarea id="catatan" name="catatan" rows="3" maxlength="255" class="form-control mt-2" placeholder="Catatan tambahan">{{ old('catatan', $jadwal->catatan) }}</textarea>
        @error('catatan')
            <p class="mt-2 text-sm font-medium text-[var(--danger)]" role="alert">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-[var(--border)] pt-5 sm:col-span-2 sm:flex-row sm:justify-end">
        <a href="{{ route('jadwal.index') }}" class="button-secondary">Batal</a>
        <button type="submit" class="button-primary">{{ $jadwal->exists ? 'Simpan perubahan' : 'Simpan jadwal' }}</button>
    </div>
</form>
