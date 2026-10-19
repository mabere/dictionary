<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class DictionaryCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::saving(function (DictionaryCategory $category) {
            if (blank($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function entries(): HasMany
    {
        return $this->hasMany(DictionaryEntry::class, 'category_id');
    }

    public function senses(): HasMany
    {
        return $this->hasMany(DictionarySense::class, 'category_id');
    }
}
