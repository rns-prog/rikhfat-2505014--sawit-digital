<x-guest-layout>
    <div class="mb-7">
        <p class="text-sm font-semibold uppercase tracking-[0.16em] text-[#7a846f]">Mulai mencatat</p>
        <h1 class="mt-2 text-2xl font-bold tracking-tight text-[#263a2f]">Buat akun</h1>
        <p class="mt-2 text-sm text-[#788173]">Daftarkan akun untuk mengelola data kebun Anda.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-4">
            <x-input-label for="name" value="Nama lengkap" />
            <x-text-input id="name" class="mt-2 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="email" value="Alamat email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mb-4">
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mb-6">
            <x-input-label for="password_confirmation" value="Ulangi kata sandi" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-4">
            <x-primary-button class="w-full justify-center">Daftar</x-primary-button>
            <p class="text-center text-sm text-[#788173]">Sudah punya akun? <a href="{{ route('login') }}" class="font-semibold text-[#315c3d] hover:underline">Masuk</a></p>
        </div>
    </form>
</x-guest-layout>
