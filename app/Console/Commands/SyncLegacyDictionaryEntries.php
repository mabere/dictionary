<?php

namespace App\Console\Commands;

use App\Models\DictionaryEntry;
use App\Services\LegacyDictionaryEntrySynchronizer;
use Illuminate\Console\Command;

class SyncLegacyDictionaryEntries extends Command
{
    protected $signature = 'dictionary:sync-legacy {--dry-run : Count legacy entries without writing normalized records}';

    protected $description = 'Synchronize legacy dictionary entries into the normalized lexicographic schema';

    public function handle(LegacyDictionaryEntrySynchronizer $synchronizer): int
    {
        $count = DictionaryEntry::query()->count();

        if ($this->option('dry-run')) {
            $this->info("{$count} legacy entries would be synchronized. No records were written.");

            return self::SUCCESS;
        }

        $progress = $this->output->createProgressBar($count);
        $progress->start();

        DictionaryEntry::query()
            ->with('examples')
            ->chunkById(100, function ($entries) use ($synchronizer, $progress): void {
                foreach ($entries as $entry) {
                    $synchronizer->sync($entry);
                    $progress->advance();
                }
            });

        $progress->finish();
        $this->newLine(2);
        $this->info("{$count} legacy entries synchronized.");

        return self::SUCCESS;
    }
}
