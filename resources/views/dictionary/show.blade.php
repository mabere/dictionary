@extends('layouts.public', ['maxWidth' => '960px'])

@section('title', $lemma->headword.' â€” Kamus Tolaki')

@section('content')
    <section class="py-8 sm:py-10">
        @php
            $sourceReferences = [];
            $registerSource = function ($source, string $usage) use (&$sourceReferences): void {
                if (! $source || $source->visibility !== 'public' || ! in_array($source->rights_status, ['permission_granted', 'public_domain'], true)) return;
                $id = $source->getKey();
                $sourceReferences[$id] ??= ['citation' => $source->citation, 'uses' => []];
                $sourceReferences[$id]['uses'][] = $usage;
            };
            foreach ($lemma->forms as $form) {
                $registerSource($form->source, 'Bentuk: '.$form->form);
                foreach ($form->analyses as $analysis) $registerSource($analysis->source, 'Analisis morfologi: '.$form->form);
            }
            foreach ($lemma->senses as $sense) {
                $registerSource($sense->source, 'Definisi makna '.$sense->sense_number);
                foreach ($sense->translations as $translation) $registerSource($translation->source, 'Padanan '.strtoupper($translation->language_code).' makna '.$sense->sense_number);
                foreach ($sense->examples as $example) {
                    $registerSource($example->source, 'Contoh Tolaki makna '.$sense->sense_number);
                    foreach ($example->translations as $translation) $registerSource($translation->source, 'Terjemahan contoh '.strtoupper($translation->language_code).' makna '.$sense->sense_number);
                }
            }
            foreach ($lemma->pronunciations as $pronunciation) $registerSource($pronunciation->source, 'Pelafalan lema');
        @endphp
        <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('dictionary.index', request()->query()) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-800 hover:text-emerald-950">
                <span aria-hidden="true">â†</span> Kembali ke hasil kamus
            </a>
            <form method="GET" action="{{ route('dictionary.index') }}" class="flex w-full gap-2 sm:w-auto">
                <label for="detail-search" class="sr-only">Cari lema atau padanan</label>
                <input id="detail-search" name="q" type="search" value="{{ request('q') }}" placeholder="Cari lema atau padanan"
                    class="h-10 min-w-0 flex-1 rounded-lg border border-slate-300 bg-white px-3 text-sm outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-100 sm:w-64">
                <button type="submit" class="h-10 rounded-lg bg-emerald-800 px-4 text-sm font-semibold text-white hover:bg-emerald-900">Cari</button>
            </form>
        </div>

        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="relative border-b border-emerald-100 bg-gradient-to-br from-emerald-50 via-white to-sky-50 px-6 py-8 sm:px-9 sm:py-10">
                <p class="text-xs font-bold uppercase tracking-[0.18em] text-emerald-800">Kamus Tolaki Â· Entri leksikal</p>
                <div class="mt-3 flex flex-wrap items-end justify-between gap-4">
                    <h1 class="break-words text-4xl font-semibold tracking-tight text-slate-950 sm:text-6xl">{{ $lemma->headword }}</h1>
                    @if ($lemma->orthographyProfile)
                        <span class="rounded-full border border-slate-200 bg-white/80 px-3 py-1.5 text-xs text-slate-600">
                            Ejaan: {{ $lemma->orthographyProfile->name }} Â· {{ $lemma->orthographyProfile->version }}
                        </span>
                    @endif
                    <span class="rounded-full bg-emerald-800 px-3 py-1.5 text-xs font-semibold text-white">Status entri: Diterbitkan setelah peninjauan</span>
                </div>

                @if ($lemma->forms->isNotEmpty())
                    <div class="mt-5 flex flex-wrap items-start gap-x-2 gap-y-2 text-sm">
                        <span class="pt-1 font-semibold text-slate-600">Bentuk terkait:</span>
                        @foreach ($lemma->forms as $form)
                            <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-slate-700">
                                <span class="font-medium">{{ $form->form }}</span>
                                <span class="ml-1 text-[11px] text-slate-500">{{ ['variant' => 'Varian', 'derived' => 'Turunan', 'subentry' => 'Subentri', 'historical' => 'Historis'][$form->form_type] ?? $form->form_type }}{{ $form->region_label ? ' Â· '.$form->region_label : '' }}</span>
                            </span>
                        @endforeach
                    </div>
                @endif
            </header>

            <div class="space-y-8 px-5 py-6 sm:px-9 sm:py-8">
                @php
                    $pronunciations = $lemma->pronunciations->filter(fn ($item) => filled($item->transcription) || ($item->audio_path && in_array($item->rights_status, ['permission_granted', 'public_domain'], true)));
                @endphp
                @if ($pronunciations->isNotEmpty())
                    <section aria-labelledby="pronunciation-heading" class="rounded-xl border border-slate-200 bg-slate-50 p-4 sm:p-5">
                        <h2 id="pronunciation-heading" class="text-xs font-bold uppercase tracking-wider text-slate-500">Pelafalan</h2>
                        <div class="mt-3 flex flex-wrap gap-3">
                            @foreach ($pronunciations as $pronunciation)
                                <div class="min-w-48 rounded-lg border border-white bg-white p-3 shadow-sm">
                                    @if ($pronunciation->transcription)
                                        <p class="text-lg font-medium text-slate-900">/{{ $pronunciation->transcription }}/</p>
                                        @if ($pronunciation->transcription_system)
                                            <p class="mt-1 text-xs text-slate-500">{{ $pronunciation->transcription_system }}</p>
                                        @endif
                                    @endif
                                    @if ($pronunciation->region_label)
                                        <p class="mt-1 text-xs text-slate-500">Wilayah: {{ $pronunciation->region_label }}</p>
                                    @endif
                                    @if ($pronunciation->audio_path && in_array($pronunciation->rights_status, ['permission_granted', 'public_domain'], true))
                                        <audio controls preload="none" class="mt-3 w-full"><source src="{{ Storage::disk('public')->url($pronunciation->audio_path) }}">Browser Anda tidak mendukung pemutar audio.</audio>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                @php $reviewedAnalyses = $lemma->forms->flatMap->analyses; @endphp
                @if ($reviewedAnalyses->isNotEmpty())
                    <section aria-labelledby="morphology-heading">
                        <div class="mb-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Struktur kata</p>
                            <h2 id="morphology-heading" class="mt-1 text-xl font-semibold text-slate-950">Analisis morfologi</h2>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach ($lemma->forms as $form)
                                @foreach ($form->analyses as $analysis)
                                    <div class="rounded-xl border border-slate-200 p-4">
                                        <p class="text-sm text-slate-500">Bentuk: <span class="font-semibold text-slate-800">{{ $form->form }}</span></p>
                                        @if ($analysis->segmentation)
                                            <p class="mt-2 font-mono text-sm text-emerald-900">{{ $analysis->segmentation }}</p>
                                        @endif
                                        @if ($analysis->baseLemma)
                                            <p class="mt-2 text-sm text-slate-700">Lema dasar: {{ $analysis->baseLemma->headword }}</p>
                                        @endif
                                        @if ($analysis->analysis_note)
                                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $analysis->analysis_note }}</p>
                                        @endif
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </section>
                @endif

                <section aria-labelledby="senses-heading">
                    <div class="mb-4 flex flex-wrap items-end justify-between gap-2">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Informasi leksikal</p>
                            <h2 id="senses-heading" class="mt-1 text-2xl font-semibold text-slate-950">Makna dan padanan</h2>
                        </div>
                        <span class="text-sm text-slate-500">{{ $lemma->senses->count() }} {{ $lemma->senses->count() === 1 ? 'makna' : 'makna' }}</span>
                    </div>

                    <div class="space-y-5">
                        @foreach ($lemma->senses as $sense)
                            <section id="sense-{{ $sense->sense_number }}" class="scroll-mt-24 overflow-hidden rounded-xl border border-slate-200">
                                <div class="flex flex-wrap items-center gap-2 border-b border-slate-100 bg-slate-50/80 px-4 py-3 sm:px-5">
                                    <span class="rounded-md bg-emerald-800 px-2.5 py-1 text-xs font-bold text-white">Makna {{ $sense->sense_number }}</span>
                                    @if ($sense->part_of_speech)
                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-medium text-slate-700 ring-1 ring-slate-200">{{ ['noun' => 'Nomina', 'verb' => 'Verba', 'adjective' => 'Adjektiva', 'adverb' => 'Adverbia', 'pronoun' => 'Pronomina', 'preposition' => 'Preposisi', 'conjunction' => 'Konjungsi', 'interjection' => 'Interjeksi', 'phrase' => 'Frasa/ungkapan'][$sense->part_of_speech] ?? $sense->part_of_speech }}</span>
                                    @endif
                                    @foreach ([$sense->category?->name, $sense->domain_label, $sense->register_label] as $label)
                                        @if ($label)<span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-medium text-sky-800">{{ $label }}</span>@endif
                                    @endforeach
                                </div>

                                <div class="p-4 sm:p-5">
                                    @if ($sense->definition)
                                        @php $mainDefinitionLabel = ['id' => 'Definisi bahasa Indonesia', 'tol' => 'Definisi bahasa Tolaki', 'en' => 'Definisi bahasa Inggris'][$sense->definition_language] ?? 'Definisi'; @endphp
                                        <p class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $mainDefinitionLabel }}</p>
                                        <p class="mt-1 text-base leading-7 text-slate-800">{{ $sense->definition }}</p>
                                    @endif
                                    @if ($sense->definition_tolaki || $sense->definition_english)
                                        <dl class="mt-3 grid gap-3 sm:grid-cols-2">
                                            @if ($sense->definition_tolaki && $sense->definition_language !== 'tol')<div class="rounded-lg border border-slate-100 p-3"><dt class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Definisi bahasa Tolaki</dt><dd class="mt-1 text-sm leading-6 text-slate-700">{{ $sense->definition_tolaki }}</dd></div>@endif
                                            @if ($sense->definition_english && $sense->definition_language !== 'en')<div class="rounded-lg border border-slate-100 p-3"><dt class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Definition in English</dt><dd class="mt-1 text-sm leading-6 text-slate-700">{{ $sense->definition_english }}</dd></div>@endif
                                        </dl>
                                    @endif
                                    <p class="mt-2 text-xs font-medium text-emerald-800">Status makna: {{ ['published' => 'Diterbitkan', 'reviewed' => 'Ditinjau', 'draft' => 'Draf', 'submitted' => 'Menunggu peninjauan', 'rejected' => 'Perlu revisi'][$sense->status] ?? 'Belum ditetapkan' }}</p>

                                    <dl class="mt-4 grid gap-3 md:grid-cols-2">
                                        @foreach (['id' => ['Padanan Indonesia', 'emerald'], 'en' => ['English equivalents', 'sky']] as $languageCode => [$label, $color])
                                            @php $items = $sense->translations->where('language_code', $languageCode); @endphp
                                            <div class="rounded-lg border border-slate-100 bg-slate-50/70 p-3.5">
                                                <dt class="text-[11px] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</dt>
                                                <dd class="mt-2 space-y-2">
                                                    @forelse ($items as $translation)
                                                        <div>
                                                            <p class="font-semibold text-slate-900">{{ $translation->form }}
                                                                @if ($translation->context_note)<span class="font-normal text-slate-500">â€” {{ $translation->context_note }}</span>@endif
                                                            </p>
                                                            @if ($translation->translation_type !== 'equivalent')
                                                                <p class="text-xs text-slate-500">{{ ['gloss' => 'Glos', 'descriptive' => 'Padanan deskriptif', 'contextual' => 'Padanan kontekstual'][$translation->translation_type] ?? $translation->translation_type }}</p>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <span class="text-sm italic text-slate-500">Belum tersedia</span>
                                                    @endforelse
                                                </dd>
                                            </div>
                                        @endforeach
                                    </dl>

                                    @if ($sense->usage_note || $sense->cultural_note)
                                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                                            @if ($sense->usage_note)<section><h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Catatan penggunaan</h3><p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $sense->usage_note }}</p></section>@endif
                                            @if ($sense->cultural_note)<section><h3 class="text-xs font-bold uppercase tracking-wider text-amber-700">Catatan budaya</h3><p class="mt-1 whitespace-pre-line text-sm leading-6 text-slate-700">{{ $sense->cultural_note }}</p></section>@endif
                                        </div>
                                    @endif

                                    @php $publicRelations = $sense->relations->filter(fn ($relation) => $relation->relatedSense?->lemma?->status === 'published'); @endphp
                                    @if ($publicRelations->isNotEmpty())
                                        <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3">
                                            <span class="text-xs font-semibold text-slate-500">Relasi:</span>
                                            @foreach ($publicRelations as $relation)
                                                @php $relationTypeLabel = ['synonym' => 'Sinonim', 'antonym' => 'Antonim', 'see_also' => 'Lihat juga', 'related' => 'Berkaitan'][$relation->relation_type] ?? $relation->relation_type; @endphp
                                                @if ($relation->relatedSense->lemma->is($lemma))
                                                    <a href="#sense-{{ $relation->relatedSense->sense_number }}" class="rounded-full bg-violet-50 px-3 py-1 text-xs text-violet-800 hover:bg-violet-100">{{ $relationTypeLabel }} Â· Makna {{ $relation->relatedSense->sense_number }}</a>
                                                @else
                                                    <a href="{{ route('dictionary.show', ['lemma' => $relation->relatedSense->lemma->public_id]) }}" class="rounded-full bg-violet-50 px-3 py-1 text-xs text-violet-800 hover:bg-violet-100">{{ $relationTypeLabel }} Â· {{ $relation->relatedSense->lemma->headword }}</a>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif

                                    @if ($sense->examples->isNotEmpty())
                                        <div class="mt-5 border-t border-slate-200 pt-4">
                                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-500">Contoh penggunaan</h3>
                                            <div class="mt-3 grid gap-3">
                                                @foreach ($sense->examples as $example)
                                                    <blockquote class="rounded-lg border-l-4 border-emerald-500 bg-emerald-50/50 p-4">
                                                        <p class="font-medium leading-6 text-slate-950">{{ $example->tolaki_text }}</p>
                                                        @foreach ($example->translations as $exampleTranslation)
                                                            <p class="mt-2 text-sm leading-6 text-slate-700"><span class="mr-2 inline-block rounded bg-white px-1.5 py-0.5 text-[10px] font-bold text-slate-500">{{ strtoupper($exampleTranslation->language_code) }}</span>{{ $exampleTranslation->text }}</p>
                                                        @endforeach
                                                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                                                            @if ($example->region_label)<span>Wilayah: {{ $example->region_label }}</span>@endif
                                                        </div>
                                                    </blockquote>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </section>
                        @endforeach
                    </div>
                </section>

                @if (count($sourceReferences))
                    <section aria-labelledby="sources-heading" class="border-t border-slate-200 pt-6">
                        <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Keterlacakan</p>
                        <h2 id="sources-heading" class="mt-1 text-xl font-semibold text-slate-950">Sumber</h2>
                        <ol class="mt-4 space-y-3">
                            @foreach ($sourceReferences as $reference)
                                <li class="rounded-lg border border-slate-200 p-4">
                                    <p class="text-sm font-medium leading-6 text-slate-800">{{ $reference['citation'] }}</p>
                                    <p class="mt-2 text-xs leading-5 text-slate-500">Digunakan untuk: {{ implode('; ', array_unique($reference['uses'])) }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @endif
            </div>
        </article>
    </section>
@endsection
