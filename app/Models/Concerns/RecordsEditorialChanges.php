<?php

namespace App\Models\Concerns;

use App\Models\DictionaryEditorialEvent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait RecordsEditorialChanges
{
    protected static function bootRecordsEditorialChanges(): void
    {
        static::created(function (Model $model): void {
            self::recordEditorialChange($model, 'created');
        });

        static::updated(function (Model $model): void {
            self::recordEditorialChange($model, 'updated');
        });
    }

    private static function recordEditorialChange(Model $model, string $action): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('dictionary_editorial_events')) {
            return;
        }

        $keysToExclude = ['created_at', 'updated_at', 'remember_token'];
        $after = Arr::except($action === 'created' ? $model->getAttributes() : $model->getChanges(), $keysToExclude);

        if ($after === []) {
            return;
        }

        $before = [];

        if ($action === 'updated') {
            foreach (array_keys($after) as $key) {
                $before[$key] = $model->getRawOriginal($key);
                $after[$key] = $model->getAttribute($key);
            }
        }

        DictionaryEditorialEvent::query()->create([
            'entity_type' => class_basename($model),
            'entity_id' => $model->getKey(),
            'action' => $action,
            'from_status' => $before['status'] ?? null,
            'to_status' => $after['status'] ?? null,
            'change_set' => $action === 'created'
                ? ['after' => $after]
                : ['before' => $before, 'after' => $after],
            'actor_id' => auth()->id(),
        ]);
    }
}
