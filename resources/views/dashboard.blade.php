<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Kamus</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-slate-50 text-slate-950">
    <main class="mx-auto flex min-h-screen w-full max-w-7xl flex-col px-5 py-8 sm:px-6 lg:px-8">
        <header class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm md:p-6">
            <div class="flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Dashboard</p>
                    <h1 class="mt-2 text-3xl font-semibold tracking-tight sm:text-4xl">Selamat datang,
                        {{ $user->name }}.
                    </h1>
                    <p class="mt-3 max-w-2xl text-base leading-7 text-slate-600">
                        Ringkasan ini menampilkan kondisi data kamus, status editorial, dan akses cepat ke pengelolaan
                        entri.
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('dictionary.index') }}"
                        class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-medium text-slate-700 hover:bg-white">
                        Buka Kamus Publik
                    </a>
                    <a href="{{ url('/admin') }}"
                        class="inline-flex h-11 items-center rounded-lg bg-slate-950 px-4 text-sm font-medium text-white hover:bg-slate-800">
                        Admin Filament
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex h-11 items-center rounded-lg border border-slate-300 px-4 text-sm font-medium text-slate-700 hover:bg-white">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <section class="grid gap-4 py-6 sm:grid-cols-2 lg:grid-cols-4">
            <article class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Total entri</p>
                <p class="mt-2 text-2xl font-semibold">{{ $entryCount }}</p>
            </article>
            <article class="rounded-lg border border-slate-200 border-t-4 border-t-emerald-600 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Published</p>
                <p class="mt-2 text-2xl font-semibold text-emerald-700">{{ $publishedCount }}</p>
            </article>
            <article class="rounded-lg border border-slate-200 border-t-4 border-t-sky-600 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Kategori</p>
                <p class="mt-2 text-2xl font-semibold text-sky-700">{{ $categoryCount }}</p>
            </article>
            {{-- <article class="rounded-lg border border-slate-200 border-t-4 border-t-slate-400 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Draft</p>
                <p class="mt-2 text-2xl font-semibold text-slate-700">{{ $draftCount }}</p>
            </article>

            <article class="rounded-lg border border-slate-200 border-t-4 border-t-violet-600 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Visitor publik</p>
                <p class="mt-2 text-2xl font-semibold text-violet-700">{{ $visitCount }}</p>
            </article>
            <article class="rounded-lg border border-slate-200 border-t-4 border-t-violet-600 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Visitor hari ini</p>
                <p class="mt-2 text-2xl font-semibold text-violet-700">{{ $visitTodayCount }}</p>
            </article> --}}
            <article class="rounded-lg border border-slate-200 border-t-4 border-t-violet-600 bg-white p-4 shadow-sm">
                <p class="text-sm font-medium text-slate-500">Pencarian</p>
                <p class="mt-2 text-2xl font-semibold text-violet-700">{{ $searchVisitCount }}</p>
            </article>
        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold">Tren Visitor Publik</h2>
                        <p class="mt-1 text-sm text-slate-500">Kunjungan halaman kamus selama 7 hari terakhir.</p>
                    </div>
                    <span
                        class="rounded-md bg-violet-50 px-3 py-1 text-sm font-semibold text-violet-800">{{ $visitCount }}
                        Total</span>
                </div>

                <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <svg viewBox="0 0 420 220" role="img" aria-label="Grafik tren visitor publik 7 hari terakhir"
                        class="h-52 w-full overflow-visible">
                        @foreach ([55, 95, 135, 175] as $gridY)
                            <line x1="0" y1="{{ $gridY }}" x2="420" y2="{{ $gridY }}"
                                stroke="#e2e8f0" stroke-width="1" />
                        @endforeach

                        @foreach ($visitorTrend as $day)
                            @php
                                $barWidth = 28;
                                $gap = 30;
                                $x = $loop->index * ($barWidth + $gap) + 24;
                                $barHeight = max($day['value'] > 0 ? 10 : 0, round(($day['value'] / $trendMax) * 120));
                                $y = 172 - $barHeight;
                            @endphp

                            <rect x="{{ $x }}" y="{{ $y }}" width="{{ $barWidth }}"
                                height="{{ $barHeight }}" rx="6" fill="#7c3aed"></rect>
                            <text x="{{ $x + $barWidth / 2 }}" y="{{ $y - 8 }}" text-anchor="middle"
                                fill="#0f172a" font-size="12" font-weight="700">{{ $day['value'] }}</text>
                            <text x="{{ $x + $barWidth / 2 }}" y="205" text-anchor="middle" fill="#475569"
                                font-size="11">{{ $day['label'] }}</text>
                        @endforeach
                    </svg>
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-semibold">Tipe Aktivitas</h2>
                <p class="mt-1 text-sm text-slate-500">Proporsi pencarian dan pembukaan detail entry.</p>

                <div class="mx-auto mt-5 grid h-36 w-36 place-items-center rounded-full"
                    style="background: conic-gradient(#047857 0 {{ $searchVisitPercent }}%, #2563eb {{ $searchVisitPercent }}% {{ $searchVisitPercent + $detailVisitPercent }}%, #e2e8f0 {{ $searchVisitPercent + $detailVisitPercent }}% 100%);">
                    <div class="grid h-24 w-24 place-items-center rounded-full bg-white text-center shadow-sm">
                        <div>
                            <p class="text-2xl font-semibold text-slate-950">{{ $visitCount }}</p>
                            <p class="text-xs font-medium text-slate-500">Visit</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid gap-3">
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">Pencarian</span>
                            <span class="font-semibold text-emerald-700">{{ $searchVisitCount }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-emerald-700" style="width: {{ $searchVisitPercent }}%">
                            </div>
                        </div>
                    </div>
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">Detail entry</span>
                            <span class="font-semibold text-blue-700">{{ $detailVisitCount }}</span>
                        </div>
                        <div class="mt-2 h-2 rounded-full bg-slate-100">
                            <div class="h-2 rounded-full bg-blue-600" style="width: {{ $detailVisitPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </article>
        </section>

        <section class="mt-5 grid gap-6 lg:grid-cols-[0.9fr_1.1fr]">
            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div>
                    <h2 class="text-lg font-semibold">Status Editorial</h2>
                    <p class="mt-1 text-sm text-slate-500">Distribusi status entri kamus saat ini.</p>
                </div>
                <div class="mt-4 grid gap-3">
                    @foreach ([
        'draft' => ['Draft', $draftCount],
        'submitted' => ['Submitted', $submittedCount],
        'reviewed' => ['Reviewed', $reviewedCount],
        'published' => ['Published', $publishedCount],
        'rejected' => ['Rejected', $rejectedCount],
    ] as $status => [$label, $count])
                        @php
                            $statusWidth =
                                $entryCount > 0 ? max($count > 0 ? 6 : 0, round(($count / $entryCount) * 100)) : 0;
                            $statusColor = match ($status) {
                                'published' => 'bg-emerald-600',
                                'reviewed' => 'bg-sky-600',
                                'submitted' => 'bg-amber-500',
                                'rejected' => 'bg-red-500',
                                default => 'bg-slate-500',
                            };
                        @endphp
                        <div class="rounded-lg bg-slate-50 px-4 py-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium capitalize text-slate-700">{{ $label }}</span>
                                <span class="text-sm font-semibold text-slate-950">{{ $count }}</span>
                            </div>
                            <div class="mt-3 h-2 rounded-full bg-white">
                                <div class="h-2 rounded-full {{ $statusColor }}" style="width: {{ $statusWidth }}%">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between gap-3">
                    <h2 class="text-lg font-semibold">Entri terbaru</h2>
                    <a href="{{ url('/admin/dictionary-entries') }}"
                        class="text-sm font-medium text-emerald-700">Kelola</a>
                </div>

                <div class="mt-4 overflow-hidden rounded-lg border border-slate-200">
                    <table class="w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-100 text-left text-slate-600">
                            <tr>
                                <th class="px-4 py-3 font-medium">Tolaki</th>
                                <th class="px-4 py-3 font-medium">Indonesia</th>
                                <th class="px-4 py-3 font-medium">English</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Kategori</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse ($latestEntries as $entry)
                                @php
                                    $translations = $entry->senses->flatMap->translations;
                                    $indonesian = $translations->where('language_code', 'id')->pluck('form')->unique()->implode('; ');
                                    $english = $translations->where('language_code', 'en')->pluck('form')->unique()->implode('; ');
                                    $category = $entry->senses->first()?->category?->name;
                                @endphp
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-3 font-medium text-slate-950">{{ $entry->headword }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $indonesian ?: '-' }}</td>
                                    <td class="px-4 py-3 text-slate-700">{{ $english ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span
                                            class="rounded-md bg-slate-100 px-2 py-1 text-xs font-medium text-slate-700">{{ $entry->status }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $category ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-500">
                                        Belum ada entri kamus. Tambahkan data melalui Admin Filament.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>

        <section class="mt-6 rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <h2 class="text-lg font-semibold">Entri populer 30 hari terakhir</h2>
                    <p class="mt-1 text-sm text-slate-500">Ranking berdasarkan jumlah halaman detail yang dibuka.</p>
                </div>
            </div>
            <div class="mt-4 grid gap-3">
                @forelse ($popularEntries as $visit)
                    @php
                        $popularWidth = round(($visit->aggregate / $popularEntryMax) * 100);
                    @endphp
                    <div class="rounded-lg bg-slate-50 px-4 py-3">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-medium text-slate-800">
                                {{ $visit->lemma?->headword ?? $visit->entry?->tolaki_word ?? 'Entri terhapus' }}
                            </span>
                            <span class="text-sm font-semibold text-slate-950">{{ $visit->aggregate }}
                                kunjungan</span>
                        </div>
                        <div class="mt-3 h-2 rounded-full bg-white">
                            <div class="h-2 rounded-full bg-sky-600" style="width: {{ $popularWidth }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="rounded-lg border border-dashed border-slate-300 p-4 text-sm text-slate-600">
                        Belum ada data kunjungan detail entri.
                    </p>
                @endforelse
            </div>
        </section>
    </main>
</body>

</html>
