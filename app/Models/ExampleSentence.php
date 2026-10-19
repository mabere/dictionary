<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ExampleSentence extends Model
{
    use HasFactory;

    protected $fillable = [
        'dictionary_entry_id',
        'tolaki_sentence',
        'indonesian_sentence',
        'english_sentence',
        'source_sentence',
        'target_sentence',
        'notes',
    ];

    protected static function booted(): void
    {
        static::saving(function (ExampleSentence $example): void {
            $example->tolaki_sentence = $example->tolaki_sentence ?: $example->source_sentence;
            $example->indonesian_sentence = $example->indonesian_sentence ?: $example->target_sentence;
            $example->source_sentence = $example->source_sentence ?: $example->tolaki_sentence;
            $example->target_sentence = $example->target_sentence ?: $example->indonesian_sentence;
        });

        static::saved(function (ExampleSentence $example): void {
            if (! Schema::hasTable('dictionary_lemmas')) {
                return;
            }

            $legacyEntryId = $example->dictionary_entry_id;

            DB::afterCommit(function () use ($legacyEntryId): void {
                $legacyEntry = DictionaryEntry::query()->with('examples')->find($legacyEntryId);

                if ($legacyEntry) {
                    app(\App\Services\LegacyDictionaryEntrySynchronizer::class)->sync($legacyEntry);
                }
            });
        });
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(DictionaryEntry::class, 'dictionary_entry_id');
    }
}
