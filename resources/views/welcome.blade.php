<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Catat Kebun · Pencatatan hasil kebun</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f7f6f0] font-sans text-[#263a2f] antialiased">
        <main class="mx-auto flex min-h-screen max-w-7xl flex-col px-4 py-6 sm:px-6 lg:px-8">
            <header class="flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#315c3d] text-lg font-bold text-white">CK</span>
                    <span class="text-lg font-bold tracking-tight">Catat Kebun</span>
                </a>
                <div class="flex items-center gap-2 sm:gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="rounded-full bg-[#315c3d] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#244a30]">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-full px-4 py-2.5 text-sm font-semibold text-[#52634f] hover:bg-[#eaf0e6]">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-full bg-[#315c3d] px-4 py-2.5 text-sm font-bold text-white hover:bg-[#244a30]">Daftar</a>
                    @endauth
                </div>
            </header>

            <section class="grid flex-1 items-center gap-12 py-14 md:grid-cols-[1.1fr_0.9fr] md:py-20">
                <div class="max-w-2xl">
                    <p class="inline-flex rounded-full border border-[#d9dfd3] bg-[#fffefa] px-4 py-2 text-xs font-bold uppercase tracking-[0.16em] text-[#64745d]">Catatan usaha kebun</p>
                    <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight text-[#263a2f] sm:text-5xl">Kelola kebun dengan <span class="text-[#54774d]">lebih teratur.</span></h1>
                    <p class="mt-5 max-w-xl text-base leading-7 text-[#697365]">Mulai dari data kebun dan blok yang Anda kelola. Sederhana, mudah diakses, dan membantu catatan tetap rapi.</p>
                    @guest
                        <div class="mt-8 flex flex-wrap gap-3">
                            <a href="{{ route('register') }}" class="rounded-full bg-[#315c3d] px-6 py-3 text-sm font-bold text-white transition hover:bg-[#244a30]">Buat akun</a>
                            <a href="{{ route('login') }}" class="rounded-full border border-[#d9dfd3] bg-[#fffefa] px-6 py-3 text-sm font-semibold text-[#52634f] transition hover:bg-[#f1f4ec]">Saya sudah punya akun</a>
                        </div>
                    @endguest
                </div>
                <div class="relative mx-auto w-full max-w-md">
                    <div class="absolute -left-5 -top-5 h-24 w-24 rounded-full bg-[#e6d9ba]"></div>
                    <div class="absolute -bottom-5 -right-4 h-32 w-32 rounded-full bg-[#dfe8d7]"></div>
                    <div class="relative rounded-[2rem] border border-[#e7e6dc] bg-[#fffefa] p-6 shadow-[0_24px_70px_rgba(47,65,48,0.08)] sm:p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[#89907f]">Mulai dari sini</p>
                                <h2 class="mt-2 text-xl font-bold text-[#315c3d]">Data kebun Anda</h2>
                            </div>
                            <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#eef2e9] text-xl text-[#315c3d]" aria-hidden="true">⌂</span>
                        </div>
                        <div class="mt-7 space-y-3">
                            <div class="rounded-2xl bg-[#f4f3ec] p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-[#919386]">Nama kebun</p>
                                <p class="mt-1 font-semibold text-[#405243]">Kebun atau blok pertama</p>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-2xl bg-[#f4f3ec] p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-[#919386]">Lokasi</p>
                                    <p class="mt-1 text-sm font-semibold text-[#405243]">Area kebun</p>
                                </div>
                                <div class="rounded-2xl bg-[#f4f3ec] p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-[#919386]">Luas</p>
                                    <p class="mt-1 text-sm font-semibold text-[#405243]">Dalam hektare</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex items-center gap-3 rounded-2xl bg-[#315c3d] p-4 text-white">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-white/15" aria-hidden="true">✓</span>
                            <p class="text-sm font-medium">Catatan tersimpan untuk akun Anda.</p>
                        </div>
                    </div>
                </div>
            </section>
            <footer class="border-t border-[#e5e6da] py-5 text-xs text-[#89907f]">Pencatatan hasil kebun, lebih dekat dan mudah.</footer>
        </main>
    </body>
</html>
