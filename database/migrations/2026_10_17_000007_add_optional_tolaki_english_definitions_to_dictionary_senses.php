<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dictionary_senses', function (Blueprint $table): void {
            $table->text('definition_tolaki')->nullable()->after('definition');
            $table->text('definition_english')->nullable()->after('definition_tolaki');
        });
    }

    public function down(): void
    {
        Schema::table('dictionary_senses', function (Blueprint $table): void {
            $table->dropColumn(['definition_tolaki', 'definition_english']);
        });
    }
};
