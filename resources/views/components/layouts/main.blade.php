<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Perpustakaan Digital') }}</title>
    @vite(['resources/css/app.css'])
</head>

<body>
    <div id="appShell" class="app-shell">
        <header class="shell-header">
            <div class="shell-header-inner">
                <div class="flex items-center gap-4">
                    <button id="sidebarToggle" type="button" aria-label="Toggle sidebar"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-stone-200 bg-white text-stone-700 transition hover:bg-stone-100">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round">
                            <path d="M4 7h16" />
                            <path d="M4 12h16" />
                            <path d="M4 17h16" />
                        </svg>
                    </button>
                    <div class="brand-mark">LIB</div>
                    <div>
                        <a href="{{ route('home') }}" class="text-lg font-semibold tracking-tight text-stone-900">
                            Perpustakaan Digital
                        </a>
                        <p class="text-sm text-stone-500">Sistem layanan surat dan administrasi perpustakaan</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @auth
                        <div class="hidden rounded-2xl border border-stone-200 bg-stone-50 px-4 py-2 text-right md:block">
                            <p class="text-sm font-medium text-stone-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs uppercase tracking-[0.18em] text-stone-500">{{ auth()->user()->role }}</p>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="btn-sm bg-brand-900 text-white hover:bg-brand-700">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-sm bg-brand-900 text-white hover:bg-brand-700">
                            Login
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <div class="shell-body">
            <aside id="sidebar" class="shell-sidebar" aria-hidden="false">
                <div class="shell-sidebar-panel">
                    <div class="border-b border-white/10 px-6 py-6">
                        <div class="flex items-start justify-between gap-4">
                            <div class="sidebar-brand-copy">
                                <p class="sidebar-kicker">Navigation</p>
                                <h2 class="sidebar-heading">Ruang kerja perpustakaan</h2>
                                {{-- <p class="sidebar-copy">
                                    Layout ini menjadi fondasi tampilan semua modul agar terasa konsisten, rapi, dan profesional.
                                </p> --}}
                            </div>
                            <button id="sidebarClose" type="button" aria-label="Tutup menu"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-2xl border border-white/10 text-stone-200 transition hover:bg-white/10 lg:hidden">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round">
                                    <path d="M6 6l12 12" />
                                    <path d="M18 6l-12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="sidebar-scroll">
                        <nav class="space-y-2 px-4 py-4">
                            <a href="{{ route('home') }}"
                                class="sidebar-link {{ request()->routeIs('home') ? 'sidebar-link-active' : 'sidebar-link-idle' }}">
                                <div class="sidebar-link-main">
                                    <span class="sidebar-icon">H</span>
                                    <span class="sidebar-nav-label">Beranda</span>
                                </div>
                                <span class="sidebar-nav-meta">00</span>
                            </a>
                            <a href="{{ route('dashboard') }}"
                                class="sidebar-link {{ request()->routeIs('dashboard') ? 'sidebar-link-active' : 'sidebar-link-idle' }}">
                                <div class="sidebar-link-main">
                                    <span class="sidebar-icon">D</span>
                                    <span class="sidebar-nav-label">Dashboard</span>
                                </div>
                                <span class="sidebar-nav-meta">01</span>
                            </a>
                            <a href="{{ route('free-library-requests.index') }}"
                                class="sidebar-link {{ request()->routeIs('free-library-requests.*') ? 'sidebar-link-active' : 'sidebar-link-idle' }}">
                                <div class="sidebar-link-main">
                                    <span class="sidebar-icon">B</span>
                                    <span class="sidebar-nav-label">Bebas Pustaka</span>
                                </div>
                                <span class="sidebar-nav-meta">02</span>
                            </a>
                            <a href="{{ route('thesis-submissions.index') }}"
                                class="sidebar-link {{ request()->routeIs('thesis-submissions.*') ? 'sidebar-link-active' : 'sidebar-link-idle' }}">
                                <div class="sidebar-link-main">
                                    <span class="sidebar-icon">S</span>
                                    <span class="sidebar-nav-label">Setor Skripsi</span>
                                </div>
                                <span class="sidebar-nav-meta">03</span>
                            </a>
                        <a href="{{ route('research-permits.index') }}"
                            class="sidebar-link {{ request()->routeIs('research-permits.*') ? 'sidebar-link-active' : 'sidebar-link-idle' }}">
                            <div class="sidebar-link-main">
                                <span class="sidebar-icon">I</span>
                                <span class="sidebar-nav-label">Surat Ijin Penelitian</span>
                            </div>
                            <span class="sidebar-nav-meta">04</span>
                        </a>
                        @if (in_array(auth()->user()->role, ['staf', 'kepala', 'admin'], true))
                            <a href="{{ route('reports.index') }}"
                                class="sidebar-link {{ request()->routeIs('reports.*') ? 'sidebar-link-active' : 'sidebar-link-idle' }}">
                                <div class="sidebar-link-main">
                                    <span class="sidebar-icon">L</span>
                                    <span class="sidebar-nav-label">Laporan</span>
                                </div>
                                <span class="sidebar-nav-meta">05</span>
                            </a>
                        @endif
                    </nav>

                        <div class="border-t border-white/10 px-6 py-5">
                            @auth
                                <div class="sidebar-session">
                                    <p class="sidebar-session-kicker">Sesi aktif</p>
                                    <p class="sidebar-session-email">{{ auth()->user()->email }}</p>
                                    <p class="sidebar-session-copy">
                                        Anda sedang masuk sebagai {{ auth()->user()->role }}.
                                    </p>
                                </div>

                                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                                    @csrf
                                    <button type="submit"
                                        class="sidebar-link sidebar-link-idle w-full cursor-pointer border border-white/10 bg-white/5 hover:bg-white/10">
                                        <div class="sidebar-link-main">
                                            <span class="sidebar-icon">
                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                                    <path d="M16 17l5-5-5-5" />
                                                    <path d="M21 12H9" />
                                                </svg>
                                            </span>
                                            <span class="sidebar-nav-label">Logout</span>
                                        </div>
                                        <span class="sidebar-nav-meta"></span>
                                    </button>
                                </form>
                            @else
                                <div class="sidebar-session">
                                    <p class="sidebar-session-kicker">Akses tamu</p>
                                    <p class="sidebar-session-copy">
                                        Login untuk membuka dashboard internal dan modul layanan.
                                    </p>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </aside>

            <div id="sidebarOverlay" class="sidebar-overlay hidden lg:hidden"></div>

            <div class="shell-main">
                <div class="shell-toolbar">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.22em] text-stone-500">App Shell</p>
                            <p class="mt-1 text-sm leading-6 text-stone-600">
                                Header, sidebar, content, dan footer kini disatukan dalam satu template layout berbasis
                                Tailwind.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-outline-primary px-4 py-2">
                                    Dashboard
                                </a>
                                <a href="{{ route('free-library-requests.index') }}" class="btn-outline-info px-4 py-2">
                                    Bebas Pustaka
                                </a>
                                <a href="{{ route('thesis-submissions.index') }}" class="btn-outline-warning px-4 py-2">
                                    Setor Skripsi
                                </a>
                                <a href="{{ route('research-permits.index') }}" class="btn-outline-success px-4 py-2">
                                    Izin Penelitian
                                </a>
                                @if (in_array(auth()->user()->role, ['staf', 'kepala', 'admin'], true))
                                    <a href="{{ route('reports.index') }}" class="btn-secondary px-4 py-2">
                                        Laporan
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn-primary px-4 py-2">
                                    Masuk ke Sistem
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                @if (session('status'))
                    <div class="flash-success">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="flash-error">
                        <p class="font-medium">Ada beberapa hal yang perlu diperiksa:</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-6">
                    {{ $slot }}
                </div>
            </div>
        </div>

        <footer class="app-footer">
            <div class="app-footer-inner">
                <p>Build with love by Laravel + Tailwind CSS</p>
                <p>{{ date('Y') }} - UPT Perpustakaan Universitas Lakidende Unaaha</p>
            </div>
        </footer>
    </div>

    <script>
        const appShell = document.getElementById('appShell');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggle = document.getElementById('sidebarToggle');
        const closeButton = document.getElementById('sidebarClose');
        const desktopBreakpoint = window.matchMedia('(min-width: 1024px)');

        let desktopCollapsed = false;
        let mobileOpen = false;

        const syncSidebarState = () => {
            if (!appShell || !sidebar || !overlay) {
                return;
            }

            if (desktopBreakpoint.matches) {
                mobileOpen = false;
                overlay.classList.add('hidden');
                sidebar.classList.remove('hidden');
                sidebar.setAttribute('aria-hidden', 'false');
                document.body.classList.remove('overflow-hidden');
                appShell.classList.toggle('sidebar-collapsed', desktopCollapsed);
                appShell.classList.remove('sidebar-mobile-open');
            } else {
                desktopCollapsed = false;
                appShell.classList.remove('sidebar-collapsed');
                appShell.classList.toggle('sidebar-mobile-open', mobileOpen);
                sidebar.classList.toggle('hidden', !mobileOpen);
                sidebar.setAttribute('aria-hidden', mobileOpen ? 'false' : 'true');
                overlay.classList.toggle('hidden', !mobileOpen);
                document.body.classList.toggle('overflow-hidden', mobileOpen);
            }
        };

        const toggleSidebar = () => {
            if (desktopBreakpoint.matches) {
                desktopCollapsed = !desktopCollapsed;
            } else {
                mobileOpen = !mobileOpen;
            }

            syncSidebarState();
        };

        const closeSidebar = () => {
            if (desktopBreakpoint.matches) {
                desktopCollapsed = true;
            } else {
                mobileOpen = false;
            }

            syncSidebarState();
        };

        toggle?.addEventListener('click', toggleSidebar);
        closeButton?.addEventListener('click', closeSidebar);
        overlay?.addEventListener('click', closeSidebar);

        window.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });

        const handleViewportChange = () => {
            mobileOpen = false;
            syncSidebarState();
        };

        if (desktopBreakpoint.addEventListener) {
            desktopBreakpoint.addEventListener('change', handleViewportChange);
        } else {
            window.addEventListener('resize', handleViewportChange);
        }

        syncSidebarState();
    </script>
</body>

</html>
