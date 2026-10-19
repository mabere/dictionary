<?php

namespace App\Services;

use App\Models\DictionaryEntry;
use App\Models\DictionaryExample;
use App\Models\DictionaryExampleTranslation;
use App\Models\DictionaryLemma;
use App\Models\DictionaryPronunciation;
use App\Models\DictionarySense;
use App\Models\DictionaryTranslation;
use Illuminate\Support\Facades\DB;

class LegacyDictionaryEntrySynchronizer
{
    public function sync(DictionaryEntry $entry): DictionaryLemma
    {
        return DB::transaction(function () use ($entry): DictionaryLemma {
            $entry->loadMissing('examples');

            $headword = $entry->tolaki_word ?: $entry->source_word;
            $lemma = DictionaryLemma::query()->updateOrCreate(
                ['legacy_entry_id' => $entry->getKey()],
                [
                    'public_id' => $entry->public_id,
                    'headword' => $headword,
                    'headword_normalized' => DictionaryLemma::normalizeForm($headword),
                    'status' => $entry->status,
                    'created_by' => $entry->created_by,
                    'reviewed_by' => $entry->reviewed_by,
                    'reviewed_at' => $entry->reviewed_at,
                ],
            );

            $sense = DictionarySense::query()->firstOrNew([
                'lemma_id' => $lemma->getKey(),
                'sense_number' => 1,
            ]);
            $sense->fill([
                'definition' => $entry->definition,
                'definition_language' => 'id',
                'part_of_speech' => $entry->part_of_speech,
                'category_id' => $entry->category_id,
                'usage_note' => $entry->usage_note ?: $entry->notes,
                'cultural_note' => $entry->cultural_note,
                'status' => $entry->status,
                'source_id' => null,
                'created_by' => $entry->created_by,
                'reviewed_by' => $entry->reviewed_by,
                'reviewed_at' => $entry->reviewed_at,
            ])->save();

            $this->syncTranslation($sense, $entry->getKey(), 'id', $entry->indonesian_word ?: $entry->target_word);
            $this->syncTranslation($sense, $entry->getKey(), 'en', $entry->english_word ?: $entry->reverse_word);

            if (filled($entry->phonetic) || filled($entry->pronunciation_audio)) {
                DictionaryPronunciation::query()->updateOrCreate(
                    ['legacy_entry_id' => $entry->getKey()],
                    [
                        'lemma_id' => $lemma->getKey(),
                        'sense_id' => null,
                        'transcription' => $entry->phonetic,
                        'transcription_system' => null,
                        'audio_path' => $entry->pronunciation_audio,
                        'rights_status' => $entry->status === DictionaryEntry::STATUS_PUBLISHED ? 'permission_granted' : 'unknown',
                        'audio_visibility' => $entry->status === DictionaryEntry::STATUS_PUBLISHED ? 'public' : 'internal',
                        'status' => $entry->status,
                        'created_by' => $entry->created_by,
                    ],
                );
            } else {
                DictionaryPronunciation::query()
                    ->where('legacy_entry_id', $entry->getKey())
                    ->delete();
            }

            foreach ($entry->examples as $legacyExample) {
                $tolakiText = $legacyExample->tolaki_sentence ?: $legacyExample->source_sentence;
                if (blank($tolakiText)) {
                    continue;
                }

                $example = DictionaryExample::query()->updateOrCreate(
                    ['legacy_example_id' => $legacyExample->getKey()],
                    [
                        'sense_id' => $sense->getKey(),
                        'tolaki_text' => $tolakiText,
                        'evidence_method' => 'legacy_import',
                        'elicitation_note' => $legacyExample->notes,
                        'status' => $entry->status,
                        'created_by' => $entry->created_by,
                    ],
                );

                $this->syncExampleTranslation(
                    $example,
                    $legacyExample->getKey(),
                    'id',
                    $legacyExample->indonesian_sentence ?: $legacyExample->target_sentence,
                    $entry->created_by,
                );
                $this->syncExampleTranslation(
                    $example,
                    $legacyExample->getKey(),
                    'en',
                    $legacyExample->english_sentence,
                    $entry->created_by,
                );
            }

            return $lemma->load('senses.translations', 'senses.examples.translations');
        });
    }

    private function syncTranslation(DictionarySense $sense, int $legacyEntryId, string $languageCode, ?string $form): void
    {
        if (blank($form)) {
            DictionaryTranslation::query()
                ->where('legacy_entry_id', $legacyEntryId)
                ->where('language_code', $languageCode)
                ->delete();

            return;
        }

        $normalized = DictionaryLemma::normalizeForm($form);

        DictionaryTranslation::query()->updateOrCreate(
            [
                'legacy_entry_id' => $legacyEntryId,
                'language_code' => $languageCode,
            ],
            [
                'form' => DictionaryLemma::cleanForm($form),
                'sense_id' => $sense->getKey(),
                'form_normalized' => $normalized,
                'translation_type' => 'equivalent',
                'status' => $sense->status,
                'source_id' => null,
                'created_by' => $sense->created_by,
                'reviewed_by' => $sense->reviewed_by,
                'reviewed_at' => $sense->reviewed_at,
            ],
        );
    }

    private function syncExampleTranslation(
        DictionaryExample $example,
        int $legacyExampleId,
        string $languageCode,
        ?string $text,
        ?int $createdBy,
    ): void {
        if (blank($text)) {
            DictionaryExampleTranslation::query()
                ->where('legacy_example_id', $legacyExampleId)
                ->where('language_code', $languageCode)
                ->delete();

            return;
        }

        DictionaryExampleTranslation::query()->updateOrCreate(
            [
                'legacy_example_id' => $legacyExampleId,
                'language_code' => $languageCode,
            ],
            [
                'example_id' => $example->getKey(),
                'text' => trim($text),
                'status' => $example->status,
                'created_by' => $createdBy,
            ],
        );
    }
}
