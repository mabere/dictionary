<?php

namespace App\Filament\Resources\DictionarySources\Pages;

use App\Filament\Resources\DictionarySources\DictionarySourceResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDictionarySource extends EditRecord
{
    protected static string $resource = DictionarySourceResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()->requiresConfirmation()];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['created_by']);

        return $data;
    }
}
