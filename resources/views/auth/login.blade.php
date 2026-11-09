<x-guest-layout>
    <div class="mb-7">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#7a846f]">Selamat datang kembali</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#263a2f]">Masuk ke akun Anda</h1>
        <p class="mt-2 text-sm text-[#788173]">Lanjutkan mencatat dan mengelola kebun.</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-5">
            <x-input-label for="email" value="Alamat email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-5">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <label for="remember" class="mb-6 inline-flex items-center gap-2 text-sm text-[#667064]">
            <input id="remember" type="checkbox" name="remember" class="rounded border-[#d5dbd0] text-[#315c3d] shadow-sm focus:ring-[#527652]">
            Ingat saya
        </label>

        <div class="flex flex-col gap-4">
            <x-primary-button class="w-full justify-center">Masuk</x-primary-button>
            <p class="text-center text-sm text-[#788173]">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-[#315c3d] hover:underline">Daftar</a></p>
        </div>
    </form>
</x-guest-layout>
