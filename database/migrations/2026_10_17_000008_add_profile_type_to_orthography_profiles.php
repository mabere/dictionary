<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orthography_profiles', function (Blueprint $table): void {
            $table->string('profile_type', 40)->default('orthography')->after('version');
        });
    }

    public function down(): void
    {
        Schema::table('orthography_profiles', function (Blueprint $table): void {
            $table->dropColumn('profile_type');
        });
    }
};
