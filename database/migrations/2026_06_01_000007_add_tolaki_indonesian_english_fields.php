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
            if (! Schema::hasColumn('dictionary_entries', 'tolaki_word')) {
                $table->string('tolaki_word')->nullable()->after('id');
                $table->string('tolaki_word_normalized')->nullable()->index()->after('tolaki_word');
                $table->string('indonesian_word')->nullable()->after('tolaki_word_normalized');
                $table->string('indonesian_word_normalized')->nullable()->index()->after('indonesian_word');
                $table->string('english_word')->nullable()->after('indonesian_word_normalized');
                $table->string('english_word_normalized')->nullable()->index()->after('english_word');
                $table->text('usage_note')->nullable()->after('definition');
                $table->text('cultural_note')->nullable()->after('usage_note');
            }
        });

        Schema::table('example_sentences', function (Blueprint $table) {
            if (! Schema::hasColumn('example_sentences', 'tolaki_sentence')) {
                $table->text('tolaki_sentence')->nullable()->after('dictionary_entry_id');
                $table->text('indonesian_sentence')->nullable()->after('tolaki_sentence');
                $table->text('english_sentence')->nullable()->after('indonesian_sentence');
            }
        });

        DictionaryEntry::query()
            ->withoutGlobalScopes()
            ->with('examples')
            ->get()
            ->each(function (DictionaryEntry $entry): void {
                $entry->forceFill([
                    'tolaki_word' => $entry->tolaki_word ?: $entry->source_word,
                    'indonesian_word' => $entry->indonesian_word ?: $entry->target_word,
                    'english_word' => $entry->english_word ?: $entry->reverse_word,
                    'usage_note' => $entry->usage_note ?: $entry->notes,
                ])->save();

                foreach ($entry->examples as $example) {
                    $example->forceFill([
                        'tolaki_sentence' => $example->tolaki_sentence ?: $example->source_sentence,
                        'indonesian_sentence' => $example->indonesian_sentence ?: $example->target_sentence,
                    ])->save();
                }
            });
    }

    public function down(): void
    {
        Schema::table('example_sentences', function (Blueprint $table) {
            foreach (['english_sentence', 'indonesian_sentence', 'tolaki_sentence'] as $column) {
                if (Schema::hasColumn('example_sentences', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('dictionary_entries', function (Blueprint $table) {
            foreach ([
                'cultural_note',
                'usage_note',
                'english_word_normalized',
                'english_word',
                'indonesian_word_normalized',
                'indonesian_word',
                'tolaki_word_normalized',
                'tolaki_word',
            ] as $column) {
                if (Schema::hasColumn('dictionary_entries', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
