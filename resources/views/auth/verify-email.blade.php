<x-guest-layout>
    <div class="mb-4">
        <h1 class="text-3xl font-semibold text-[var(--text)]">Verifikasi email</h1>
        <p class="mt-2 text-[var(--muted)]">Sebelum melanjutkan, buka tautan verifikasi yang kami kirim ke email Anda. Jika belum menerima email, kirim ulang tautannya.</p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-lg border border-[#b8d8c0] bg-[var(--green-soft)] px-4 py-3 font-medium text-[var(--green)]" role="status">
            Tautan verifikasi baru sudah dikirim ke alamat email Anda.
        </div>
    @endif

    <div class="mt-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <x-primary-button class="w-full sm:w-auto">Kirim ulang tautan</x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="min-h-11 rounded-lg px-4 py-2 font-medium text-[var(--muted)] hover:bg-[var(--green-soft)] hover:text-[var(--green)]">Keluar</button>
        </form>
    </div>
</x-guest-layout>
