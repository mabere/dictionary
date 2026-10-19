<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDictionaryContributionRequest;
use App\Models\DictionaryCategory;
use App\Models\DictionaryEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DictionaryContributionController extends Controller
{
    public function create(): View
    {
        session()->put('dictionary_contribution_token', Str::random(40));

        return view('dictionary.contribute', [
            'categories' => DictionaryCategory::query()->orderBy('name')->get(),
            'partsOfSpeech' => [
                'noun' => 'Noun / nomina',
                'verb' => 'Verb / verba',
                'adjective' => 'Adjective / adjektiva',
                'adverb' => 'Adverb / adverbia',
                'pronoun' => 'Pronoun / pronomina',
                'preposition' => 'Preposition / preposisi',
                'conjunction' => 'Conjunction / konjungsi',
                'interjection' => 'Interjection / interjeksi',
                'phrase' => 'Phrase / frasa',
            ],
        ]);
    }

    public function store(StoreDictionaryContributionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        DB::transaction(function () use ($validated): void {
            $entry = DictionaryEntry::create([
                'tolaki_word' => $validated['tolaki_word'],
                'indonesian_word' => $validated['indonesian_word'],
                'english_word' => $validated['english_word'] ?? null,
                'part_of_speech' => $validated['part_of_speech'] ?? null,
                'category_id' => $validated['category_id'] ?? null,
                'definition' => $validated['definition'] ?? null,
                'usage_note' => $validated['usage_note'] ?? null,
                'cultural_note' => $validated['cultural_note'] ?? null,
                'status' => DictionaryEntry::STATUS_SUBMITTED,
                'created_by' => auth()->id(),
            ]);

            if (filled($validated['tolaki_sentence'] ?? null) && filled($validated['indonesian_sentence'] ?? null)) {
                $entry->examples()->create([
                    'tolaki_sentence' => $validated['tolaki_sentence'],
                    'indonesian_sentence' => $validated['indonesian_sentence'],
                    'english_sentence' => $validated['english_sentence'] ?? null,
                    'notes' => $validated['example_notes'] ?? null,
                ]);
            }

            app(\App\Services\LegacyDictionaryEntrySynchronizer::class)->sync($entry->fresh('examples'));
        });

        $request->session()->forget('dictionary_contribution_token');

        return redirect()
            ->route('dictionary.contribute')
            ->with('status', 'Usulan kata berhasil dikirim dan menunggu validasi editor.');
    }
}
