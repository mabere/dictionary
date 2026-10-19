<?php

namespace App\Filament\Resources\OrthographyProfiles;

use App\Filament\Resources\OrthographyProfiles\Pages\CreateOrthographyProfile;
use App\Filament\Resources\OrthographyProfiles\Pages\EditOrthographyProfile;
use App\Filament\Resources\OrthographyProfiles\Pages\ListOrthographyProfiles;
use App\Models\OrthographyProfile;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class OrthographyProfileResource extends Resource
{
    protected static ?string $model = OrthographyProfile::class;

    protected static ?string $slug = 'orthography-profiles';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLanguage;

    protected static ?string $navigationLabel = 'Profil Ortografi';

    protected static ?string $modelLabel = 'Profil Ortografi';

    protected static ?string $pluralModelLabel = 'Profil Ortografi';

    protected static string|UnitEnum|null $navigationGroup = 'Kamus';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Konvensi ortografi')->schema([
                TextInput::make('name')->label('Nama profil')->required()->maxLength(255)->columnSpan(2),
                TextInput::make('version')->label('Versi')->required()->maxLength(32)->columnSpan(1),
                Select::make('profile_type')->label('Jenis profil')->required()->options([
                    'orthography' => 'Ortografi',
                    'verb_morphology' => 'Morfologi verba',
                ])->default('orthography')->columnSpan(1),
                Select::make('status')->label('Status')->required()->options([
                    'draft' => 'Draf untuk ditinjau',
                    'active' => 'Disahkan dan aktif',
                    'retired' => 'Digantikan',
                ])->columnSpan(1),
                Textarea::make('citation')->label('Rujukan')->rows(3)->columnSpanFull(),
                Textarea::make('rules')
                    ->label('Aturan terstruktur (JSON)')
                    ->helperText('Gunakan objek JSON. Perubahan aturan menjadi kebijakan yang tercatat dan harus ditinjau sebelum diaktifkan.')
                    ->rows(12)
                    ->required()
                    ->rules(['json'])
                    ->formatStateUsing(fn (mixed $state): string => json_encode(
                        is_array($state) ? $state : [],
                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES,
                    ) ?: '{}')
                    ->dehydrateStateUsing(fn (string $state): array => json_decode($state, true, flags: JSON_THROW_ON_ERROR))
                    ->columnSpanFull(),
                Textarea::make('change_note')->label('Alasan dan catatan perubahan')->rows(4)->columnSpanFull(),
            ])->columns(['default' => 1, 'lg' => 4])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Profil')->searchable()->sortable(),
            TextColumn::make('version')->label('Versi')->badge(),
            TextColumn::make('profile_type')->label('Jenis')->formatStateUsing(fn (string $state): string => match ($state) {
                'verb_morphology' => 'Morfologi verba',
                default => 'Ortografi',
            })->badge(),
            TextColumn::make('status')->label('Status')->badge(),
            TextColumn::make('citation')->label('Rujukan')->limit(70)->wrap(),
            TextColumn::make('updated_at')->label('Diperbarui')->dateTime()->sortable(),
        ])->recordActions([
            \Filament\Actions\EditAction::make(),
        ]);
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isDictionaryAdmin() === true;
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }

    public static function getEloquentQuery(): Builder
    {
        return static::canViewAny()
            ? parent::getEloquentQuery()
            : parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrthographyProfiles::route('/'),
            'create' => CreateOrthographyProfile::route('/create'),
            'edit' => EditOrthographyProfile::route('/{record}/edit'),
        ];
    }
}
