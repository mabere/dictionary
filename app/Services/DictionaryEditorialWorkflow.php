<?php

namespace App\Services;

use App\Models\DictionaryEditorialEvent;
use App\Models\DictionaryEntry;
use App\Models\DictionaryLemma;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DictionaryEditorialWorkflow
{
    public function transition(DictionaryLemma $lemma, string $toStatus, ?User $actor, ?string $reason = null): void
    {
        if (! in_array($toStatus, [
            DictionaryEntry::STATUS_DRAFT,
            DictionaryEntry::STATUS_SUBMITTED,
            DictionaryEntry::STATUS_REVIEWED,
            DictionaryEntry::STATUS_PUBLISHED,
            DictionaryEntry::STATUS_REJECTED,
        ], true)) {
            throw ValidationException::withMessages(['status' => 'Status editorial tidak dikenal.']);
        }

        if ($toStatus === DictionaryEntry::STATUS_PUBLISHED && ! $lemma->senses()->exists()) {
            throw ValidationException::withMessages(['senses' => 'Tambahkan minimal satu makna sebelum menerbitkan lema.']);
        }

        DB::transaction(function () use ($lemma, $toStatus, $actor, $reason): void {
            $fromStatus = $lemma->status;
            $reviewing = in_array($toStatus, [
                DictionaryEntry::STATUS_REVIEWED,
                DictionaryEntry::STATUS_PUBLISHED,
                DictionaryEntry::STATUS_REJECTED,
            ], true);

            DictionaryLemma::withoutEvents(function () use ($lemma, $toStatus, $reviewing, $actor): void {
                $lemma->forceFill([
                    'status' => $toStatus,
                    'reviewed_by' => $reviewing ? $actor?->getKey() : null,
                    'reviewed_at' => $reviewing ? now() : null,
                ])->save();
            });

            $lemma->senses()->update(['status' => $toStatus]);
            $senseIds = $lemma->senses()->pluck('id');

            if ($senseIds->isNotEmpty()) {
                DB::table('dictionary_translations')->whereIn('sense_id', $senseIds)->update(['status' => $toStatus]);
                DB::table('dictionary_examples')->whereIn('sense_id', $senseIds)->update(['status' => $toStatus]);
                $exampleIds = DB::table('dictionary_examples')->whereIn('sense_id', $senseIds)->pluck('id');

                if ($exampleIds->isNotEmpty()) {
                    DB::table('dictionary_example_translations')->whereIn('example_id', $exampleIds)->update(['status' => $toStatus]);
                }
            }

            $lemma->pronunciations()->update(['status' => $toStatus]);
            if ($toStatus === DictionaryEntry::STATUS_PUBLISHED) {
                $lemma->pronunciations()
                    ->whereIn('rights_status', ['permission_granted', 'public_domain'])
                    ->where('audio_visibility', 'internal')
                    ->update(['audio_visibility' => 'public']);
            }

            DictionaryEditorialEvent::create([
                'entity_type' => 'lemma',
                'entity_id' => $lemma->getKey(),
                'action' => 'status_changed',
                'from_status' => $fromStatus,
                'to_status' => $toStatus,
                'change_set' => ['status' => ['from' => $fromStatus, 'to' => $toStatus]],
                'reason' => $reason,
                'actor_id' => $actor?->getKey(),
            ]);
        });
    }
}
