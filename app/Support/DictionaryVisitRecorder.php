<?php

namespace App\Support;

use App\Models\DictionaryEntry;
use App\Models\DictionaryLemma;
use App\Models\DictionaryVisit;
use Illuminate\Http\Request;

class DictionaryVisitRecorder
{
    public function record(
        Request $request,
        ?DictionaryEntry $entry = null,
        ?string $keyword = null,
        ?string $direction = null,
    ): void {
        $this->recordVisit($request, $entry, null, $keyword, $direction);
    }

    public function recordLemma(Request $request, DictionaryLemma $lemma): void
    {
        $legacyEntry = $lemma->legacy_entry_id
            ? DictionaryEntry::query()->find($lemma->legacy_entry_id)
            : null;

        $this->recordVisit($request, $legacyEntry, $lemma, null, null);
    }

    private function recordVisit(
        Request $request,
        ?DictionaryEntry $entry,
        ?DictionaryLemma $lemma,
        ?string $keyword,
        ?string $direction,
    ): void {
        $ipHash = hash_hmac('sha256', (string) $request->ip(), config('app.key'));
        $userAgentHash = $request->userAgent()
            ? hash_hmac('sha256', $request->userAgent(), config('app.key'))
            : null;
        $keyword = filled($keyword) ? mb_substr($keyword, 0, 255) : null;
        $occurredOn = now()->toDateString();

        $routeName = $request->route()?->getName();

        $exists = DictionaryVisit::query()
            ->where('route_name', $routeName)
            ->when($entry, fn ($query) => $query->where('dictionary_entry_id', $entry->id), fn ($query) => $query->whereNull('dictionary_entry_id'))
            ->when($lemma, fn ($query) => $query->where('dictionary_lemma_id', $lemma->id), fn ($query) => $query->whereNull('dictionary_lemma_id'))
            ->when($keyword, fn ($query) => $query->where('keyword', $keyword), fn ($query) => $query->whereNull('keyword'))
            ->when($direction, fn ($query) => $query->where('search_direction', $direction), fn ($query) => $query->whereNull('search_direction'))
            ->where('ip_hash', $ipHash)
            ->whereDate('occurred_on', $occurredOn)
            ->exists();

        if ($exists) {
            return;
        }

        try {
            DictionaryVisit::create([
                'path' => $request->path(),
                'route_name' => $routeName,
                'dictionary_entry_id' => $entry?->id,
                'dictionary_lemma_id' => $lemma?->id,
                'keyword' => $keyword,
                'search_direction' => $direction,
                'ip_hash' => $ipHash,
                'user_agent_hash' => $userAgentHash,
                'occurred_on' => $occurredOn,
            ]);
        } catch (\Throwable) {
            // Silently ignore concurrent duplicate tracking attempts
        }
    }
}
