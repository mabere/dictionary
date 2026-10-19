<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('example_sentences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dictionary_entry_id')->constrained('dictionary_entries')->cascadeOnDelete();
            $table->text('source_sentence');
            $table->text('target_sentence');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('example_sentences');
    }
};
