<?php

namespace App\Filament\Resources\DictionarySources\Pages;

use App\Filament\Resources\DictionarySources\DictionarySourceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDictionarySources extends ListRecords
{
    protected static string $resource = DictionarySourceResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
