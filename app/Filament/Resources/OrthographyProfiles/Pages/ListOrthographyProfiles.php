<?php

namespace App\Filament\Resources\OrthographyProfiles\Pages;

use App\Filament\Resources\OrthographyProfiles\OrthographyProfileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrthographyProfiles extends ListRecords
{
    protected static string $resource = OrthographyProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
