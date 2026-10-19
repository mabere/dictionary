<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dictionary_translations', function (Blueprint $table) {
            $table->foreignId('legacy_entry_id')->nullable()->after('sense_id')
                ->constrained('dictionary_entries')->cascadeOnDelete();
            $table->unique(['legacy_entry_id', 'language_code'], 'translation_legacy_language_unique');
        });

        Schema::table('dictionary_example_translations', function (Blueprint $table) {
            $table->foreignId('legacy_example_id')->nullable()->after('example_id')
                ->constrained('example_sentences')->cascadeOnDelete();
            $table->unique(['legacy_example_id', 'language_code'], 'example_translation_legacy_language_unique');
        });
    }

    public function down(): void
    {
        Schema::table('dictionary_example_translations', function (Blueprint $table) {
            $table->dropUnique('example_translation_legacy_language_unique');
            $table->dropConstrainedForeignId('legacy_example_id');
        });

        Schema::table('dictionary_translations', function (Blueprint $table) {
            $table->dropUnique('translation_legacy_language_unique');
            $table->dropConstrainedForeignId('legacy_entry_id');
        });
    }
};
