<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'route_name',
        'dictionary_entry_id',
        'dictionary_lemma_id',
        'keyword',
        'search_direction',
        'ip_hash',
        'user_agent_hash',
        'occurred_on',
    ];

    protected function casts(): array
    {
        return [
            'occurred_on' => 'date',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(DictionaryEntry::class, 'dictionary_entry_id');
    }

    public function lemma(): BelongsTo
    {
        return $this->belongsTo(DictionaryLemma::class, 'dictionary_lemma_id');
    }
}
