<form method="POST" action="{{ $kebun->exists ? route('kebun.update', $kebun) : route('kebun.store') }}" class="mt-7 space-y-5">
    @csrf
    @if ($kebun->exists)
        @method('PUT')
    @endif

    <div>
        <label for="nama" class="block text-sm font-semibold text-[#405243]">Nama kebun / blok</label>
        <input id="nama" name="nama" type="text" value="{{ old('nama', $kebun->nama) }}" required maxlength="255" autofocus class="mt-2 block w-full rounded-xl border-[#dfe2d7] bg-[#fffefa] px-4 py-3 text-sm text-[#263a2f] shadow-sm placeholder:text-[#a3a89d] focus:border-[#527652] focus:ring-[#527652]" placeholder="Contoh: Kebun Utara">
        @error('nama')
            <p class="mt-2 text-sm text-[#a54c3a]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="lokasi" class="block text-sm font-semibold text-[#405243]">Lokasi</label>
        <input id="lokasi" name="lokasi" type="text" value="{{ old('lokasi', $kebun->lokasi) }}" required maxlength="255" class="mt-2 block w-full rounded-xl border-[#dfe2d7] bg-[#fffefa] px-4 py-3 text-sm text-[#263a2f] shadow-sm placeholder:text-[#a3a89d] focus:border-[#527652] focus:ring-[#527652]" placeholder="Desa, kecamatan, atau patokan lokasi">
        @error('lokasi')
            <p class="mt-2 text-sm text-[#a54c3a]">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="luas_ha" class="block text-sm font-semibold text-[#405243]">Luas kebun (hektare)</label>
        <div class="relative mt-2">
            <input id="luas_ha" name="luas_ha" type="number" value="{{ old('luas_ha', $kebun->luas_ha) }}" required min="0.01" max="99999999.99" step="0.01" class="block w-full rounded-xl border-[#dfe2d7] bg-[#fffefa] px-4 py-3 pr-16 text-sm text-[#263a2f] shadow-sm placeholder:text-[#a3a89d] focus:border-[#527652] focus:ring-[#527652]" placeholder="0,00">
            <span class="pointer-events-none absolute inset-y-0 right-4 flex items-center text-sm text-[#8a9083]">ha</span>
        </div>
        @error('luas_ha')
            <p class="mt-2 text-sm text-[#a54c3a]">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-col-reverse gap-3 border-t border-[#eeede5] pt-5 sm:flex-row sm:justify-end">
        <a href="{{ route('kebun.index') }}" class="inline-flex items-center justify-center rounded-full border border-[#d9dfd3] px-5 py-3 text-sm font-semibold text-[#64745d] hover:bg-[#f4f6f0]">Batal</a>
        <button type="submit" class="inline-flex items-center justify-center rounded-full bg-[#315c3d] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#244a30]">{{ $kebun->exists ? 'Simpan perubahan' : 'Simpan kebun' }}</button>
    </div>
</form>
