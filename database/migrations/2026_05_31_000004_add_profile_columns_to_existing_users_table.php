<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('mahasiswa')->index()->after('email');
            }

            if (! Schema::hasColumn('users', 'nim')) {
                $table->string('nim')->nullable()->unique()->after('role');
            }

            if (! Schema::hasColumn('users', 'study_program')) {
                $table->string('study_program')->nullable()->after('phone');
            }

            if (! Schema::hasColumn('users', 'faculty')) {
                $table->string('faculty')->nullable()->after('study_program');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['faculty', 'study_program', 'nim', 'role'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
