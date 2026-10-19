<?php

namespace App\Filament\Resources\DictionaryEntries\Pages;

use App\Filament\Resources\DictionaryEntries\DictionaryEntryResource;
use App\Models\DictionaryEntry;
use Filament\Resources\Pages\CreateRecord;

class CreateDictionaryEntry extends CreateRecord
{
    protected static string $resource = DictionaryEntryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        $data['status'] = DictionaryEntry::STATUS_DRAFT;
        $data['reviewed_by'] = null;
        $data['reviewed_at'] = null;

        return $data;
    }
}
