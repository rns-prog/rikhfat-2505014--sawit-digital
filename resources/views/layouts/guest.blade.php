<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', request()->routeIs('login') ? 'Masuk' : (request()->routeIs('register') ? 'Daftar' : 'Akun')) · Sawitku</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen antialiased">
        <x-latar-kebun />
        <main class="relative z-10 mx-auto flex min-h-screen max-w-6xl items-center px-4 py-6 sm:px-6 lg:px-8">
            <div class="grid w-full overflow-hidden rounded-2xl md:grid-cols-2">
                <section class="auth-panel relative hidden min-h-[38rem] flex-col justify-between overflow-hidden p-8 text-white md:flex lg:p-12">
                    <a href="/" class="relative z-10 inline-flex w-fit items-center gap-2 text-white" aria-label="Sawitku, halaman utama">
                        <svg class="h-10 w-10" viewBox="0 0 40 40" fill="none" aria-hidden="true">
                            <path d="M20 35V18m0 7C17 17 10 13 4 14c2 7 7 11 16 11Zm0-3c3-8 9-12 16-11-2 7-7 11-16 11Zm0-8c-1-5-4-9-9-12m9 9c2-5 5-8 10-11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="font-display text-3xl font-semibold">Sawitku</span>
                    </a>
                    <div class="relative z-10 max-w-sm">
                        <p class="text-3xl font-semibold leading-snug lg:text-4xl">Catat panen dan biaya kebun dengan mudah.</p>
                    </div>
                    <svg class="auth-palm" viewBox="0 0 300 400" fill="none" aria-hidden="true">
                        <path d="M142 405c20-98 17-182 1-264" stroke="white" stroke-width="12" stroke-linecap="round"/>
                        <path d="M145 153C103 108 70 95 35 104c28 29 60 46 105 53C93 121 72 88 71 52c35 22 60 53 76 103-5-61 7-102 36-135 15 43 9 83-24 139 40-53 78-77 121-78-19 39-50 67-118 91 61-16 103-10 133 14-40 17-85 20-142 1 49 24 75 53 84 89-43-13-77-45-100-94Z" fill="white" fill-opacity=".12" stroke="white" stroke-width="8" stroke-linejoin="round"/>
                        <g fill="white" fill-opacity=".8">
                            <circle cx="144" cy="173" r="10"/>
                            <circle cx="160" cy="184" r="10"/>
                            <circle cx="143" cy="195" r="10"/>
                            <circle cx="160" cy="205" r="10"/>
                            <circle cx="142" cy="216" r="10"/>
                        </g>
                    </svg>
                </section>
                <section class="page-surface flex flex-col justify-center p-5 sm:p-8 md:rounded-l-none md:p-10">
                    <a href="/" class="mb-6 inline-flex w-fit items-center gap-2 text-[var(--green)] md:hidden" aria-label="Sawitku, halaman utama">
                        <svg class="h-8 w-8" viewBox="0 0 40 40" fill="none" aria-hidden="true">
                            <path d="M20 35V18m0 7C17 17 10 13 4 14c2 7 7 11 16 11Zm0-3c3-8 9-12 16-11-2 7-7 11-16 11Zm0-8c-1-5-4-9-9-12m9 9c2-5 5-8 10-11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="font-display text-2xl font-semibold">Sawitku</span>
                    </a>
                    {{ $slot }}
                </section>
            </div>
        </main>
    </body>
</html>
