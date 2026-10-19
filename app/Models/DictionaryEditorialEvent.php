<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictionaryEditorialEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'entity_type', 'entity_id', 'action', 'from_status', 'to_status',
        'change_set', 'reason', 'actor_id', 'created_at',
    ];

    protected function casts(): array
    {
        return ['change_set' => 'array', 'created_at' => 'datetime'];
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
