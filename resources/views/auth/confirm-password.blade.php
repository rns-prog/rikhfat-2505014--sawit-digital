<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-3xl font-semibold text-[var(--text)]">Konfirmasi kata sandi</h1>
        <p class="mt-2 text-[var(--muted)]">Masukkan kata sandi Anda untuk melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf
        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div class="mt-5">
            <x-primary-button class="w-full">Konfirmasi</x-primary-button>
        </div>
    </form>
</x-guest-layout>
