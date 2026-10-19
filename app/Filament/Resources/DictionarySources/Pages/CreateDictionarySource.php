<?php

namespace App\Filament\Resources\DictionarySources\Pages;

use App\Filament\Resources\DictionarySources\DictionarySourceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDictionarySource extends CreateRecord
{
    protected static string $resource = DictionarySourceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
