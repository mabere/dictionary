<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DictionaryEntry extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SUBMITTED = 'submitted';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'public_id',
        'tolaki_word',
        'tolaki_word_normalized',
        'indonesian_word',
        'indonesian_word_normalized',
        'english_word',
        'english_word_normalized',
        'source_word',
        'source_word_normalized',
        'target_word',
        'target_word_normalized',
        'duplicate_key',
        'reverse_word',
        'source_language',
        'target_language',
        'part_of_speech',
        'phonetic',
        'pronunciation_audio',
        'definition',
        'usage_note',
        'cultural_note',
        'notes',
        'category_id',
        'status',
        'created_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (DictionaryEntry $entry) {
            $entry->public_id = $entry->public_id ?: (string) Str::ulid();

            $entry->tolaki_word = self::cleanWord($entry->tolaki_word);
            $entry->indonesian_word = self::cleanWord($entry->indonesian_word);
            $entry->english_word = self::cleanWord($entry->english_word);
            $entry->source_word = self::cleanWord($entry->source_word);
            $entry->target_word = self::cleanWord($entry->target_word);
            $entry->reverse_word = self::cleanWord($entry->reverse_word);
            $entry->part_of_speech = self::cleanWord($entry->part_of_speech);
            $entry->phonetic = self::cleanWord($entry->phonetic);

            $entry->tolaki_word = $entry->tolaki_word ?: $entry->source_word;
            $entry->indonesian_word = $entry->indonesian_word ?: $entry->target_word;
            $entry->english_word = $entry->english_word ?: $entry->reverse_word;
            $entry->usage_note = $entry->usage_note ?: $entry->notes;

            $entry->source_word = $entry->source_word ?: $entry->tolaki_word;
            $entry->target_word = $entry->target_word ?: ($entry->indonesian_word ?: $entry->english_word);
            $entry->source_language = $entry->source_language ?: 'Tolaki';
            $entry->target_language = $entry->target_language ?: 'Indonesia';
            $entry->reverse_word = $entry->reverse_word ?: $entry->english_word;
            $entry->notes = $entry->notes ?: $entry->usage_note;

            $entry->tolaki_word_normalized = self::normalizeWord($entry->tolaki_word);
            $entry->indonesian_word_normalized = self::normalizeWord($entry->indonesian_word);
            $entry->english_word_normalized = self::normalizeWord($entry->english_word);
            $entry->source_word_normalized = self::normalizeWord($entry->source_word);
            $entry->target_word_normalized = self::normalizeWord($entry->target_word);
            $entry->duplicate_key = self::makeDuplicateKey(
                $entry->tolaki_word,
                $entry->indonesian_word,
                'Tolaki',
                'Indonesia',
                $entry->part_of_speech,
            );
        });

        static::saved(function (DictionaryEntry $entry): void {
            if (! Schema::hasTable('dictionary_lemmas')) {
                return;
            }

            $legacyEntryId = $entry->getKey();

            DB::afterCommit(function () use ($legacyEntryId): void {
                $legacyEntry = DictionaryEntry::query()->with('examples')->find($legacyEntryId);

                if ($legacyEntry) {
                    app(\App\Services\LegacyDictionaryEntrySynchronizer::class)->sync($legacyEntry);
                }
            });
        });
    }

    public static function normalizeWord(?string $word): string
    {
        return Str::of(self::cleanWord($word) ?? '')
            ->lower()
            ->toString();
    }

    public static function cleanWord(?string $word): ?string
    {
        if (blank($word)) {
            return null;
        }

        return Str::of($word)->squish()->toString();
    }

    public static function makeDuplicateKey(
        ?string $sourceWord,
        ?string $targetWord,
        ?string $sourceLanguage,
        ?string $targetLanguage,
        ?string $partOfSpeech,
    ): string {
        return collect([
            self::normalizeWord($sourceWord),
            self::normalizeWord($partOfSpeech),
        ])->implode('|');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DictionaryCategory::class, 'category_id');
    }

    public function examples(): HasMany
    {
        return $this->hasMany(ExampleSentence::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('created_by', $user->getKey());
    }
}
