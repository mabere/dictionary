<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryEvidenceLink extends Model
{
    protected $fillable = ['source_id', 'evidence_type', 'evidence_id', 'evidence_note', 'confidence'];

    public function source(): BelongsTo
    {
        return $this->belongsTo(DictionarySource::class, 'source_id');
    }
}
