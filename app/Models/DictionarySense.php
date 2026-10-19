<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DictionarySense extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = [
        'lemma_id', 'sense_number', 'definition', 'definition_tolaki', 'definition_english', 'definition_language', 'part_of_speech',
        'grammatical_features', 'category_id', 'domain_label', 'register_label', 'usage_note', 'cultural_note',
        'status', 'source_id', 'created_by', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['grammatical_features' => 'array', 'reviewed_at' => 'datetime'];
    }

    public function lemma(): BelongsTo
    {
        return $this->belongsTo(DictionaryLemma::class, 'lemma_id');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(DictionaryTranslation::class, 'sense_id');
    }

    public function examples(): HasMany
    {
        return $this->hasMany(DictionaryExample::class, 'sense_id');
    }

    public function relations(): HasMany
    {
        return $this->hasMany(DictionarySenseRelation::class, 'sense_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DictionarySource::class, 'source_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DictionaryCategory::class, 'category_id');
    }
}
