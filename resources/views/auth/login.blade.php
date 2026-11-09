<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-semibold text-[var(--text)]">Masuk</h1>
        <p class="mt-1 text-[var(--muted)]">Masukkan email dan kata sandi.</p>
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

        <label for="remember" class="mb-6 inline-flex min-h-11 items-center gap-2 text-[var(--muted)]">
            <input id="remember" type="checkbox" name="remember" class="rounded border-[var(--border)] text-[var(--green)] focus:ring-[var(--green)]">
            Ingat saya
        </label>

        <div class="flex flex-col gap-4">
            <x-primary-button class="w-full justify-center">Masuk</x-primary-button>
            <p class="text-center text-[var(--muted)]">Belum punya akun? <a href="{{ route('register') }}" class="font-semibold text-[var(--green)] hover:text-[var(--green-hover)]">Daftar</a></p>
        </div>
    </form>
</x-guest-layout>
