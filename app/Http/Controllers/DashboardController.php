<?php

namespace App\Http\Controllers;

use App\Models\DictionaryCategory;
use App\Models\DictionaryEntry;
use App\Models\DictionaryLemma;
use App\Models\DictionaryVisit;
use App\Models\User;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $user = auth()->user();

        $userCountsQuery = User::query();
        if (! $user->isDictionaryAdmin()) {
            $userCountsQuery->where('managed_by', $user->id);
        }
        $userCounts = $userCountsQuery
            ->select('role', DB::raw('count(*) as aggregate'))
            ->groupBy('role')
            ->pluck('aggregate', 'role');

        $lemmaQuery = DictionaryLemma::query();
        if (! ($user->isDictionaryAdmin() || $user->isAssessor())) {
            $lemmaQuery->where('created_by', $user->id);
        }

        $entryCounts = (clone $lemmaQuery)
            ->select('status', DB::raw('count(*) as aggregate'))
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $latestEntries = (clone $lemmaQuery)
            ->with(['senses.category', 'senses.translations', 'creator', 'reviewer'])
            ->latest()
            ->take(5);

        $popularEntries = DictionaryVisit::query()
            ->select(['dictionary_entry_id', 'dictionary_lemma_id'])
            ->selectRaw('count(*) as aggregate')
            ->where(function ($query) use ($user): void {
                $query->whereHas('entry', fn ($entryQuery) => $entryQuery->where('created_by', $user->id))
                    ->orWhereHas('lemma', fn ($lemmaQuery) => $lemmaQuery->where('created_by', $user->id));
            })
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('dictionary_entry_id', 'dictionary_lemma_id')
            ->with(['entry', 'lemma'])
            ->orderByDesc('aggregate')
            ->take(5)
            ->get();

        $dailyVisitCounts = DictionaryVisit::query()
            ->selectRaw('DATE(occurred_on) as visit_date, count(DISTINCT ip_hash) as aggregate')
            ->whereDate('occurred_on', '>=', now()->subDays(6)->toDateString())
            ->groupByRaw('DATE(occurred_on)')
            ->pluck('aggregate', 'visit_date');

        $visitorTrend = collect(CarbonPeriod::create(now()->subDays(6)->toDateString(), today()->toDateString()))
            ->map(fn ($date) => [
                'label' => $date->format('d M'),
                'value' => (int) $dailyVisitCounts->get($date->toDateString(), 0),
            ]);

        $trendMax = max(1, $visitorTrend->max('value'));
        $detailVisitCount = DictionaryVisit::query()
            ->where(fn ($query) => $query->whereNotNull('dictionary_entry_id')->orWhereNotNull('dictionary_lemma_id'))
            ->count();
        $searchVisitCount = DictionaryVisit::query()->whereNotNull('keyword')->count();
        $activityCount = max(1, $searchVisitCount + $detailVisitCount);
        $visitCount = DB::query()
            ->fromSub(
                DictionaryVisit::query()
                    ->selectRaw('DATE(occurred_on) as visit_date, ip_hash')
                    ->groupByRaw('DATE(occurred_on), ip_hash'),
                'unique_visitors'
            )
            ->count();
        $visitTodayCount = DictionaryVisit::query()
            ->whereDate('occurred_on', today())
            ->distinct()
            ->count('ip_hash');

        return view('dashboard', [
            'categoryCount' => $user->isDictionaryAdmin() ? DictionaryCategory::query()->count() : 0,
            'entryCount' => (clone $lemmaQuery)->count(),
            'publishedCount' => $entryCounts->get(DictionaryEntry::STATUS_PUBLISHED, 0),
            'draftCount' => $entryCounts->get(DictionaryEntry::STATUS_DRAFT, 0),
            'submittedCount' => $entryCounts->get(DictionaryEntry::STATUS_SUBMITTED, 0),
            'reviewedCount' => $entryCounts->get(DictionaryEntry::STATUS_REVIEWED, 0),
            'rejectedCount' => $entryCounts->get(DictionaryEntry::STATUS_REJECTED, 0),
            'entryCounts' => $entryCounts,
            'latestEntries' => $latestEntries->get(),
            'visitCount' => $visitCount,
            'visitTodayCount' => $visitTodayCount,
            'searchVisitCount' => $searchVisitCount,
            'detailVisitCount' => $detailVisitCount,
            'visitorTrend' => $visitorTrend,
            'trendMax' => $trendMax,
            'searchVisitPercent' => round(($searchVisitCount / $activityCount) * 100),
            'detailVisitPercent' => round(($detailVisitCount / $activityCount) * 100),
            'popularEntryMax' => max(1, (int) $popularEntries->max('aggregate')),
            'popularEntries' => $popularEntries,
            'userCounts' => $userCounts,
            'user' => $user,
        ]);
    }
}
