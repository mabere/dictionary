<?php

namespace App\Filament\Resources\DictionaryEntries\Pages;

use App\Filament\Resources\DictionaryEntries\DictionaryEntryResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDictionaryEntries extends ListRecords
{
    protected static string $resource = DictionaryEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('importLexicographicCsv')
                ->label('Impor CSV terstruktur')
                ->icon('heroicon-o-document-arrow-up')
                ->url(route('admin.lexicographic-import'))
                ->visible(fn (): bool => auth()->user()?->canManageDictionary() === true),
            Action::make('exportLexicographicCsv')
                ->label('Ekspor CSV terstruktur')
                ->icon('heroicon-o-document-arrow-down')
                ->url(route('admin.lexicographic-export'))
                ->visible(fn (): bool => auth()->user()?->canManageDictionary() === true),
            Action::make('importCsv')
                ->label('Impor CSV lama')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(route('admin.dictionary-entries.import'))
                ->visible(fn (): bool => auth()->user()?->canManageDictionary() === true),
            Action::make('exportCsv')
                ->label('Ekspor CSV lama')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('admin.dictionary-entries.export'))
                ->visible(fn (): bool => auth()->user()?->canManageDictionary() === true),
            CreateAction::make()
                ->visible(fn (): bool => auth()->user()?->canManageDictionary() === true),
        ];
    }
}
