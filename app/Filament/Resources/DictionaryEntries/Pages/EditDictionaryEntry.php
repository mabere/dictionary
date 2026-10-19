<?php

namespace App\Filament\Resources\DictionaryEntries\Pages;

use App\Filament\Resources\DictionaryEntries\DictionaryEntryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditDictionaryEntry extends EditRecord
{
    protected static string $resource = DictionaryEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->visible(fn (): bool => auth()->user()?->isDictionaryAdmin() === true),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['created_by'], $data['reviewed_by'], $data['reviewed_at']);

        if (! auth()->user()?->isDictionaryAdmin()) {
            $data['status'] = \App\Models\DictionaryEntry::STATUS_DRAFT;
        }

        return $data;
    }
}
