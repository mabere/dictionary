<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dictionary_visits', function (Blueprint $table) {
            $table->string('search_direction', 16)->nullable()->after('keyword')->index();
        });
    }

    public function down(): void
    {
        Schema::table('dictionary_visits', function (Blueprint $table) {
            $table->dropIndex(['search_direction']);
            $table->dropColumn('search_direction');
        });
    }
};
