<?php

namespace App\Livewire\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

trait ManagesScheduledPublications
{
    /**
     * Publishes scheduled content for a specific model type.
     *
     * @param  class-string<Model>  $modelClass
     */
    public function refreshScheduledPublications(?string $modelClass = null): void
    {
        if ($modelClass === null) {
            return;
        }

        $now = now();

        $modelClass::query()
            ->where('auto_publish', true)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->each(function (Model $record) use ($now): void {
                $record->forceFill([
                    'actif' => true,
                    'auto_publish' => false,
                    'scheduled_for' => null,
                    'published_at' => $record->published_at ?: $now,
                ])->save();
            });
    }

    /**
     * Publishes scheduled content with status tracking.
     *
     * @param  class-string<Model>  $modelClass
     * @param  callable(Model, Carbon): void|null  $beforeSave
     */
    public function refreshScheduledStatusPublications(?string $modelClass = null, string $defaultStatus = 'publie', ?callable $beforeSave = null): void
    {
        if ($modelClass === null) {
            return;
        }

        $now = now();

        $modelClass::query()
            ->where('auto_publish', true)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->each(function (Model $record) use ($now, $defaultStatus, $beforeSave): void {
                $publishAt = $record->scheduled_for instanceof Carbon ? $record->scheduled_for : $now;

                $record->forceFill([
                    'statut' => $record->scheduled_status ?: $defaultStatus,
                    'auto_publish' => false,
                    'scheduled_for' => null,
                    'scheduled_status' => null,
                    'published_at' => $record->published_at ?: $publishAt,
                ]);

                if ($beforeSave) {
                    $beforeSave($record, $publishAt);
                }

                $record->save();
            });
    }
}
