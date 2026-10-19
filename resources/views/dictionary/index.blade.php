@extends('layouts.public')

@section('title', 'Kamus Tolaki Indonesia Inggris')

@section('content')
    <section class="py-8">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Kamus Tolaki Indonesia Inggris</p>
            <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-950 sm:text-4xl">Cari lema, padanan, atau makna</h1>
            <p class="mt-4 text-base leading-7 text-slate-600">
                Telusuri lema Tolaki, padanan Indonesia atau Inggris, serta definisi dari entri yang sudah diterbitkan.
            </p>
        </div>

        <form method="GET" action="{{ route('dictionary.index') }}" class="mx-auto mt-7 max-w-3xl">
            @php
                $hasAdvancedFilters = $filters['direction'] !== 'both' || filled($filters['category']) || filled($filters['part_of_speech']);
            @endphp
            <style>
                #advanced-search-panel { display: none; }
                #advanced-search-toggle:checked~#advanced-search-panel { display: grid; }
            </style>
            <div class="rounded-lg border border-slate-200 bg-white p-2 shadow-sm sm:p-3">
                <input id="advanced-search-toggle" type="checkbox" class="hidden" @checked($hasAdvancedFilters)>
                <div class="grid gap-2 sm:flex sm:gap-3">
                    <input name="q" value="{{ $filters['q'] }}" type="search" autofocus
                        placeholder="Cari dalam Tolaki, Indonesia, Inggris, atau definisi"
                        class="h-12 min-w-0 rounded-md border border-slate-300 px-4 text-base outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 sm:h-10 sm:flex-1">
                    <button type="submit" class="h-11 rounded-md bg-emerald-700 px-4 text-sm font-semibold text-white hover:bg-emerald-800 sm:h-10">Cari</button>
                    <label for="advanced-search-toggle" class="inline-flex h-11 cursor-pointer items-center justify-center rounded-md bg-sky-700 px-3 text-sm font-semibold text-white hover:bg-sky-800 sm:h-10">Filter</label>
                </div>
                <div id="advanced-search-panel" class="mt-3 gap-2 border-t border-slate-200 pt-3 md:grid-cols-[1fr_1fr_1fr_auto]">
                    <select name="direction" aria-label="Arah pencarian" class="h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700">
                        <option value="both" @selected($filters['direction'] === 'both')>Semua bahasa</option>
                        <option value="tolaki" @selected($filters['direction'] === 'tolaki')>Lema Tolaki</option>
                        <option value="indonesian" @selected($filters['direction'] === 'indonesian')>Padanan Indonesia</option>
                        <option value="english" @selected($filters['direction'] === 'english')>Padanan Inggris</option>
                    </select>
                    <select name="category" aria-label="Kategori" class="h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) $filters['category'] === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select name="part_of_speech" aria-label="Kelas kata" class="h-10 rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-700">
                        <option value="">Semua kelas kata</option>
                        @foreach ($partsOfSpeech as $part)
                            <option value="{{ $part }}" @selected($filters['part_of_speech'] === $part)>{{ $part }}</option>
                        @endforeach
                    </select>
                    <a href="{{ route('dictionary.index') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-medium text-slate-700 hover:bg-slate-100">Reset</a>
                </div>
            </div>
        </form>
    </section>

    <section class="rounded-lg border border-slate-200 bg-white px-3 py-2 shadow-sm" aria-label="Indeks huruf awal Tolaki">
        <div class="flex items-center gap-3 overflow-x-auto">
            <div class="flex min-w-max items-center gap-1 text-sm">
                <a href="{{ route('dictionary.index', request()->except('letter', 'page')) }}" class="inline-flex h-8 items-center justify-center rounded-md px-3 font-medium {{ $filters['letter'] === '' ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">Semua</a>
                @foreach ($letters as $letter)
                    <a href="{{ route('dictionary.index', array_merge(request()->except('page'), ['letter' => $letter])) }}" class="inline-flex h-8 min-w-8 items-center justify-center rounded-md px-2 font-medium uppercase {{ $filters['letter'] === $letter ? 'bg-emerald-700 text-white' : 'text-slate-600 hover:bg-slate-100' }}">{{ $letter }}</a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="py-6">
        <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-slate-950">Lema Tolaki</h2>
                <p class="mt-1 text-sm text-slate-500">{{ $entries->total() }} lema ditemukan</p>
            </div>
            <p class="text-sm text-slate-500">Indeks awal: {{ $alphabetProfile }}{{ $alphabetProfileIsActive ? '' : ' (belum disahkan)' }}.</p>
        </div>

        <div class="grid gap-5 lg:grid-cols-2">
            @forelse ($entries as $lemma)
                @php
                    $translations = $lemma->senses->flatMap->translations;
                    $indonesian = $translations->where('language_code', 'id')->pluck('form')->unique()->implode('; ');
                    $english = $translations->where('language_code', 'en')->pluck('form')->unique()->implode('; ');
                    $firstSense = $lemma->senses->first();
                    $firstExample = $firstSense?->examples->first();
                @endphp
                <article class="rounded-lg border border-slate-200 bg-white px-5 py-5 shadow-sm sm:px-6">
                    <div class="flex flex-wrap items-baseline gap-x-3 gap-y-1">
                        <a href="{{ route('dictionary.show', ['lemma' => $lemma->public_id]) }}" class="text-2xl font-semibold text-emerald-800 hover:text-emerald-950">{{ $lemma->headword }}</a>
                        @if ($lemma->senses->count() > 1)
                            <span class="rounded bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">{{ $lemma->senses->count() }} makna</span>
                        @endif
                        @foreach ($lemma->senses->pluck('part_of_speech')->filter()->unique()->take(3) as $part)
                            <span class="rounded bg-slate-100 px-2 py-1 text-xs font-medium text-slate-600">{{ $part }}</span>
                        @endforeach
                    </div>
                    <p class="mt-2 text-sm text-slate-600">
                        <span class="font-medium">Indonesia:</span> {{ $indonesian ?: '—' }}
                        <span class="mx-1 text-slate-300">/</span>
                        <span class="font-medium">Inggris:</span> {{ $english ?: '—' }}
                    </p>
                    @if ($firstSense?->definition)
                        <p class="mt-3 leading-7 text-slate-700">{{ $firstSense->definition }}</p>
                    @endif
                    @if ($firstExample)
                        <p class="mt-3 border-l-2 border-emerald-200 pl-3 text-sm leading-6 text-slate-600">{{ $firstExample->tolaki_text }}</p>
                    @endif
                    <a href="{{ route('dictionary.show', ['lemma' => $lemma->public_id]) }}" class="mt-4 inline-flex text-sm font-medium text-emerald-700 hover:text-emerald-900">Lihat semua makna</a>
                </article>
            @empty
                <div class="rounded-lg border border-slate-200 bg-white px-5 py-12 text-center shadow-sm lg:col-span-2">
                    <p class="text-lg font-medium text-slate-900">Belum ada lema terbit yang cocok.</p>
                    <p class="mt-2 text-sm text-slate-600">Coba kata kunci lain atau ubah filter pencarian.</p>
                </div>
            @endforelse
        </div>
        <div class="pt-5">{{ $entries->links() }}</div>
    </section>
@endsection
