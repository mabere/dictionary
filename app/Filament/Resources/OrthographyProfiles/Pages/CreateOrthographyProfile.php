<?php

namespace App\Filament\Resources\OrthographyProfiles\Pages;

use App\Filament\Resources\OrthographyProfiles\OrthographyProfileResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateOrthographyProfile extends CreateRecord
{
    protected static string $resource = OrthographyProfileResource::class;

    protected Width|string|null $maxContentWidth = Width::Full;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        return $data;
    }
}
