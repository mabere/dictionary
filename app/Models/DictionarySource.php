<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DictionarySource extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = [
        'source_type', 'citation', 'published_on', 'rights_status', 'visibility',
        'permission_reference', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return ['published_on' => 'date'];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function evidenceLinks(): HasMany
    {
        return $this->hasMany(DictionaryEvidenceLink::class, 'source_id');
    }
}
