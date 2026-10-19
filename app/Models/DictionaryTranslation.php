<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryTranslation extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = [
        'sense_id', 'legacy_entry_id', 'language_code', 'form', 'form_normalized', 'translation_type',
        'context_note', 'status', 'source_id', 'created_by', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    protected static function booted(): void
    {
        static::saving(function (self $translation): void {
            $translation->form = DictionaryLemma::cleanForm($translation->form);
            $translation->form_normalized = DictionaryLemma::normalizeForm($translation->form);
            $translation->language_code = strtolower(trim((string) $translation->language_code));
        });
    }

    public function sense(): BelongsTo
    {
        return $this->belongsTo(DictionarySense::class, 'sense_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DictionarySource::class, 'source_id');
    }

    public function legacyEntry(): BelongsTo
    {
        return $this->belongsTo(DictionaryEntry::class, 'legacy_entry_id');
    }
}
