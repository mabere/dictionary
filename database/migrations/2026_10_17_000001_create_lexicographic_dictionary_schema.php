<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orthography_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('version', 32);
            $table->string('status', 24)->default('draft')->index();
            $table->json('rules')->nullable();
            $table->text('citation')->nullable();
            $table->text('change_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['name', 'version']);
        });

        Schema::create('dictionary_sources', function (Blueprint $table) {
            $table->id();
            $table->string('source_type', 40)->index();
            $table->text('citation');
            $table->date('published_on')->nullable();
            $table->string('rights_status', 40)->default('unknown')->index();
            $table->string('visibility', 24)->default('internal')->index();
            $table->string('permission_reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('dictionary_lemmas', function (Blueprint $table) {
            $table->id();
            $table->ulid('public_id')->unique();
            $table->string('headword');
            $table->string('headword_normalized')->index();
            $table->foreignId('orthography_profile_id')->nullable()->constrained('orthography_profiles')->nullOnDelete();
            $table->foreignId('legacy_entry_id')->nullable()->unique()->constrained('dictionary_entries')->nullOnDelete();
            $table->string('status', 24)->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'headword_normalized']);
        });

        Schema::create('dictionary_lemma_forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lemma_id')->constrained('dictionary_lemmas')->cascadeOnDelete();
            $table->string('form');
            $table->string('form_normalized')->index();
            $table->string('form_type', 32)->default('variant')->index();
            $table->string('region_label')->nullable()->index();
            $table->text('notes')->nullable();
            $table->foreignId('source_id')->nullable()->constrained('dictionary_sources')->nullOnDelete();
            $table->timestamps();
            $table->unique(['lemma_id', 'form_normalized', 'form_type'], 'lemma_forms_identity_unique');
        });

        Schema::create('dictionary_senses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lemma_id')->constrained('dictionary_lemmas')->cascadeOnDelete();
            $table->unsignedSmallInteger('sense_number');
            $table->text('definition')->nullable();
            $table->string('definition_language', 8)->default('id');
            $table->string('part_of_speech', 64)->nullable()->index();
            $table->json('grammatical_features')->nullable();
            $table->string('domain_label')->nullable()->index();
            $table->string('register_label')->nullable()->index();
            $table->text('usage_note')->nullable();
            $table->text('cultural_note')->nullable();
            $table->string('status', 24)->default('draft')->index();
            $table->foreignId('source_id')->nullable()->constrained('dictionary_sources')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['lemma_id', 'sense_number']);
        });

        Schema::create('dictionary_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sense_id')->constrained('dictionary_senses')->cascadeOnDelete();
            $table->string('language_code', 8)->index();
            $table->string('form');
            $table->string('form_normalized')->index();
            $table->string('translation_type', 32)->default('equivalent')->index();
            $table->text('context_note')->nullable();
            $table->string('status', 24)->default('draft')->index();
            $table->foreignId('source_id')->nullable()->constrained('dictionary_sources')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['sense_id', 'language_code']);
        });

        Schema::create('dictionary_examples', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sense_id')->constrained('dictionary_senses')->cascadeOnDelete();
            $table->text('tolaki_text');
            $table->string('region_label')->nullable()->index();
            $table->string('evidence_method', 32)->default('unknown')->index();
            $table->text('elicitation_note')->nullable();
            $table->string('status', 24)->default('draft')->index();
            $table->foreignId('source_id')->nullable()->constrained('dictionary_sources')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('dictionary_example_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('example_id')->constrained('dictionary_examples')->cascadeOnDelete();
            $table->string('language_code', 8)->index();
            $table->text('text');
            $table->text('translation_note')->nullable();
            $table->string('status', 24)->default('draft')->index();
            $table->foreignId('source_id')->nullable()->constrained('dictionary_sources')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->index(['example_id', 'language_code']);
        });

        Schema::create('dictionary_pronunciations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lemma_id')->nullable()->constrained('dictionary_lemmas')->cascadeOnDelete();
            $table->foreignId('sense_id')->nullable()->constrained('dictionary_senses')->cascadeOnDelete();
            $table->text('transcription')->nullable();
            $table->string('transcription_system', 64)->nullable();
            $table->string('audio_path')->nullable();
            $table->string('region_label')->nullable()->index();
            $table->foreignId('source_id')->nullable()->constrained('dictionary_sources')->nullOnDelete();
            $table->string('rights_status', 40)->default('unknown')->index();
            $table->string('status', 24)->default('draft')->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['lemma_id', 'sense_id']);
        });

        Schema::create('dictionary_morphological_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lemma_form_id')->constrained('dictionary_lemma_forms')->cascadeOnDelete();
            $table->foreignId('base_lemma_id')->nullable()->constrained('dictionary_lemmas')->nullOnDelete();
            $table->text('segmentation')->nullable();
            $table->json('morphemes')->nullable();
            $table->text('analysis_note')->nullable();
            $table->string('status', 24)->default('draft')->index();
            $table->foreignId('source_id')->nullable()->constrained('dictionary_sources')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('dictionary_sense_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sense_id')->constrained('dictionary_senses')->cascadeOnDelete();
            $table->foreignId('related_sense_id')->constrained('dictionary_senses')->cascadeOnDelete();
            $table->string('relation_type', 32)->index();
            $table->text('note')->nullable();
            $table->foreignId('source_id')->nullable()->constrained('dictionary_sources')->nullOnDelete();
            $table->timestamps();
            $table->unique(['sense_id', 'related_sense_id', 'relation_type'], 'sense_relations_identity_unique');
        });

        Schema::create('dictionary_evidence_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('dictionary_sources')->cascadeOnDelete();
            $table->string('evidence_type', 40);
            $table->unsignedBigInteger('evidence_id');
            $table->text('evidence_note')->nullable();
            $table->string('confidence', 24)->default('unassessed')->index();
            $table->timestamps();
            $table->index(['evidence_type', 'evidence_id']);
            $table->unique(['source_id', 'evidence_type', 'evidence_id'], 'evidence_source_identity_unique');
        });

        Schema::create('dictionary_editorial_events', function (Blueprint $table) {
            $table->id();
            $table->string('entity_type', 40)->index();
            $table->unsignedBigInteger('entity_id');
            $table->string('action', 40)->index();
            $table->string('from_status', 24)->nullable();
            $table->string('to_status', 24)->nullable();
            $table->json('change_set')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['entity_type', 'entity_id', 'created_at'], 'editorial_entity_history_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dictionary_editorial_events');
        Schema::dropIfExists('dictionary_evidence_links');
        Schema::dropIfExists('dictionary_sense_relations');
        Schema::dropIfExists('dictionary_morphological_analyses');
        Schema::dropIfExists('dictionary_pronunciations');
        Schema::dropIfExists('dictionary_example_translations');
        Schema::dropIfExists('dictionary_examples');
        Schema::dropIfExists('dictionary_translations');
        Schema::dropIfExists('dictionary_senses');
        Schema::dropIfExists('dictionary_lemma_forms');
        Schema::dropIfExists('dictionary_lemmas');
        Schema::dropIfExists('dictionary_sources');
        Schema::dropIfExists('orthography_profiles');
    }
};
