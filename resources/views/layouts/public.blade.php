<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Kamus Tolaki'))</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-950">
    <main class="mx-auto flex min-h-screen w-full flex-col px-5 py-6 sm:px-6 lg:px-8" style="max-width: {{ $maxWidth ?? '1120px' }};">
        <header class="flex items-center justify-between gap-4 rounded-lg border border-emerald-100 bg-emerald-50/70 px-4 py-3 shadow-sm">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-lg bg-emerald-700 text-base font-semibold text-white">K</span>
                <span class="text-sm font-semibold text-slate-950">Kamus Tolaki</span>
            </a>

            <nav class="flex items-center gap-2">
                <a href="{{ route('dictionary.contribute') }}" class="inline-flex h-10 items-center rounded-lg border border-emerald-200 bg-white/70 px-3 text-xs font-medium text-emerald-800 hover:bg-white sm:px-4 sm:text-sm">
                    Usulkan Kata
                </a>
                @auth
                    @if (auth()->user()?->canViewDictionaryBackoffice())
                        <a href="{{ route('dashboard') }}" class="inline-flex h-10 items-center rounded-lg bg-slate-950 px-4 text-sm font-medium text-white hover:bg-slate-800">
                            Dashboard
                        </a>
                    @else
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex h-10 items-center rounded-lg bg-slate-950 px-4 text-sm font-medium text-white hover:bg-slate-800">
                                Logout
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="inline-flex h-10 items-center rounded-lg bg-slate-950 px-4 text-sm font-medium text-white hover:bg-slate-800">
                        Login
                    </a>
                @endauth
            </nav>
        </header>

        <div class="flex-1">
            @yield('content')
        </div>

        <div class="mt-6 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
            <section class="bg-slate-50 px-4 py-4">
                <div class="flex flex-col gap-2 text-center text-sm text-slate-600 sm:flex-row sm:items-center sm:justify-center sm:gap-4">
                    <span>Visitor publik: <strong class="font-semibold text-slate-900">{{ $publicVisitorTotal ?? 0 }}</strong></span>
                    <span class="hidden text-slate-300 sm:inline">/</span>
                    <span>Hari ini: <strong class="font-semibold text-slate-900">{{ $publicVisitorToday ?? 0 }}</strong></span>
                </div>
            </section>

            <footer class="border-t border-slate-200 bg-emerald-50/70 px-4 py-5 text-center text-sm text-slate-600">
                <p>{{ date('Y') }} - Kamus Tolaki. Pencarian, kontribusi, dan validasi kosakata.</p>
            </footer>
        </div>
    </main>
</body>

</html>
