<?php

namespace App\Http\Controllers;

use App\Models\DictionaryCategory;
use App\Models\DictionaryEntry;
use App\Models\DictionaryLemma;
use App\Models\DictionarySense;
use App\Models\OrthographyProfile;
use App\Support\DictionaryVisitRecorder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class PublicDictionaryController extends Controller
{
    private const TOLAKI_INITIALS = ['a', 'b', 'd', 'e', 'g', 'h', 'i', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'w'];

    public function index(Request $request, DictionaryVisitRecorder $visitRecorder): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', 'integer', 'exists:dictionary_categories,id'],
            'part_of_speech' => ['nullable', 'string', 'max:64'],
            'direction' => ['nullable', 'in:both,tolaki,indonesian,english,source,target'],
            'letter' => ['nullable', 'string', 'max:1'],
        ]);

        $keyword = trim((string) ($validated['q'] ?? ''));
        $categoryId = $validated['category'] ?? null;
        $partOfSpeech = $validated['part_of_speech'] ?? null;
        $direction = match ($validated['direction'] ?? 'both') {
            'source' => 'tolaki',
            'target' => 'both',
            default => $validated['direction'] ?? 'both',
        };
        $letter = DictionaryLemma::normalizeForm((string) ($validated['letter'] ?? ''));

        $entries = DictionaryLemma::query()
            ->published()
            ->with([
                'senses' => fn ($query) => $query->where('status', 'published'),
                'senses.translations' => fn ($query) => $query->where('status', 'published'),
                'senses.examples' => fn ($query) => $query->where('status', 'published'),
            ])
            ->when($keyword !== '', function (Builder $query) use ($keyword, $direction): void {
                $normalized = DictionaryLemma::normalizeForm($keyword);
                $escapedNormalized = addcslashes($normalized, '%_\\');
                $escapedKeyword = addcslashes($keyword, '%_\\');

                $query->where(function (Builder $search) use ($escapedKeyword, $escapedNormalized, $direction): void {
                    if (in_array($direction, ['tolaki', 'both'], true)) {
                        $search->where('headword_normalized', 'like', "%{$escapedNormalized}%")
                            ->orWhereHas('forms', fn (Builder $forms) => $forms->where('form_normalized', 'like', "%{$escapedNormalized}%"));

                        $search->orWhereHas('senses', fn (Builder $senses) => $senses
                            ->where('status', 'published')
                            ->where(fn (Builder $sq) => $sq
                                ->where('definition_tolaki', 'like', "%{$escapedKeyword}%")
                                ->orWhere(fn (Builder $legacy) => $legacy->where('definition_language', 'tol')->where('definition', 'like', "%{$escapedKeyword}%"))));
                    }

                    if (in_array($direction, ['indonesian', 'both'], true)) {
                        $search->orWhereHas('senses', fn (Builder $senses) => $senses
                            ->where('status', 'published')
                            ->where(function (Builder $sq) use ($escapedNormalized, $escapedKeyword): void {
                                $sq->whereHas('translations', fn (Builder $translations) => $translations
                                    ->where('status', 'published')
                                    ->where('language_code', 'id')
                                    ->where('form_normalized', 'like', "%{$escapedNormalized}%"))
                                    ->orWhere(fn (Builder $def) => $def
                                        ->where('definition_language', 'id')
                                        ->where('definition', 'like', "%{$escapedKeyword}%"));
                            }));
                    }

                    if (in_array($direction, ['english', 'both'], true)) {
                        $search->orWhereHas('senses', fn (Builder $senses) => $senses
                            ->where('status', 'published')
                            ->where(function (Builder $sq) use ($escapedNormalized, $escapedKeyword): void {
                                $sq->whereHas('translations', fn (Builder $translations) => $translations
                                    ->where('status', 'published')
                                    ->where('language_code', 'en')
                                    ->where('form_normalized', 'like', "%{$escapedNormalized}%"))
                                    ->orWhere(fn (Builder $def) => $def
                                        ->where('definition_english', 'like', "%{$escapedKeyword}%")
                                        ->orWhere(fn (Builder $legacy) => $legacy->where('definition_language', 'en')->where('definition', 'like', "%{$escapedKeyword}%")));
                            }));
                    }

                    if ($direction === 'both') {
                        $search->orWhereHas('senses', fn (Builder $senses) => $senses
                            ->where('status', 'published')
                            ->where(function (Builder $definitions) use ($escapedKeyword): void {
                                $definitions->where('definition', 'like', "%{$escapedKeyword}%")
                                    ->orWhere('definition_tolaki', 'like', "%{$escapedKeyword}%")
                                    ->orWhere('definition_english', 'like', "%{$escapedKeyword}%");
                            }));
                    }

                });
            })
            ->when($categoryId, fn (Builder $query) => $query->whereHas('senses', fn (Builder $senses) => $senses->where('category_id', $categoryId)))
            ->when($partOfSpeech, fn (Builder $query) => $query->whereHas('senses', fn (Builder $senses) => $senses->where('part_of_speech', $partOfSpeech)))
            ->when($letter !== '', fn (Builder $query) => $query->where('headword_normalized', 'like', "{$letter}%"))
            ->orderBy('headword_normalized')
            ->paginate(12)
            ->withQueryString();

        $visitRecorder->record($request, keyword: $keyword, direction: $direction);

        $activeOrthography = OrthographyProfile::query()
            ->where('status', 'active')
            ->latest('id')
            ->first();
        $letters = data_get($activeOrthography?->rules, 'initials', self::TOLAKI_INITIALS);
        $letters = is_array($letters) ? array_values(array_filter(array_map(
            fn (mixed $initial): ?string => is_string($initial) && mb_strlen(trim($initial)) === 1
                ? mb_strtolower(trim($initial))
                : null,
            $letters,
        ))) : self::TOLAKI_INITIALS;

        if ($letters === []) {
            $letters = self::TOLAKI_INITIALS;
        }

        return view('dictionary.index', [
            'entries' => $entries,
            'categories' => DictionaryCategory::query()->orderBy('name')->get(),
            'partsOfSpeech' => DictionarySense::query()
                ->whereNotNull('part_of_speech')
                ->whereHas('lemma', fn (Builder $lemma) => $lemma->where('status', 'published'))
                ->distinct()
                ->orderBy('part_of_speech')
                ->pluck('part_of_speech'),
            'filters' => [
                'q' => $keyword,
                'category' => $categoryId,
                'part_of_speech' => $partOfSpeech,
                'direction' => $direction,
                'letter' => $letter,
            ],
            'letters' => $letters,
            'alphabetProfile' => $activeOrthography?->name ?? 'Profil kandidat Kamus TolakiÃ¢â‚¬â€œIndonesia 1985',
            'alphabetProfileIsActive' => (bool) $activeOrthography,
        ]);
    }

    public function show(Request $request, DictionaryLemma $lemma, DictionaryVisitRecorder $visitRecorder): View
    {
        abort_unless($lemma->status === DictionaryEntry::STATUS_PUBLISHED, 404);

        $visitRecorder->recordLemma($request, $lemma);

        return view('dictionary.show', [
            'lemma' => $lemma->load([
                'orthographyProfile',
                'forms.source',
                'forms.analyses' => fn ($query) => $query->where('status', 'reviewed'),
                'forms.analyses.source',
                'forms.analyses.baseLemma',
                'senses' => fn ($query) => $query->where('status', 'published'),
                'senses.category',
                'senses.source',
                'senses.translations' => fn ($query) => $query->where('status', 'published'),
                'senses.translations.source',
                'senses.relations' => fn ($query) => $query->whereHas('relatedSense.lemma', fn (Builder $lemmaQuery) => $lemmaQuery->where('status', 'published')),
                'senses.relations.relatedSense',
                'senses.relations.relatedSense.lemma',
                'senses.relations.source',
                'senses.examples' => fn ($query) => $query->where('status', 'published'),
                'senses.examples.source',
                'senses.examples.translations' => fn ($query) => $query->where('status', 'published'),
                'senses.examples.translations.source',
                'pronunciations' => fn ($query) => $query->where('status', 'published'),
                'pronunciations.source',
            ]),
        ]);
    }
}
