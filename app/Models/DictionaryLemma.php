<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DictionaryLemma extends Model
{
    use HasFactory;
    use RecordsEditorialChanges;

    protected $fillable = [
        'public_id', 'headword', 'headword_normalized', 'orthography_profile_id',
        'legacy_entry_id', 'status', 'created_by', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::creating(function (self $lemma): void {
            $lemma->public_id ??= (string) Str::ulid();
            $lemma->headword = self::cleanForm($lemma->headword);
            $lemma->headword_normalized = self::normalizeForm($lemma->headword);
        });

        static::updating(function (self $lemma): void {
            $lemma->headword = self::cleanForm($lemma->headword);
            $lemma->headword_normalized = self::normalizeForm($lemma->headword);
        });
    }

    public static function cleanForm(?string $form): ?string
    {
        return blank($form) ? null : Str::of($form)->squish()->toString();
    }

    public static function normalizeForm(?string $form): string
    {
        return Str::lower(self::cleanForm($form) ?? '');
    }

    public function legacyEntry(): BelongsTo
    {
        return $this->belongsTo(DictionaryEntry::class, 'legacy_entry_id');
    }

    public function orthographyProfile(): BelongsTo
    {
        return $this->belongsTo(OrthographyProfile::class);
    }

    public function forms(): HasMany
    {
        return $this->hasMany(DictionaryLemmaForm::class, 'lemma_id');
    }

    public function senses(): HasMany
    {
        return $this->hasMany(DictionarySense::class, 'lemma_id')->orderBy('sense_number');
    }

    public function pronunciations(): HasMany
    {
        return $this->hasMany(DictionaryPronunciation::class, 'lemma_id');
    }

    public function editorialEvents(): HasMany
    {
        return $this->hasMany(DictionaryEditorialEvent::class, 'entity_id')
            ->whereIn('entity_type', ['lemma', 'DictionaryLemma'])
            ->latest('created_at');
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
        return $query->where('status', 'published');
    }
}
