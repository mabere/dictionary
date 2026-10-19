<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DictionaryCategory;
use App\Models\DictionaryEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DictionaryEntryImportExportController extends Controller
{
    public function create(): View
    {
        $this->authorizeDictionaryAdmin();

        return view('admin.dictionary.import');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeDictionaryAdmin();

        $validated = $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:10240'],
        ]);

        $handle = fopen($validated['csv_file']->getRealPath(), 'rb');
        $header = fgetcsv($handle) ?: [];
        $header = array_map(fn ($value) => trim((string) $value), $header);
        $header[0] = isset($header[0]) ? preg_replace('/^\xEF\xBB\xBF/', '', $header[0]) : null;

        $missingHeaders = array_diff($this->headers(), $header);
        $extraHeaders = array_diff($header, $this->headers());

        if ($missingHeaders !== [] || $extraHeaders !== []) {
            fclose($handle);

            $message = 'Header CSV tidak sesuai template.';

            if ($missingHeaders !== []) {
                $message .= ' Kolom hilang: '.implode(', ', $missingHeaders).'.';
            }

            if ($extraHeaders !== []) {
                $message .= ' Kolom tidak dikenal: '.implode(', ', $extraHeaders).'.';
            }

            return back()
                ->withErrors(['csv_file' => $message])
                ->withInput();
        }

        $created = 0;
        $skipped = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, array_slice(array_pad($row, count($header), null), 0, count($header)));
            $data = $this->cleanCsvData($data ?: []);

            if (! $data || blank($data['tolaki_word'] ?? null) || blank($data['indonesian_word'] ?? null)) {
                $skipped++;

                continue;
            }

            $duplicateKey = DictionaryEntry::makeDuplicateKey(
                $data['tolaki_word'] ?? null,
                $data['indonesian_word'] ?? null,
                'Tolaki',
                'Indonesia',
                $data['part_of_speech'] ?? null,
            );

            if (DictionaryEntry::query()->where('created_by', auth()->id())->where('duplicate_key', $duplicateKey)->exists()) {
                $skipped++;

                continue;
            }

            $categoryId = null;

            if (filled($data['category'] ?? null)) {
                $category = DictionaryCategory::firstOrCreate(
                    ['slug' => str($data['category'])->slug()->toString()],
                    ['name' => $data['category']]
                );

                $categoryId = $category->id;
            }

            $status = in_array($data['status'] ?? null, [
                DictionaryEntry::STATUS_DRAFT,
                DictionaryEntry::STATUS_SUBMITTED,
                DictionaryEntry::STATUS_REVIEWED,
                DictionaryEntry::STATUS_PUBLISHED,
                DictionaryEntry::STATUS_REJECTED,
            ], true) ? $data['status'] : DictionaryEntry::STATUS_DRAFT;

            if ($status === DictionaryEntry::STATUS_PUBLISHED && ! auth()->user()->isDictionaryAdmin()) {
                $status = DictionaryEntry::STATUS_REVIEWED;
            }

            $entry = DictionaryEntry::create([
                'tolaki_word' => $data['tolaki_word'],
                'indonesian_word' => $data['indonesian_word'],
                'english_word' => $data['english_word'] ?? null,
                'part_of_speech' => $data['part_of_speech'] ?: null,
                'phonetic' => $data['phonetic'] ?? null,
                'definition' => $data['definition'] ?? null,
                'usage_note' => $data['usage_note'] ?? null,
                'cultural_note' => $data['cultural_note'] ?? null,
                'category_id' => $categoryId,
                'status' => $status,
                'created_by' => auth()->id(),
            ]);

            if (filled($data['tolaki_sentence'] ?? null) && filled($data['indonesian_sentence'] ?? null)) {
                $entry->examples()->create([
                    'tolaki_sentence' => $data['tolaki_sentence'],
                    'indonesian_sentence' => $data['indonesian_sentence'],
                    'english_sentence' => $data['english_sentence'] ?? null,
                    'notes' => $data['example_notes'] ?? null,
                ]);
            }

            $created++;
        }

        fclose($handle);

        return redirect()
            ->route('admin.dictionary-entries.import')
            ->with('status', "{$created} entri berhasil diimpor. {$skipped} baris dilewati.");
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string|null>
     */
    private function cleanCsvData(array $data): array
    {
        return collect($data)
            ->map(fn (mixed $value): ?string => blank($value) ? null : str((string) $value)->squish()->toString())
            ->all();
    }

    public function export(): StreamedResponse
    {
        $this->authorizeDictionaryAdmin();

        return response()->streamDownload(function () {
            $output = fopen('php://output', 'wb');

            fputcsv($output, $this->headers());

            DictionaryEntry::query()->where('created_by', auth()->id())
                ->with(['category', 'examples'])
                ->orderBy('tolaki_word')
                ->chunk(200, function ($entries) use ($output) {
                    foreach ($entries as $entry) {
                        $example = $entry->examples->first();

                        fputcsv($output, $this->escapeCsvRow([
                            $entry->tolaki_word,
                            $entry->indonesian_word,
                            $entry->english_word,
                            $entry->part_of_speech,
                            $entry->phonetic,
                            $entry->definition,
                            $entry->usage_note,
                            $entry->cultural_note,
                            $entry->category?->name,
                            $entry->status,
                            $example?->tolaki_sentence,
                            $example?->indonesian_sentence,
                            $example?->english_sentence,
                            $example?->notes,
                        ]));
                    }
                });

            fclose($output);
        }, 'dictionary-entries.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function template(): StreamedResponse
    {
        $this->authorizeDictionaryAdmin();

        return response()->streamDownload(function () {
            $output = fopen('php://output', 'wb');
            fputcsv($output, $this->headers());
            fclose($output);
        }, 'dictionary-import-template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @return array<int, string>
     */
    private function headers(): array
    {
        return [
            'tolaki_word',
            'indonesian_word',
            'english_word',
            'part_of_speech',
            'phonetic',
            'definition',
            'usage_note',
            'cultural_note',
            'category',
            'status',
            'tolaki_sentence',
            'indonesian_sentence',
            'english_sentence',
            'example_notes',
        ];
    }

    private function authorizeDictionaryAdmin(): void
    {
        abort_unless(auth()->check() && in_array(auth()->user()->role, ['admin', 'editor'], true), 403);
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array<int, mixed>
     */
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
