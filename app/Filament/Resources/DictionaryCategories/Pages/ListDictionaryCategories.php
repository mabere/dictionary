<?php

namespace App\Filament\Resources\DictionaryCategories\Pages;

use App\Filament\Resources\DictionaryCategories\DictionaryCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDictionaryCategories extends ListRecords
{
    protected static string $resource = DictionaryCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => auth()->user()?->isDictionaryEditor() === true),
        ];
    }
}
