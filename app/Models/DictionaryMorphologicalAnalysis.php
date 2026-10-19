<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryMorphologicalAnalysis extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = [
        'lemma_form_id', 'base_lemma_id', 'segmentation', 'morphemes', 'analysis_note',
        'status', 'source_id', 'created_by', 'reviewed_by', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return ['morphemes' => 'array', 'reviewed_at' => 'datetime'];
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(DictionaryLemmaForm::class, 'lemma_form_id');
    }

    public function baseLemma(): BelongsTo
    {
        return $this->belongsTo(DictionaryLemma::class, 'base_lemma_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DictionarySource::class, 'source_id');
    }
}
