<header class="border-b border-[#e5e6da] bg-[#fffefa]">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-3" aria-label="Catat Kebun, beranda">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[#315c3d] text-lg font-bold text-white">CK</span>
            <span>
                <span class="block text-base font-bold tracking-tight text-[#263a2f]">Catat Kebun</span>
                <span class="hidden text-xs text-[#778173] sm:block">Panen tercatat, usaha terukur</span>
            </span>
        </a>

        <nav class="hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}" @class(['rounded-full px-3 py-2 text-sm font-semibold transition', 'bg-[#eaf0e6] text-[#315c3d]' => request()->routeIs('dashboard'), 'text-[#667064] hover:bg-[#f1f2eb] hover:text-[#315c3d]' => ! request()->routeIs('dashboard')])>Dashboard</a>
            <a href="{{ route('kebun.index') }}" @class(['rounded-full px-3 py-2 text-sm font-semibold transition', 'bg-[#eaf0e6] text-[#315c3d]' => request()->routeIs('kebun.*'), 'text-[#667064] hover:bg-[#f1f2eb] hover:text-[#315c3d]' => ! request()->routeIs('kebun.*')])>Kebun</a>
            <span class="rounded-full px-3 py-2 text-sm font-medium text-[#a3a89d]" aria-disabled="true">Panen <span class="ml-1 text-[10px] uppercase tracking-wide">Segera</span></span>
            <span class="rounded-full px-3 py-2 text-sm font-medium text-[#a3a89d]" aria-disabled="true">Biaya <span class="ml-1 text-[10px] uppercase tracking-wide">Segera</span></span>
            <span class="rounded-full px-3 py-2 text-sm font-medium text-[#a3a89d]" aria-disabled="true">Laporan <span class="ml-1 text-[10px] uppercase tracking-wide">Segera</span></span>
        </nav>

        <div class="hidden items-center gap-3 lg:flex">
            <span class="max-w-36 truncate text-sm font-medium text-[#657064]">{{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="rounded-full border border-[#d9dfd3] px-4 py-2 text-sm font-semibold text-[#52634f] transition hover:border-[#315c3d] hover:bg-[#f4f6f0]">Keluar</button>
            </form>
        </div>

        <details class="relative lg:hidden">
            <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-xl border border-[#dfe3d8] text-[#315c3d] hover:bg-[#f4f6f0]" aria-label="Buka menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </summary>
            <nav class="absolute right-0 z-10 mt-2 w-64 rounded-2xl border border-[#e5e6da] bg-[#fffefa] p-3 shadow-xl" aria-label="Navigasi seluler">
                <a href="{{ route('dashboard') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-[#315c3d] hover:bg-[#eef2e9]">Dashboard</a>
                <a href="{{ route('kebun.index') }}" class="block rounded-xl px-4 py-3 text-sm font-semibold text-[#315c3d] hover:bg-[#eef2e9]">Kebun</a>
                <span class="block px-4 py-3 text-sm text-[#9ba196]" aria-disabled="true">Panen <span class="ml-1 text-xs">· Segera</span></span>
                <span class="block px-4 py-3 text-sm text-[#9ba196]" aria-disabled="true">Biaya <span class="ml-1 text-xs">· Segera</span></span>
                <span class="block px-4 py-3 text-sm text-[#9ba196]" aria-disabled="true">Laporan <span class="ml-1 text-xs">· Segera</span></span>
                <div class="my-2 border-t border-[#e9e9df]"></div>
                <p class="truncate px-4 py-2 text-xs text-[#778173]">{{ Auth::user()->name }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full rounded-xl px-4 py-3 text-left text-sm font-semibold text-[#7d4938] hover:bg-[#f8eee9]">Keluar</button>
                </form>
            </nav>
        </details>
    </div>
</header>
