<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DictionaryExample extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = [
        'sense_id', 'tolaki_text', 'region_label', 'evidence_method', 'elicitation_note',
        'status', 'source_id', 'legacy_example_id', 'created_by',
    ];

    public function sense(): BelongsTo
    {
        return $this->belongsTo(DictionarySense::class, 'sense_id');
    }

    public function legacyExample(): BelongsTo
    {
        return $this->belongsTo(ExampleSentence::class, 'legacy_example_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(DictionaryExampleTranslation::class, 'example_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DictionarySource::class, 'source_id');
    }
}
