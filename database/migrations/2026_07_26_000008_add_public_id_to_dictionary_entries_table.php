<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dictionary_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('dictionary_entries', 'public_id')) {
                $table->string('public_id', 26)->nullable()->after('id');
            }
        });

        DB::table('dictionary_entries')
            ->whereNull('public_id')
            ->lazyById()
            ->each(function (object $entry): void {
                DB::table('dictionary_entries')
                    ->where('id', $entry->id)
                    ->update(['public_id' => (string) Str::ulid()]);
            });

        Schema::table('dictionary_entries', function (Blueprint $table) {
            $table->unique('public_id', 'dictionary_entries_public_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('dictionary_entries', function (Blueprint $table) {
            $table->dropUnique('dictionary_entries_public_id_unique');
            $table->dropColumn('public_id');
        });
    }
};
