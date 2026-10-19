<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryPronunciation extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = [
        'lemma_id', 'sense_id', 'legacy_entry_id', 'transcription', 'transcription_system', 'audio_path',
        'region_label', 'source_id', 'rights_status', 'status', 'created_by',
    ];

    public function lemma(): BelongsTo
    {
        return $this->belongsTo(DictionaryLemma::class, 'lemma_id');
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
