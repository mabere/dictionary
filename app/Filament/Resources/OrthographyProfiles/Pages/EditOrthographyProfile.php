<?php

namespace App\Filament\Resources\OrthographyProfiles\Pages;

use App\Filament\Resources\OrthographyProfiles\OrthographyProfileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;

class EditOrthographyProfile extends EditRecord
{
    protected static string $resource = OrthographyProfileResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

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
