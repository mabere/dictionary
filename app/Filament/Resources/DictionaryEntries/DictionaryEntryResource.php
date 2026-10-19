<?php

namespace App\Filament\Resources\DictionaryEntries;

use App\Filament\Resources\DictionaryEntries\Pages\CreateDictionaryEntry;
use App\Filament\Resources\DictionaryEntries\Pages\EditDictionaryEntry;
use App\Filament\Resources\DictionaryEntries\Pages\ListDictionaryEntries;
use App\Filament\Resources\DictionaryEntries\Pages\ViewDictionaryEntry;
use App\Filament\Resources\DictionaryEntries\Schemas\DictionaryEntryForm;
use App\Filament\Resources\DictionaryEntries\Schemas\DictionaryEntryInfolist;
use App\Filament\Resources\DictionaryEntries\Tables\DictionaryEntriesTable;
use App\Models\DictionaryLemma;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class DictionaryEntryResource extends Resource
{
    protected static ?string $model = DictionaryLemma::class;

    protected static ?string $slug = 'dictionary-entries';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Lema dan Makna';

    protected static ?string $modelLabel = 'Lema';

    protected static ?string $pluralModelLabel = 'Lema dan Makna';

    protected static string|UnitEnum|null $navigationGroup = 'Kamus';

    public static function form(Schema $schema): Schema
    {
        return DictionaryEntryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DictionaryEntryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DictionaryEntriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewDictionaryBackoffice() === true;
    }

    public static function canView(Model $record): bool
    {
        $user = auth()->user();

        if (! $user?->canViewDictionaryBackoffice()) {
            return false;
        }

        return $user->isDictionaryAdmin()
            || $user->isAssessor()
            || $user->ownsDictionaryLemma($record)
            || ($user->isDictionaryEditor() && in_array($record->status, ['submitted', 'reviewed', 'published'], true));
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->canManageDictionary() === true;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        return $user?->isDictionaryAdmin() === true
            || ($user?->isDictionaryEditor() === true && $user->ownsDictionaryLemma($record) && $record->status !== \App\Models\DictionaryEntry::STATUS_PUBLISHED);
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        return $user?->isDictionaryAdmin() === true;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isDictionaryAdmin() === true;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with([
            'forms.source',
            'forms.analyses.baseLemma',
            'forms.analyses.source',
            'senses.category',
            'senses.source',
            'senses.translations.source',
            'senses.relations.relatedSense.lemma',
            'senses.relations.source',
            'senses.examples.source',
            'senses.examples.translations.source',
            'pronunciations.source',
            'editorialEvents.actor',
            'creator',
            'reviewer',
            'orthographyProfile',
        ]);
        $user = auth()->user();

        if ($user?->isDictionaryAdmin() || $user?->isAssessor()) {
            return $query;
        }

        if ($user?->isDictionaryEditor()) {
            return $query->where(function (Builder $scoped) use ($user): void {
                $scoped->where('created_by', $user->getKey())
                    ->orWhereIn('status', ['submitted', 'reviewed', 'published']);
            });
        }

        return $query->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDictionaryEntries::route('/'),
            'create' => CreateDictionaryEntry::route('/create'),
            'view' => ViewDictionaryEntry::route('/{record}'),
            'edit' => EditDictionaryEntry::route('/{record}/edit'),
        ];
    }
}
