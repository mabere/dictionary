<?php

namespace App\Providers;

use App\Models\DictionaryVisit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.public', function ($view): void {
            [$publicVisitorTotal, $publicVisitorToday] = \Illuminate\Support\Facades\Cache::remember(
                'public_visitor_summary_stats',
                now()->addMinutes(5),
                function (): array {
                    $total = DB::query()
                        ->fromSub(
                            DictionaryVisit::query()
                                ->selectRaw('DATE(occurred_on) as visit_date, ip_hash')
                                ->groupByRaw('DATE(occurred_on), ip_hash'),
                            'unique_visitors'
                        )
                        ->count();

                    $today = DictionaryVisit::query()
                        ->whereDate('occurred_on', today())
                        ->distinct()
                        ->count('ip_hash');

                    return [$total, $today];
                }
            );

            $view->with([
                'publicVisitorTotal' => $publicVisitorTotal,
                'publicVisitorToday' => $publicVisitorToday,
            ]);
        });
    }
}
