<?php

namespace App\Observers;

use App\Models\Formation;
use App\Services\PublicationNewsletterService;
use Throwable;

class FormationObserver
{
    public function saved(Formation $formation): void
    {
        $visibleStatuses = ['ouverte', 'complete', 'terminee'];
        $isNewVisibleTraining = $formation->wasRecentlyCreated && in_array($formation->statut, $visibleStatuses, true);
        $becameVisible = $formation->wasChanged('statut') && in_array($formation->statut, $visibleStatuses, true);

        if (! $isNewVisibleTraining && ! $becameVisible) {
            return;
        }

        try {
            app(PublicationNewsletterService::class)->sendForVisibleTraining($formation);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
