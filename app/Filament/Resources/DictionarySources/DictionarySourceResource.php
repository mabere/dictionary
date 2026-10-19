<?php

namespace App\Filament\Resources\DictionarySources;

use App\Filament\Resources\DictionarySources\Pages\CreateDictionarySource;
use App\Filament\Resources\DictionarySources\Pages\EditDictionarySource;
use App\Filament\Resources\DictionarySources\Pages\ListDictionarySources;
use App\Models\DictionarySource;
use BackedEnum;
use Filament\Forms\Components\DatePicker;
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

class DictionarySourceResource extends Resource
{
    protected static ?string $model = DictionarySource::class;

    protected static ?string $slug = 'dictionary-sources';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Sumber Data';

    protected static ?string $modelLabel = 'Sumber Data';

    protected static ?string $pluralModelLabel = 'Sumber Data';

    protected static string|UnitEnum|null $navigationGroup = 'Kamus';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Identitas sumber')->schema([
                Select::make('source_type')->label('Jenis sumber')->required()->options([
                    'book' => 'Buku/kamus',
                    'publication' => 'Publikasi ilmiah',
                    'fieldwork' => 'Penelitian lapangan',
                    'corpus' => 'Korpus',
                    'audio' => 'Rekaman audio',
                    'other' => 'Lainnya',
                ]),
                DatePicker::make('published_on')->label('Tanggal terbit/pengumpulan'),
                Textarea::make('citation')->label('Sitasi / identitas sumber')->required()->rows(3)->columnSpanFull(),
                Select::make('rights_status')->label('Status hak penggunaan')->required()->options([
                    'unknown' => 'Belum diperiksa',
                    'rights_check_required' => 'Perlu pemeriksaan hak',
                    'permission_granted' => 'Izin diberikan',
                    'public_domain' => 'Domain publik',
                    'restricted' => 'Penggunaan terbatas',
                ])->default('unknown'),
                Select::make('visibility')->label('Visibilitas metadata')->required()->options([
                    'internal' => 'Internal',
                    'public' => 'Dapat ditampilkan publik',
                    'restricted' => 'Terbatas',
                ])->default('internal'),
                TextInput::make('permission_reference')->label('Referensi izin')->maxLength(255),
                Textarea::make('notes')->label('Catatan hak, privasi, atau penggunaan')->rows(4)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('citation')->label('Sumber')->searchable()->limit(70)->wrap(),
            TextColumn::make('source_type')->label('Jenis')->badge(),
            TextColumn::make('rights_status')->label('Hak')->badge(),
            TextColumn::make('visibility')->label('Visibilitas')->badge(),
            TextColumn::make('published_on')->label('Tanggal')->date()->sortable(),
            TextColumn::make('created_at')->label('Dicatat')->dateTime()->sortable(),
        ])->recordActions([
            \Filament\Actions\EditAction::make(),
            \Filament\Actions\DeleteAction::make()->requiresConfirmation(),
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
        return auth()->user()?->isDictionaryAdmin() === true
            ? parent::getEloquentQuery()
            : parent::getEloquentQuery()->whereRaw('1 = 0');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDictionarySources::route('/'),
            'create' => CreateDictionarySource::route('/create'),
            'edit' => EditDictionarySource::route('/{record}/edit'),
        ];
    }
}
