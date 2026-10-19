<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dictionary_visits', function (Blueprint $table) {
            $table->id();
            $table->string('path');
            $table->string('route_name')->nullable()->index();
            $table->foreignId('dictionary_entry_id')->nullable()->constrained('dictionary_entries')->nullOnDelete();
            $table->string('keyword')->nullable()->index();
            $table->string('ip_hash', 64)->index();
            $table->string('user_agent_hash', 64)->nullable();
            $table->date('occurred_on')->index();
            $table->timestamps();

            $table->index(['dictionary_entry_id', 'occurred_on']);
            $table->index(['occurred_on', 'ip_hash']);
            $table->unique([
                'route_name',
                'dictionary_entry_id',
                'keyword',
                'ip_hash',
                'occurred_on',
            ], 'dictionary_visits_daily_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dictionary_visits');
    }
};
