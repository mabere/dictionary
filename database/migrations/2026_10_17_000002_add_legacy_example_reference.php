<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dictionary_examples', function (Blueprint $table) {
            $table->foreignId('legacy_example_id')
                ->nullable()
                ->unique()
                ->constrained('example_sentences')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dictionary_examples', function (Blueprint $table) {
            $table->dropConstrainedForeignId('legacy_example_id');
        });
    }
};
