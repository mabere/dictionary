<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dictionary_entries', function (Blueprint $table) {
            $table->id();
            $table->string('source_word');
            $table->string('source_word_normalized')->index();
            $table->string('target_word');
            $table->string('target_word_normalized')->index();
            $table->string('reverse_word')->nullable();
            $table->string('source_language')->default('Bahasa A')->index();
            $table->string('target_language')->default('Bahasa B')->index();
            $table->string('part_of_speech')->nullable()->index();
            $table->string('phonetic')->nullable();
            $table->string('pronunciation_audio')->nullable();
            $table->text('definition')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('dictionary_categories')->nullOnDelete();
            $table->string('status')->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(
                ['source_word_normalized', 'target_word_normalized', 'source_language', 'target_language', 'part_of_speech'],
                'dictionary_entries_unique_translation'
            );
            $table->index(['status', 'category_id']);
            $table->index(['status', 'part_of_speech']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dictionary_entries');
    }
};
