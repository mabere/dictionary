<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dictionary_visits', function (Blueprint $table) {
            $table->foreignId('dictionary_lemma_id')->nullable()->after('dictionary_entry_id')
                ->constrained('dictionary_lemmas')->nullOnDelete();
            $table->index(['dictionary_lemma_id', 'occurred_on']);
        });
    }

    public function down(): void
    {
        Schema::table('dictionary_visits', function (Blueprint $table) {
            $table->dropIndex(['dictionary_lemma_id', 'occurred_on']);
            $table->dropConstrainedForeignId('dictionary_lemma_id');
        });
    }
};
