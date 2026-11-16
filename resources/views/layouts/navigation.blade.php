<div class="mobile-topbar">
    <button id="sidebar-toggle" class="mobile-menu-button" type="button" aria-label="Buka menu" aria-controls="app-sidebar" aria-expanded="false">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
            <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
        </svg>
    </button>
    <a href="{{ route('dashboard') }}" class="sidebar-brand !gap-2 !p-0 text-lg" aria-label="Sawitku, Dashboard">
        <svg class="h-7 w-7" viewBox="0 0 40 40" fill="none" aria-hidden="true">
            <path d="M20 35V18m0 7C17 17 10 13 4 14c2 7 7 11 16 11Zm0-3c3-8 9-12 16-11-2 7-7 11-16 11Zm0-8c-1-5-4-9-9-12m9 9c2-5 5-8 10-11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Sawitku
    </a>
</div>

<button id="sidebar-overlay" class="sidebar-overlay" type="button" aria-label="Tutup menu"></button>

<aside id="app-sidebar" class="app-sidebar" aria-label="Navigasi utama">
    <a href="{{ route('dashboard') }}" class="sidebar-brand" aria-label="Sawitku, Dashboard">
        <svg class="h-8 w-8" viewBox="0 0 40 40" fill="none" aria-hidden="true">
            <path d="M20 35V18m0 7C17 17 10 13 4 14c2 7 7 11 16 11Zm0-3c3-8 9-12 16-11-2 7-7 11-16 11Zm0-8c-1-5-4-9-9-12m9 9c2-5 5-8 10-11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        Sawitku
    </a>

    <p class="sidebar-greeting">Hi,
        <strong>{{ ucwords(Auth::user()->name) }}</strong>
    </p>

    <nav class="sidebar-menu">
        <a href="{{ route('dashboard') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('dashboard')])>
            <span class="sidebar-icon">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="3.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="13.5" y="3.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="3.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                    <rect x="13.5" y="13.5" width="7" height="7" rx="1.5" stroke="currentColor" stroke-width="1.7"/>
                </svg>
            </span>
            Dashboard
        </a>
        <a href="{{ route('kebun.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('kebun.*')])>
            <span class="sidebar-icon">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M3 20.5h18M5.5 20V9.5h13V20M8 9.5V6h8v3.5M9 13h2m2 0h2m-6 3h2m2 0h2" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            Kebun
        </a>
        <a href="{{ route('panen.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('panen.*')])>
            <span class="sidebar-icon">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 20.5V10m0 5c-1.6-4-4.8-6-9-5.8 1.1 4.1 4.1 6.1 9 6.3Zm0-2.3c1.7-4.1 5-6 9-5.5-1.2 4.1-4.2 6-9 6Zm0-6c-.5-2-1.7-3.7-3.6-5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            Panen
        </a>
        <a href="{{ route('biaya.index') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('biaya.*')])>
            <span class="sidebar-icon">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M12 3.5v17m4-13c0-1.8-1.8-3-4-3s-4 1.2-4 3 1.5 2.5 4 3.5 4 1.7 4 3.5-1.8 3-4 3-4-1.2-4-3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            Biaya
        </a>
        <a href="{{ route('laporan') }}" @class(['sidebar-link', 'sidebar-link-active' => request()->routeIs('laporan*')])>
            <span class="sidebar-icon">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 19.5V4.5m0 15h16M7 15l4-4 3 2 5-6m-4 0h4v4" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            Laporan
        </a>
        <span class="sidebar-disabled" aria-disabled="true">
            <span class="sidebar-icon">
                <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <rect x="3.5" y="5" width="17" height="15.5" rx="2" stroke="currentColor" stroke-width="1.7"/>
                    <path d="M7.5 3.5v3M16.5 3.5v3M3.5 9.5h17M8 13h.01M12 13h.01M16 13h.01M8 16.5h.01M12 16.5h.01" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                </svg>
            </span>
            Jadwal
        </span>
    </nav>

    <div class="sidebar-help">
        <strong>Bantuan</strong>
        Perlu bantuan mengelola catatan kebun?
    </div>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-logout w-full">
            <svg class="h-[18px] w-[18px]" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M10 4H5.5A1.5 1.5 0 0 0 4 5.5v13A1.5 1.5 0 0 0 5.5 20H10m5-4 4-4-4-4m4 4H9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Keluar
        </button>
    </form>
</aside>
