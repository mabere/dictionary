<?php

namespace App\Filament\Resources\DictionaryEntries\Pages;

use App\Filament\Resources\DictionaryEntries\DictionaryEntryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewDictionaryEntry extends ViewRecord
{
    protected static string $resource = DictionaryEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (): bool => auth()->user()?->isDictionaryEditor() === true),
        ];
    }
}
