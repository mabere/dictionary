<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class StudentDataEntryGuide extends Page
{
    public string $selectedGuideTab = 'overview';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationLabel = 'Panduan Mahasiswa';

    protected static ?string $title = 'Panduan Input Entri Kamus';

    protected static ?string $slug = 'panduan-mahasiswa';

    protected static string|\UnitEnum|null $navigationGroup = 'Kamus';

    protected static ?int $navigationSort = 5;

    protected string $view = 'filament.pages.student-data-entry-guide';

    public static function canAccess(): bool
    {
        return auth()->user()?->canViewDictionaryBackoffice() === true;
    }

    public function selectGuideTab(string $tab): void
    {
        abort_unless(in_array($tab, ['overview', 'identity', 'meaning', 'pronunciation', 'workflow'], true), 404);

        $this->selectedGuideTab = $tab;
    }

    public function renderGuide(): string
    {
        return Str::markdown($this->guideMarkdown(), [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]);
    }

    public function guideTabs(): array
    {
        $sections = preg_split('/(?=^## \d+\. )/m', $this->guideMarkdown(), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $intro = [];
        $byNumber = [];

        foreach ($sections as $section) {
            if (preg_match('/^## (\d+)\. /', $section, $matches)) {
                $byNumber[(int) $matches[1]] = $section;
            } else {
                $intro[] = $section;
            }
        }

        $tab4Parts = preg_split('/(?=^### )/m', $byNumber[4] ?? '', -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $tab4 = ['identity' => [], 'meaning' => [], 'pronunciation' => [], 'editorial' => []];
        foreach ($tab4Parts as $part) {
            if (preg_match('/^### 1 /', $part)) {
                $tab4['identity'][] = $part;
            } elseif (preg_match('/^### 2 /', $part)) {
                $tab4['meaning'][] = $part;
            } elseif (preg_match('/^### 3 /', $part)) {
                $tab4['pronunciation'][] = $part;
            } elseif (preg_match('/^### 4 /', $part)) {
                $tab4['editorial'][] = $part;
            } else {
                $tab4['identity'][] = $part;
            }
        }

        $groups = [
            'overview' => [...$intro, $byNumber[1] ?? '', $byNumber[2] ?? '', $byNumber[3] ?? ''],
            'identity' => $tab4['identity'],
            'meaning' => $tab4['meaning'],
            'pronunciation' => [...$tab4['pronunciation'], ...$tab4['editorial']],
            'workflow' => array_intersect_key($byNumber, array_flip([5, 6, 7, 8, 9])),
        ];

        return collect($groups)->map(fn (array $parts): string => Str::markdown(implode("\n\n", array_filter($parts)), [
            'html_input' => 'escape',
            'allow_unsafe_links' => false,
        ]))->all();
    }

    private function guideMarkdown(): string
    {
        $path = base_path('docs/panduan-input-entri.md');
        abort_unless(File::exists($path), 404);

        $markdown = File::get($path);
        $markdown = preg_replace('/```mermaid\s.*?```\s*/s', '', $markdown, 1) ?? $markdown;

        return str_replace([
            '[Keputusan ortografi](keputusan-ortografi.md)',
            '[Kamus data](data-dictionary.md)',
            '[Rencana implementasi](../RENCANA-IMPLEMENTASI-KAMUS.md)',
        ], [
            'Keputusan ortografi (dokumen internal proyek)',
            'Kamus data (dokumen internal proyek)',
            'Rencana implementasi (dokumen internal proyek)',
        ], $markdown);
    }
}
