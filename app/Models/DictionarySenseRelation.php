<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class DictionarySenseRelation extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = ['sense_id', 'related_sense_id', 'relation_type', 'note', 'source_id'];

    protected static function booted(): void
    {
        static::saving(function (self $relation): void {
            if (! $relation->related_sense_id) {
                throw ValidationException::withMessages([
                    'data.senses' => 'Pilih makna terkait sebelum menyimpan relasi makna.',
                ]);
            }
        });
    }

    public function sense(): BelongsTo
    {
        return $this->belongsTo(DictionarySense::class, 'sense_id');
    }

    public function relatedSense(): BelongsTo
    {
        return $this->belongsTo(DictionarySense::class, 'related_sense_id');
    }

    public function source(): BelongsTo
    {
        return $this->belongsTo(DictionarySource::class, 'source_id');
    }
}
