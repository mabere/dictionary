<?php

use App\Models\DictionaryEntry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dictionary_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('dictionary_entries', 'duplicate_key')) {
                $table->string('duplicate_key')->nullable()->after('target_word_normalized');
            }
        });

        DictionaryEntry::query()
            ->withoutGlobalScopes()
            ->get()
            ->each(function (DictionaryEntry $entry) {
                $entry->forceFill([
                    'duplicate_key' => DictionaryEntry::makeDuplicateKey(
                        $entry->source_word,
                        $entry->target_word,
                        $entry->source_language,
                        $entry->target_language,
                        $entry->part_of_speech,
                    ),
                ])->saveQuietly();
            });

        Schema::table('dictionary_entries', function (Blueprint $table) {
            $table->unique('duplicate_key', 'dictionary_entries_duplicate_key_unique');
        });
    }

    public function down(): void
    {
        Schema::table('dictionary_entries', function (Blueprint $table) {
            $table->dropUnique('dictionary_entries_duplicate_key_unique');
            $table->dropColumn('duplicate_key');
        });
    }
};
