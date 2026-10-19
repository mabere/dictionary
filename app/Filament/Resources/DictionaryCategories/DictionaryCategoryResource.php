<?php

namespace App\Filament\Resources\DictionaryCategories;

use App\Filament\Resources\DictionaryCategories\Pages\CreateDictionaryCategory;
use App\Filament\Resources\DictionaryCategories\Pages\EditDictionaryCategory;
use App\Filament\Resources\DictionaryCategories\Pages\ListDictionaryCategories;
use App\Filament\Resources\DictionaryCategories\Schemas\DictionaryCategoryForm;
use App\Filament\Resources\DictionaryCategories\Tables\DictionaryCategoriesTable;
use App\Models\DictionaryCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class DictionaryCategoryResource extends Resource
{
    protected static ?string $model = DictionaryCategory::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Kategori';

    protected static ?string $modelLabel = 'Kategori';

    protected static ?string $pluralModelLabel = 'Kategori';

    protected static string|UnitEnum|null $navigationGroup = 'Kamus';

    public static function form(Schema $schema): Schema
    {
        return DictionaryCategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DictionaryCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        return $user?->isDictionaryAdmin() === true
            ? $query
            : $query->whereRaw('1 = 0');
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->canViewDictionaryBackoffice() === true;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isDictionaryAdmin() === true;
    }

    public static function canEdit(Model $record): bool
    {
        return auth()->user()?->isDictionaryAdmin() === true;
    }

    public static function canDelete(Model $record): bool
    {
        return auth()->user()?->isDictionaryAdmin() === true;
    }

    public static function canView(Model $record): bool
    {
        return auth()->user()?->isDictionaryAdmin() === true;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isDictionaryAdmin() === true;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDictionaryCategories::route('/'),
            'create' => CreateDictionaryCategory::route('/create'),
            'edit' => EditDictionaryCategory::route('/{record}/edit'),
        ];
    }
}
