<?php

namespace App\Models;

use App\Models\Concerns\RecordsEditorialChanges;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrthographyProfile extends Model
{
    use RecordsEditorialChanges;

    protected $fillable = ['name', 'version', 'profile_type', 'status', 'rules', 'citation', 'change_note', 'created_by'];

    protected function casts(): array
    {
        return ['rules' => 'array'];
    }

    public function lemmas(): HasMany
    {
        return $this->hasMany(DictionaryLemma::class);
    }
}
