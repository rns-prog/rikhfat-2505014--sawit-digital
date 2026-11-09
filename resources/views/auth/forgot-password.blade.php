<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-semibold text-[var(--text)]">Lupa kata sandi?</h1>
        <p class="mt-2 text-[var(--muted)]">Masukkan alamat email, nanti kami kirim tautan untuk membuat kata sandi baru.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div class="mt-5">
            <x-primary-button class="w-full">Kirim tautan reset</x-primary-button>
        </div>
    </form>
</x-guest-layout>
