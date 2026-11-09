<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Beranda') · Sawitku</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen antialiased">
        <x-latar-kebun />
        @include('layouts.navigation')

        <main class="app-container relative z-10 py-6 sm:py-8">
            @yield('content')
        </main>
    </body>
</html>
