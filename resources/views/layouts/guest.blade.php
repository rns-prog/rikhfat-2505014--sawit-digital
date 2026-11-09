<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', 'Akun') · Catat Kebun</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f7f6f0] font-sans text-[#263a2f] antialiased">
        <main class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-10 sm:px-6">
            <div class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] border border-[#e7e6dc] bg-[#fffefa] shadow-[0_24px_70px_rgba(47,65,48,0.08)] md:grid-cols-2">
                <section class="relative hidden min-h-[34rem] flex-col justify-between overflow-hidden bg-[#315c3d] p-10 text-white md:flex">
                    <a href="/" class="relative z-10 flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 text-lg font-bold">CK</span>
                        <span class="text-lg font-bold tracking-tight">Catat Kebun</span>
                    </a>
                    <div class="relative z-10 max-w-sm">
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#d3dfc5]">Tumbuh dengan catatan</p>
                        <p class="mt-4 text-3xl font-bold leading-tight">Kelola kebun dengan lebih teratur.</p>
                        <p class="mt-4 text-sm leading-7 text-[#e2eadb]">Satu tempat sederhana untuk menyimpan catatan usaha kebun Anda.</p>
                    </div>
                    <p class="relative z-10 text-xs text-[#d3dfc5]">Pencatatan hasil kebun, lebih dekat dan mudah.</p>
                    <div class="pointer-events-none absolute -bottom-16 -right-16 h-64 w-64 rounded-full border-[30px] border-white/10"></div>
                    <div class="pointer-events-none absolute -right-10 top-12 h-36 w-36 rounded-full border-[20px] border-[#d7b97e]/20"></div>
                </section>
                <section class="flex flex-col justify-center p-6 sm:p-10">
                    <a href="/" class="mb-8 flex items-center gap-3 md:hidden">
                        <span class="flex h-10 w-10 items-center justify-center rounded-2xl bg-[#315c3d] text-sm font-bold text-white">CK</span>
                        <span class="font-bold text-[#315c3d]">Catat Kebun</span>
                    </a>
                    {{ $slot }}
                </section>
            </div>
        </main>
    </body>
</html>
