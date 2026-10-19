<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'managed_by',
        'nim',
        'phone',
        'study_program',
        'faculty',
        'address',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->canViewDictionaryBackoffice();
    }

    public function isDictionaryAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isDictionaryEditor(): bool
    {
        return in_array($this->role, ['admin', 'editor'], true);
    }

    public function isAssessor(): bool
    {
        return $this->role === 'assessor';
    }

    public function canViewDictionaryBackoffice(): bool
    {
        return in_array($this->role, ['admin', 'editor', 'assessor'], true);
    }

    public function dictionaryEntries(): HasMany
    {
        return $this->hasMany(DictionaryEntry::class, 'created_by');
    }

    public function dictionaryLemmas(): HasMany
    {
        return $this->hasMany(DictionaryLemma::class, 'created_by');
    }

    public function managedUsers(): HasMany
    {
        return $this->hasMany(self::class, 'managed_by');
    }

    public function canManageDictionary(): bool
    {
        return in_array($this->role, ['admin', 'editor'], true);
    }

    public function ownsDictionaryEntry(DictionaryEntry $entry): bool
    {
        return (int) $entry->created_by === (int) $this->getKey();
    }

    public function ownsDictionaryLemma(DictionaryLemma $lemma): bool
    {
        return (int) $lemma->created_by === (int) $this->getKey();
    }

    public function reviewedDictionaryEntries(): HasMany
    {
        return $this->hasMany(DictionaryEntry::class, 'reviewed_by');
    }
}
