<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dictionary_senses', function (Blueprint $table) {
            $table->foreignId('category_id')->nullable()->after('part_of_speech')
                ->constrained('dictionary_categories')->nullOnDelete();
        });

        Schema::table('dictionary_pronunciations', function (Blueprint $table) {
            $table->foreignId('legacy_entry_id')->nullable()->unique()
                ->after('sense_id')->constrained('dictionary_entries')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dictionary_pronunciations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('legacy_entry_id');
        });

        Schema::table('dictionary_senses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('category_id');
        });
    }
};
