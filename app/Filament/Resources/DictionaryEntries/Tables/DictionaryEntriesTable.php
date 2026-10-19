<?php

namespace App\Filament\Resources\DictionaryEntries\Tables;

use App\Models\DictionaryEntry;
use App\Models\DictionaryLemma;
use App\Services\DictionaryEditorialWorkflow;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DictionaryEntriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('headword')
                    ->label('Lema Tolaki')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('senses_count')
                    ->label('Makna')
                    ->counts('senses')
                    ->sortable(),
                TextColumn::make('part_of_speech_summary')
                    ->label('Kelas kata')
                    ->state(fn (DictionaryLemma $record): string => $record->senses->pluck('part_of_speech')->filter()->unique()->implode(', '))
                    ->badge()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        DictionaryEntry::STATUS_PUBLISHED => 'success',
                        DictionaryEntry::STATUS_REVIEWED => 'info',
                        DictionaryEntry::STATUS_REJECTED => 'danger',
                        DictionaryEntry::STATUS_SUBMITTED => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('creator.name')->label('Pembuat')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reviewer.name')->label('Reviewer')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reviewed_at')->label('Direview')->since()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Diperbarui')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    DictionaryEntry::STATUS_DRAFT => 'Draft',
                    DictionaryEntry::STATUS_SUBMITTED => 'Menunggu review',
                    DictionaryEntry::STATUS_REVIEWED => 'Ditinjau',
                    DictionaryEntry::STATUS_PUBLISHED => 'Terbit',
                    DictionaryEntry::STATUS_REJECTED => 'Perlu revisi',
                ]),
                SelectFilter::make('part_of_speech')
                    ->label('Kelas kata')
                    ->options([
                        'noun' => 'Nomina', 'verb' => 'Verba', 'adjective' => 'Adjektiva',
                        'adverb' => 'Adverbia', 'pronoun' => 'Pronomina', 'phrase' => 'Frasa/ungkapan',
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, string $value): Builder => $query->whereHas('senses', fn (Builder $senseQuery) => $senseQuery->where('part_of_speech', $value)),
                    )),
                SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->options(fn (): array => \App\Models\DictionaryCategory::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['value'] ?? null,
                        fn (Builder $query, int|string $value): Builder => $query->whereHas('senses', fn (Builder $senseQuery) => $senseQuery->where('category_id', $value)),
                    )),
                Filter::make('review_queue')
                    ->label('Antrian review')
                    ->query(fn (Builder $query): Builder => $query->whereIn('status', [
                        DictionaryEntry::STATUS_DRAFT,
                        DictionaryEntry::STATUS_SUBMITTED,
                    ])),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('submitForReview')
                    ->label('Ajukan untuk ditinjau')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->visible(fn (DictionaryLemma $record): bool => in_array($record->status, [DictionaryEntry::STATUS_DRAFT, DictionaryEntry::STATUS_REJECTED], true)
                        && (auth()->user()?->isDictionaryAdmin() === true || (int) $record->created_by === (int) auth()->id())
                    )
                    ->action(fn (DictionaryLemma $record, DictionaryEditorialWorkflow $workflow) => $workflow->transition($record, DictionaryEntry::STATUS_SUBMITTED, auth()->user())),
                Action::make('markReviewed')
                    ->label('Tandai ditinjau')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->visible(fn (DictionaryLemma $record): bool => $record->status === DictionaryEntry::STATUS_SUBMITTED
                        && (auth()->user()?->isDictionaryAdmin() === true || (auth()->user()?->isDictionaryEditor() === true && (int) $record->created_by !== (int) auth()->id()))
                    )
                    ->action(fn (DictionaryLemma $record, DictionaryEditorialWorkflow $workflow) => $workflow->transition($record, DictionaryEntry::STATUS_REVIEWED, auth()->user())),
                Action::make('publish')
                    ->label('Terbitkan')
                    ->icon('heroicon-o-globe-alt')
                    ->color('success')
                    ->visible(fn (DictionaryLemma $record): bool => auth()->user()?->isDictionaryAdmin() === true && $record->status !== DictionaryEntry::STATUS_PUBLISHED)
                    ->requiresConfirmation()
                    ->action(fn (DictionaryLemma $record, DictionaryEditorialWorkflow $workflow) => $workflow->transition($record, DictionaryEntry::STATUS_PUBLISHED, auth()->user())),
                Action::make('reject')
                    ->label('Kembalikan untuk revisi')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (DictionaryLemma $record): bool => in_array($record->status, [DictionaryEntry::STATUS_SUBMITTED, DictionaryEntry::STATUS_REVIEWED], true)
                        && (auth()->user()?->isDictionaryAdmin() === true || (auth()->user()?->isDictionaryEditor() === true && (int) $record->created_by !== (int) auth()->id()))
                    )
                    ->form([\Filament\Forms\Components\Textarea::make('reason')->label('Catatan revisi')->required()])
                    ->action(fn (DictionaryLemma $record, array $data, DictionaryEditorialWorkflow $workflow) => $workflow->transition($record, DictionaryEntry::STATUS_REJECTED, auth()->user(), $data['reason'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn (): bool => auth()->user()?->isDictionaryAdmin() === true),
                ]),
            ]);
    }
}
