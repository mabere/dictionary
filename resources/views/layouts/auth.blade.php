<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name', 'Kamus Tolaki'))</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#f6f1e8] text-slate-950" style="background: #f6f1e8;">
    <main class="grid min-h-screen bg-[#f6f1e8] lg:grid-cols-[minmax(0,1fr)_480px]">
        <section class="hidden min-h-screen items-center justify-center bg-[#f6f1e8] px-8 py-8 lg:flex xl:px-10">
            <div class="flex min-h-[688px] w-full max-w-4xl flex-col justify-between rounded-lg border border-slate-200 bg-white px-10 py-9 shadow-sm xl:px-12 xl:py-10">
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-lg bg-emerald-700 text-base font-semibold text-white">K</span>
                        <span class="text-sm font-semibold text-slate-950">Kamus Tolaki</span>
                    </a>

                    <div class="mt-12 max-w-3xl">
                        <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">@yield('eyebrow', 'Akses Kamus')</p>
                        <h1 class="mt-4 text-4xl font-semibold tracking-tight text-slate-950 xl:text-5xl">@yield('headline')</h1>
                        <p class="mt-5 text-base leading-8 text-slate-600">@yield('description')</p>

                        @hasSection('aside')
                            <div class="mt-7">
                                @yield('aside')
                            </div>
                        @endif
                    </div>
                </div>

                <p class="mt-10 text-sm text-slate-500">{{ date('Y') }} - Kamus Tolaki</p>
            </div>
        </section>

        <section class="flex min-h-screen items-start justify-center bg-[#f6f1e8] px-5 py-8 sm:px-8 lg:items-center lg:px-10">
            <div class="w-full max-w-md">
                <a href="{{ route('home') }}" class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="grid h-10 w-10 place-items-center rounded-lg bg-emerald-700 text-base font-semibold text-white">K</span>
                    <span class="text-sm font-semibold text-slate-950">Kamus Tolaki</span>
                </a>

                @yield('content')

                <div class="mt-6 rounded-lg border border-slate-200 bg-white p-6 shadow-sm sm:p-8 lg:hidden">
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">@yield('eyebrow', 'Akses Kamus')</p>
                    <h1 class="mt-3 text-2xl font-semibold tracking-tight text-slate-950 sm:text-3xl">@yield('headline')</h1>
                    <p class="mt-4 text-sm leading-7 text-slate-600">@yield('description')</p>

                    @hasSection('aside')
                        <div class="mt-5">
                            @yield('aside')
                        </div>
                    @endif

                    <p class="mt-6 text-sm text-slate-500">{{ date('Y') }} - Kamus Tolaki</p>
                </div>
            </div>
        </section>
    </main>
</body>

</html>