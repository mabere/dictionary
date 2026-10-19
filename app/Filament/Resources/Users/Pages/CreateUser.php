<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['managed_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        if (! auth()->user()?->isDictionaryAdmin() && $this->record->role === 'editor') {
            $this->record->dictionaryEntries()->update(['created_by' => $this->record->getKey()]);
        }
    }
}
