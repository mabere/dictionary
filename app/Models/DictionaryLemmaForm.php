<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DictionaryLemmaForm extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = ['lemma_id', 'form', 'form_normalized', 'form_type', 'region_label', 'notes', 'source_id'];

    protected static function booted(): void
    {
        static::saving(function (self $form): void {
            $form->form = DictionaryLemma::cleanForm($form->form);
            $form->form_normalized = DictionaryLemma::normalizeForm($form->form);
        });
    }

    public function lemma(): BelongsTo
    {
        return $this->belongsTo(DictionaryLemma::class, 'lemma_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DictionarySource::class, 'source_id');
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(DictionaryMorphologicalAnalysis::class, 'lemma_form_id');
    }
}
