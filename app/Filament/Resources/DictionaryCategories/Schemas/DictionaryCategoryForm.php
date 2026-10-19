<?php

namespace App\Filament\Resources\DictionaryCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DictionaryCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama kategori')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->helperText('Boleh dikosongkan, sistem akan membuat slug dari nama kategori.')
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }
}
