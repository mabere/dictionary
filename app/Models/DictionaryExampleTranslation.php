<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryExampleTranslation extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = [
        'example_id', 'legacy_example_id', 'language_code', 'text', 'translation_note', 'status',
        'source_id', 'created_by', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['reviewed_at' => 'datetime'];
    }

    public function example(): BelongsTo
    {
        return $this->belongsTo(DictionaryExample::class, 'example_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DictionarySource::class, 'source_id');
    }

    public function legacyExample(): BelongsTo
    {
        return $this->belongsTo(ExampleSentence::class, 'legacy_example_id');
    }
}
