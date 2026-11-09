<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Sawitku</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen antialiased">
        <main class="app-container flex min-h-screen items-center py-8">
            <section class="page-surface w-full overflow-hidden">
                <div class="auth-panel relative overflow-hidden px-6 py-8 text-white sm:px-10 sm:py-12">
                    <svg class="pointer-events-none absolute -right-4 -top-8 h-48 w-48 opacity-[0.08] sm:h-64 sm:w-64" viewBox="0 0 220 220" fill="none" aria-hidden="true">
                        <path d="M115 220V82m0 55C104 96 75 78 35 82c10 33 37 53 80 55Zm0-24c15-40 45-56 87-49-15 35-41 53-87 49Zm0-51c-6-27-21-48-46-63m46 47c12-25 30-44 56-58" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <a href="/" class="relative z-10 inline-flex items-center gap-2 text-white" aria-label="Sawitku, halaman utama">
                        <svg class="h-9 w-9" viewBox="0 0 40 40" fill="none" aria-hidden="true">
                            <path d="M20 35V18m0 7C17 17 10 13 4 14c2 7 7 11 16 11Zm0-3c3-8 9-12 16-11-2 7-7 11-16 11Zm0-8c-1-5-4-9-9-12m9 9c2-5 5-8 10-11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="font-display text-2xl font-semibold">Sawitku</span>
                    </a>
                    <div class="relative z-10 mt-8 max-w-xl">
                        @auth
                            <h1 class="text-3xl font-semibold sm:text-4xl">Halo, {{ ucwords(Auth::user()->name) }}</h1>
                            <p class="mt-2 text-white/90">Buka catatan kebun Anda.</p>
                        @else
                            <h1 class="text-3xl font-semibold sm:text-4xl">Catat panen dan biaya kebun dengan mudah.</h1>
                        @endauth
                    </div>
                </div>
                <div class="flex flex-col gap-3 p-6 sm:flex-row sm:items-center sm:p-8">
                    @auth
                        <a href="{{ route('dashboard') }}" class="button-primary">Buka dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="button-primary">Masuk</a>
                        <a href="{{ route('register') }}" class="button-secondary">Daftar</a>
                    @endauth
                </div>
            </section>
        </main>
    </body>
</html>
