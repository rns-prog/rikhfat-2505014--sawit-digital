<header class="relative z-20 border-b border-[var(--border)] bg-[var(--surface)] shadow-[0_2px_12px_rgba(31,42,36,0.05)]">
    <div class="app-container flex min-h-16 items-center justify-between gap-3">
        <a href="{{ route('dashboard') }}" class="flex shrink-0 items-center gap-2 text-[var(--green)]" aria-label="Sawitku, beranda">
            <svg class="h-8 w-8" viewBox="0 0 40 40" fill="none" aria-hidden="true">
                <path d="M20 35V18m0 7C17 17 10 13 4 14c2 7 7 11 16 11Zm0-3c3-8 9-12 16-11-2 7-7 11-16 11Zm0-8c-1-5-4-9-9-12m9 9c2-5 5-8 10-11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <circle cx="20" cy="27" r="1.5" fill="var(--green)"/>
            </svg>
            <span class="font-display text-xl font-semibold">Sawitku</span>
        </a>

        <nav class="hidden items-center gap-1 md:flex" aria-label="Navigasi utama">
            <a href="{{ route('dashboard') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('dashboard')])>Dashboard</a>
            <a href="{{ route('kebun.index') }}" @class(['nav-link', 'nav-link-active' => request()->routeIs('kebun.*')])>Kebun</a>
            <span class="menu-disabled" aria-disabled="true">Panen</span>
            <span class="menu-disabled" aria-disabled="true">Biaya</span>
            <span class="menu-disabled" aria-disabled="true">Laporan</span>
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            <span class="hidden max-w-40 truncate text-[var(--muted)] lg:inline">{{ ucwords(Auth::user()->name) }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="button-secondary">Keluar</button>
            </form>
        </div>

        <details class="relative md:hidden">
            <summary class="flex h-11 w-11 cursor-pointer list-none items-center justify-center rounded-lg border border-[var(--border)] text-[var(--green)] hover:bg-[var(--green-soft)]" aria-label="Buka menu">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                    <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
                </svg>
            </summary>
            <nav class="absolute right-0 mt-2 w-64 rounded-xl border border-[var(--border)] bg-[var(--surface)] p-2 shadow-lg" aria-label="Navigasi seluler">
                <a href="{{ route('dashboard') }}" @class(['block min-h-11 rounded-lg px-4 py-2.5', 'bg-[var(--green-soft)] font-medium text-[var(--green)]' => request()->routeIs('dashboard'), 'text-[var(--text)]' => ! request()->routeIs('dashboard')])>Dashboard</a>
                <a href="{{ route('kebun.index') }}" @class(['block min-h-11 rounded-lg px-4 py-2.5', 'bg-[var(--green-soft)] font-medium text-[var(--green)]' => request()->routeIs('kebun.*'), 'text-[var(--text)]' => ! request()->routeIs('kebun.*')])>Kebun</a>
                <span class="block px-4 py-2.5 text-[var(--muted)] opacity-70" aria-disabled="true">Panen</span>
                <span class="block px-4 py-2.5 text-[var(--muted)] opacity-70" aria-disabled="true">Biaya</span>
                <span class="block px-4 py-2.5 text-[var(--muted)] opacity-70" aria-disabled="true">Laporan</span>
                <div class="my-1 border-t border-[var(--border)]"></div>
                <p class="truncate px-4 py-2 text-[var(--muted)]">{{ ucwords(Auth::user()->name) }}</p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="min-h-11 w-full rounded-lg px-4 py-2.5 text-left font-medium text-[var(--green)] hover:bg-[var(--green-soft)]">Keluar</button>
                </form>
            </nav>
        </details>
    </div>
</header>
