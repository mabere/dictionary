<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DictionaryCategory;
use App\Models\DictionaryExample;
use App\Models\DictionaryExampleTranslation;
use App\Models\DictionaryLemma;
use App\Models\DictionaryLemmaForm;
use App\Models\DictionaryMorphologicalAnalysis;
use App\Models\DictionaryPronunciation;
use App\Models\DictionarySense;
use App\Models\DictionarySenseRelation;
use App\Models\DictionarySource;
use App\Models\DictionaryTranslation;
use App\Models\OrthographyProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LexicographicImportExportController extends Controller
{
    private const HEADERS = [
        'schema_version',
        'public_id',
        'headword',
        'status',
        'orthography_profile',
        'forms_json',
        'senses_json',
        'pronunciations_json',
    ];

    public function create(): View
    {
        $this->authorizeDictionaryEditor();

        return view('admin.dictionary.import-v2');
    }

    public function template(): StreamedResponse
    {
        $this->authorizeDictionaryEditor();

        return response()->streamDownload(function (): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, self::HEADERS);
            fclose($output);
        }, 'dictionary-lexicographic-v1-template.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function export(): StreamedResponse
    {
        $this->authorizeDictionaryEditor();
        $user = auth()->user();

        return response()->streamDownload(function () use ($user): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, self::HEADERS);

            DictionaryLemma::query()
                ->when(! $user->isDictionaryAdmin(), fn (Builder $query) => $query->where('created_by', $user->getKey()))
                ->with([
                    'orthographyProfile',
                    'forms.source',
                    'forms.analyses.baseLemma',
                    'forms.analyses.source',
                    'senses.category',
                    'senses.source',
                    'senses.translations.source',
                    'senses.examples.source',
                    'senses.examples.translations.source',
                    'senses.relations.relatedSense',
                    'senses.relations.source',
                    'pronunciations.source',
                ])
                ->orderBy('headword_normalized')
                ->chunkById(100, function ($lemmas) use ($output): void {
                    foreach ($lemmas as $lemma) {
                        $forms = $lemma->forms->map(fn (DictionaryLemmaForm $form): array => [
                            'form' => $form->form,
                            'form_type' => $form->form_type,
                            'region_label' => $form->region_label,
                            'notes' => $form->notes,
                            'source_citation' => $form->source?->citation,
                            'analyses' => $form->analyses->map(fn (DictionaryMorphologicalAnalysis $analysis): array => [
                                'base_lemma_public_id' => $analysis->baseLemma?->public_id,
                                'segmentation' => $analysis->segmentation,
                                'morphemes' => $analysis->morphemes,
                                'analysis_note' => $analysis->analysis_note,
                                'status' => $analysis->status,
                                'source_citation' => $analysis->source?->citation,
                            ])->all(),
                        ])->all();

                        $senses = $lemma->senses->map(fn (DictionarySense $sense): array => [
                            'sense_number' => $sense->sense_number,
                            'definition' => $sense->definition,
                            'definition_tolaki' => $sense->definition_tolaki,
                            'definition_english' => $sense->definition_english,
                            'definition_language' => $sense->definition_language,
                            'part_of_speech' => $sense->part_of_speech,
                            'category_slug' => $sense->category?->slug,
                            'category_name' => $sense->category?->name,
                            'grammatical_features' => $sense->grammatical_features,
                            'domain_label' => $sense->domain_label,
                            'register_label' => $sense->register_label,
                            'usage_note' => $sense->usage_note,
                            'cultural_note' => $sense->cultural_note,
                            'status' => $sense->status,
                            'source_citation' => $sense->source?->citation,
                            'translations' => $sense->translations->map(fn (DictionaryTranslation $translation): array => [
                                'language_code' => $translation->language_code,
                                'form' => $translation->form,
                                'translation_type' => $translation->translation_type,
                                'context_note' => $translation->context_note,
                                'status' => $translation->status,
                                'source_citation' => $translation->source?->citation,
                            ])->all(),
                            'examples' => $sense->examples->map(fn (DictionaryExample $example): array => [
                                'tolaki_text' => $example->tolaki_text,
                                'region_label' => $example->region_label,
                                'evidence_method' => $example->evidence_method,
                                'elicitation_note' => $example->elicitation_note,
                                'status' => $example->status,
                                'source_citation' => $example->source?->citation,
                                'translations' => $example->translations->map(fn (DictionaryExampleTranslation $translation): array => [
                                    'language_code' => $translation->language_code,
                                    'text' => $translation->text,
                                    'translation_note' => $translation->translation_note,
                                    'status' => $translation->status,
                                    'source_citation' => $translation->source?->citation,
                                ])->all(),
                            ])->all(),
                            'relations' => $sense->relations->map(fn (DictionarySenseRelation $relation): array => [
                                'related_sense_number' => $relation->relatedSense?->sense_number,
                                'relation_type' => $relation->relation_type,
                                'note' => $relation->note,
                                'source_citation' => $relation->source?->citation,
                            ])->all(),
                        ])->all();

                        $pronunciations = $lemma->pronunciations->map(fn (DictionaryPronunciation $pronunciation): array => [
                            'transcription' => $pronunciation->transcription,
                            'transcription_system' => $pronunciation->transcription_system,
                            'audio_path' => $pronunciation->audio_path,
                            'region_label' => $pronunciation->region_label,
                            'rights_status' => $pronunciation->rights_status,
                            'status' => $pronunciation->status,
                            'source_citation' => $pronunciation->source?->citation,
                        ])->all();

                        fputcsv($output, $this->escapeCsvRow([
                            '1',
                            $lemma->public_id,
                            $lemma->headword,
                            $lemma->status,
                            $lemma->orthographyProfile?->name.'|'.$lemma->orthographyProfile?->version,
                            $this->encodeJson($forms),
                            $this->encodeJson($senses),
                            $this->encodeJson($pronunciations),
                        ]));
                    }
                });

            fclose($output);
        }, 'dictionary-lexicographic-v1.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeDictionaryEditor();

        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:20480'],
        ]);

        $handle = fopen($validated['csv_file']->getRealPath(), 'rb');
        $header = fgetcsv($handle) ?: [];
        $header = array_map(fn ($value): string => trim((string) $value), $header);
        $header[0] = isset($header[0]) ? preg_replace('/^\xEF\xBB\xBF/', '', $header[0]) : null;

        if ($header !== self::HEADERS) {
            fclose($handle);

            return back()->withErrors(['csv_file' => 'Header CSV v1 harus tepat: '.implode(', ', self::HEADERS)]);
        }

        $records = [];
        $line = 1;
        while (($row = fgetcsv($handle)) !== false) {
            $line++;
            if ($row === [null] || $row === []) {
                continue;
            }

            if (count($row) !== count(self::HEADERS)) {
                fclose($handle);

                return back()->withErrors(['csv_file' => "Jumlah kolom tidak sesuai pada baris {$line}."]);
            }

            $record = array_combine(self::HEADERS, $row);
            try {
                $record['forms'] = $this->decodeList($record['forms_json'], 'forms_json', $line);
                $record['senses'] = $this->decodeList($record['senses_json'], 'senses_json', $line);
                $record['pronunciations'] = $this->decodeList($record['pronunciations_json'], 'pronunciations_json', $line);
                $this->validateRecord($record, $line);
            } catch (ValidationException $exception) {
                fclose($handle);

                return back()->withErrors(['csv_file' => $exception->getMessage()]);
            }

            $records[] = $record;
        }

        fclose($handle);

        $ids = collect($records)->pluck('public_id')->filter()->all();
        if (count($ids) !== count(array_unique($ids))) {
            return back()->withErrors(['csv_file' => 'CSV memuat public_id yang berulang. Ekspor satu baris untuk setiap lema.']);
        }

        $created = 0;
        $updated = 0;
        DB::transaction(function () use ($records, &$created, &$updated): void {
            foreach ($records as $record) {
                $lemma = filled($record['public_id'])
                    ? DictionaryLemma::query()->where('public_id', $record['public_id'])->first()
                    : null;

                if ($lemma && ! auth()->user()->isDictionaryAdmin() && (int) $lemma->created_by !== (int) auth()->id()) {
                    throw ValidationException::withMessages(['csv_file' => 'CSV mencoba memperbarui lema milik pengguna lain.']);
                }

                $isNew = ! $lemma;
                $status = $this->importStatus($record['status']);
                $profile = $this->findOrthographyProfile($record['orthography_profile']);
                $lemma ??= new DictionaryLemma;
                $lemma->fill([
                    'public_id' => $record['public_id'] ?: null,
                    'headword' => trim($record['headword']),
                    'orthography_profile_id' => $profile?->getKey(),
                    'status' => $status,
                    'created_by' => $isNew ? auth()->id() : $lemma->created_by,
                ])->save();

                foreach ($record['forms'] as $formData) {
                    $this->importForm($lemma, $formData);
                }

                $sensesByNumber = [];
                foreach ($record['senses'] as $senseData) {
                    $sense = $lemma->senses()->firstOrNew(['sense_number' => (int) $senseData['sense_number']]);
                    $sense->fill([
                        'definition' => $senseData['definition'] ?? null,
                        'definition_tolaki' => $senseData['definition_tolaki'] ?? null,
                        'definition_english' => $senseData['definition_english'] ?? null,
                        'definition_language' => $senseData['definition_language'] ?? 'id',
                        'part_of_speech' => $senseData['part_of_speech'] ?? null,
                        'category_id' => $this->importCategory($senseData['category_slug'] ?? null, $senseData['category_name'] ?? null),
                        'grammatical_features' => $senseData['grammatical_features'] ?? null,
                        'domain_label' => $senseData['domain_label'] ?? null,
                        'register_label' => $senseData['register_label'] ?? null,
                        'usage_note' => $senseData['usage_note'] ?? null,
                        'cultural_note' => $senseData['cultural_note'] ?? null,
                        'status' => $status,
                        'source_id' => $this->importSource($senseData['source_citation'] ?? null),
                        'created_by' => $isNew ? auth()->id() : $sense->created_by,
                    ])->save();
                    $sensesByNumber[$sense->sense_number] = $sense;

                    foreach ($senseData['translations'] ?? [] as $translationData) {
                        $this->importTranslation($sense, $translationData, $status, $isNew);
                    }

                    foreach ($senseData['examples'] ?? [] as $exampleData) {
                        $this->importExample($sense, $exampleData, $status, $isNew);
                    }
                }

                foreach ($record['senses'] as $senseData) {
                    $sense = $sensesByNumber[(int) $senseData['sense_number']] ?? null;
                    if (! $sense) {
                        continue;
                    }

                    foreach ($senseData['relations'] ?? [] as $relationData) {
                        $relatedSense = $sensesByNumber[(int) ($relationData['related_sense_number'] ?? 0)] ?? null;
                        if (! $relatedSense || $relatedSense->is($sense)) {
                            continue;
                        }

                        DictionarySenseRelation::query()->firstOrCreate([
                            'sense_id' => $sense->getKey(),
                            'related_sense_id' => $relatedSense->getKey(),
                            'relation_type' => $relationData['relation_type'] ?? 'related',
                        ], [
                            'note' => $relationData['note'] ?? null,
                            'source_id' => $this->importSource($relationData['source_citation'] ?? null),
                        ]);
                    }
                }

                foreach ($record['pronunciations'] as $pronunciationData) {
                    $this->importPronunciation($lemma, $pronunciationData, $status, $isNew);
                }

                $isNew ? $created++ : $updated++;
            }
        });

        return redirect()
            ->route('admin.lexicographic-import')
            ->with('status', "{$created} lema ditambahkan dan {$updated} lema diperbarui. Data turunan yang tidak ada di berkas tidak dihapus.");
    }

    private function validateRecord(array $record, int $line): void
    {
        if (($record['schema_version'] ?? '') !== '1') {
            throw ValidationException::withMessages(['csv_file' => "Versi skema tidak dikenal pada baris {$line}; gunakan versi 1."]);
        }

        if (blank($record['headword'] ?? null) || $record['senses'] === []) {
            throw ValidationException::withMessages(['csv_file' => "Lema dan minimal satu makna wajib diisi pada baris {$line}."]);
        }

        if (filled($record['public_id']) && ! Str::isUlid($record['public_id'])) {
            throw ValidationException::withMessages(['csv_file' => "public_id bukan ULID yang valid pada baris {$line}."]);
        }

        foreach (['forms', 'senses', 'pronunciations'] as $listKey) {
            foreach ($record[$listKey] as $item) {
                if (! is_array($item)) {
                    throw ValidationException::withMessages(['csv_file' => "Setiap item {$listKey} harus berupa objek JSON pada baris {$line}."]);
                }
            }
        }

        $numbers = [];
        foreach ($record['senses'] as $sense) {
            if (! is_array($sense) || empty($sense['sense_number']) || ! is_numeric($sense['sense_number'])) {
                throw ValidationException::withMessages(['csv_file' => "sense_number tidak valid pada baris {$line}."]);
            }

            $number = (int) $sense['sense_number'];
            if ($number < 1 || in_array($number, $numbers, true)) {
                throw ValidationException::withMessages(['csv_file' => "Nomor makna harus positif dan unik pada baris {$line}."]);
            }
            $numbers[] = $number;

            foreach (['translations', 'examples', 'relations'] as $nestedKey) {
                if (isset($sense[$nestedKey]) && ! is_array($sense[$nestedKey])) {
                    throw ValidationException::withMessages(['csv_file' => "Struktur {$nestedKey} tidak valid pada baris {$line}."]);
                }
                foreach ($sense[$nestedKey] ?? [] as $nestedItem) {
                    if (! is_array($nestedItem)) {
                        throw ValidationException::withMessages(['csv_file' => "Setiap item {$nestedKey} harus berupa objek JSON pada baris {$line}."]);
                    }
                }
            }
            foreach ($sense['examples'] ?? [] as $example) {
                if (isset($example['translations']) && ! is_array($example['translations'])) {
                    throw ValidationException::withMessages(['csv_file' => "Struktur terjemahan contoh tidak valid pada baris {$line}."]);
                }
                foreach ($example['translations'] ?? [] as $translation) {
                    if (! is_array($translation)) {
                        throw ValidationException::withMessages(['csv_file' => "Setiap terjemahan contoh harus berupa objek JSON pada baris {$line}."]);
                    }
                }
            }
            foreach ($record['forms'] as $form) {
                if (isset($form['analyses']) && ! is_array($form['analyses'])) {
                    throw ValidationException::withMessages(['csv_file' => "Struktur analyses tidak valid pada baris {$line}."]);
                }
                foreach ($form['analyses'] ?? [] as $analysis) {
                    if (! is_array($analysis)) {
                        throw ValidationException::withMessages(['csv_file' => "Setiap analisis morfologi harus berupa objek JSON pada baris {$line}."]);
                    }
                }
            }
        }
        foreach ($record['pronunciations'] as $pronunciation) {
            if (! is_array($pronunciation)) {
                throw ValidationException::withMessages(['csv_file' => "Setiap pelafalan harus berupa objek JSON pada baris {$line}."]);
            }
        }
    }

    private function decodeList(?string $json, string $field, int $line): array
    {
        if (blank($json)) {
            return [];
        }

        try {
            $decoded = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            throw ValidationException::withMessages(['csv_file' => "JSON {$field} tidak valid pada baris {$line}."]);
        }

        if (! is_array($decoded) || ! array_is_list($decoded)) {
            throw ValidationException::withMessages(['csv_file' => "{$field} harus berupa array JSON pada baris {$line}."]);
        }

        return $decoded;
    }

    private function importForm(DictionaryLemma $lemma, array $data): void
    {
        if (blank($data['form'] ?? null)) {
            return;
        }

        $type = $data['form_type'] ?? 'variant';
        $form = $lemma->forms()->firstOrNew([
            'form_normalized' => DictionaryLemma::normalizeForm($data['form']),
            'form_type' => $type,
        ]);
        $form->fill([
            'form' => $data['form'],
            'region_label' => $data['region_label'] ?? null,
            'notes' => $data['notes'] ?? null,
            'source_id' => $this->importSource($data['source_citation'] ?? null),
        ])->save();

        foreach ($data['analyses'] ?? [] as $analysisData) {
            if (! is_array($analysisData)) {
                continue;
            }

            $baseLemma = filled($analysisData['base_lemma_public_id'] ?? null)
                ? DictionaryLemma::query()->where('public_id', $analysisData['base_lemma_public_id'])->first()
                : null;
            $analysis = $form->analyses()->firstOrNew([
                'segmentation' => $analysisData['segmentation'] ?? null,
                'base_lemma_id' => $baseLemma?->getKey(),
            ]);
            $analysis->fill([
                'morphemes' => $analysisData['morphemes'] ?? null,
                'analysis_note' => $analysisData['analysis_note'] ?? null,
                'status' => $this->importStatus($analysisData['status'] ?? 'draft'),
                'source_id' => $this->importSource($analysisData['source_citation'] ?? null),
                'created_by' => $analysis->exists ? $analysis->created_by : auth()->id(),
            ])->save();
        }
    }

    private function importTranslation(DictionarySense $sense, array $data, string $parentStatus, bool $isNew): void
    {
        $language = strtolower((string) ($data['language_code'] ?? ''));
        if (! in_array($language, ['id', 'en'], true) || blank($data['form'] ?? null)) {
            return;
        }

        $form = DictionaryLemma::normalizeForm($data['form']);
        $type = $data['translation_type'] ?? 'equivalent';
        $query = $sense->translations()->where('language_code', $language)
            ->where('form_normalized', $form)
            ->where('translation_type', $type);
        $context = $data['context_note'] ?? null;
        $query = $context === null ? $query->whereNull('context_note') : $query->where('context_note', $context);
        $translation = $query->first() ?? new DictionaryTranslation(['sense_id' => $sense->getKey()]);
        $translation->fill([
            'language_code' => $language,
            'form' => $data['form'],
            'form_normalized' => $form,
            'translation_type' => $type,
            'context_note' => $context,
            'status' => $this->importStatus($data['status'] ?? $parentStatus),
            'source_id' => $this->importSource($data['source_citation'] ?? null),
            'created_by' => $isNew ? auth()->id() : $translation->created_by,
        ])->save();
    }

    private function importExample(DictionarySense $sense, array $data, string $parentStatus, bool $isNew): void
    {
        if (blank($data['tolaki_text'] ?? null)) {
            return;
        }

        $sourceId = $this->importSource($data['source_citation'] ?? null);
        $query = $sense->examples()->where('tolaki_text', $data['tolaki_text']);
        $query = $sourceId ? $query->where('source_id', $sourceId) : $query->whereNull('source_id');
        $example = $query->first() ?? new DictionaryExample(['sense_id' => $sense->getKey()]);
        $example->fill([
            'tolaki_text' => $data['tolaki_text'],
            'region_label' => $data['region_label'] ?? null,
            'evidence_method' => $data['evidence_method'] ?? 'unknown',
            'elicitation_note' => $data['elicitation_note'] ?? null,
            'status' => $this->importStatus($data['status'] ?? $parentStatus),
            'source_id' => $sourceId,
            'created_by' => $isNew ? auth()->id() : $example->created_by,
        ])->save();

        foreach ($data['translations'] ?? [] as $translationData) {
            $language = strtolower((string) ($translationData['language_code'] ?? ''));
            if (! in_array($language, ['id', 'en'], true) || blank($translationData['text'] ?? null)) {
                continue;
            }

            $translationSourceId = $this->importSource($translationData['source_citation'] ?? null);
            $query = $example->translations()->where('language_code', $language)->where('text', $translationData['text']);
            $query = $translationSourceId ? $query->where('source_id', $translationSourceId) : $query->whereNull('source_id');
            $translation = $query->first() ?? new DictionaryExampleTranslation(['example_id' => $example->getKey()]);
            $translation->fill([
                'language_code' => $language,
                'text' => $translationData['text'],
                'translation_note' => $translationData['translation_note'] ?? null,
                'status' => $this->importStatus($translationData['status'] ?? $parentStatus),
                'source_id' => $translationSourceId,
                'created_by' => $isNew ? auth()->id() : $translation->created_by,
            ])->save();
        }
    }

    private function importPronunciation(DictionaryLemma $lemma, array $data, string $parentStatus, bool $isNew): void
    {
        if (blank($data['transcription'] ?? null) && blank($data['audio_path'] ?? null)) {
            return;
        }

        $sourceId = $this->importSource($data['source_citation'] ?? null);
        $query = $lemma->pronunciations();
        foreach (['transcription', 'transcription_system', 'audio_path'] as $field) {
            $value = $data[$field] ?? null;
            $query = $value === null ? $query->whereNull($field) : $query->where($field, $value);
        }
        $query = $sourceId ? $query->where('source_id', $sourceId) : $query->whereNull('source_id');
        $pronunciation = $query->first() ?? new DictionaryPronunciation(['lemma_id' => $lemma->getKey()]);
        $pronunciation->fill([
            'transcription' => $data['transcription'] ?? null,
            'transcription_system' => $data['transcription_system'] ?? null,
            'audio_path' => $data['audio_path'] ?? null,
            'region_label' => $data['region_label'] ?? null,
            'rights_status' => 'unknown',
            'status' => $this->importStatus($data['status'] ?? $parentStatus),
            'source_id' => $sourceId,
            'created_by' => $isNew ? auth()->id() : $pronunciation->created_by,
        ])->save();
    }

    private function importCategory(?string $slug, ?string $name): ?int
    {
        if (blank($slug) || blank($name)) {
            return null;
        }

        return DictionaryCategory::query()->firstOrCreate(
            ['slug' => Str::slug($slug)],
            ['name' => trim($name)],
        )->getKey();
    }

    private function importSource(?string $citation): ?int
    {
        if (blank($citation)) {
            return null;
        }

        $source = DictionarySource::query()->where('citation', trim($citation))->first();
        if ($source) {
            return $source->getKey();
        }

        return DictionarySource::query()->create([
            'source_type' => 'imported_reference',
            'citation' => trim($citation),
            'rights_status' => 'unknown',
            'visibility' => 'internal',
            'notes' => 'Imported as an unverified citation reference. Review rights and visibility before public use.',
            'created_by' => auth()->id(),
        ])->getKey();
    }

    private function findOrthographyProfile(?string $nameAndVersion): ?OrthographyProfile
    {
        if (blank($nameAndVersion) || ! str_contains($nameAndVersion, '|')) {
            return null;
        }

        [$name, $version] = explode('|', $nameAndVersion, 2);

        return OrthographyProfile::query()->where('name', $name)->where('version', $version)->first();
    }

    private function importStatus(?string $status): string
    {
        $valid = ['draft', 'submitted', 'reviewed', 'published', 'rejected'];
        $status = in_array($status, $valid, true) ? $status : 'draft';

        if (! auth()->user()->isDictionaryAdmin() && $status === 'published') {
            return 'reviewed';
        }

        return $status;
    }

    private function encodeJson(array $value): string
    {
        return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }

    private function authorizeDictionaryEditor(): void
    {
        abort_unless(auth()->check() && in_array(auth()->user()->role, ['admin', 'editor'], true), 403);
    }

    private function escapeCsvRow(array $row): array
    {
        return array_map(function (mixed $value): mixed {
            if (! is_string($value) || $value === '') {
                return $value;
            }

            return in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true) ? "'{$value}" : $value;
        }, $row);
    }
}
