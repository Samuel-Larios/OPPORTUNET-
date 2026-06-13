<?php

namespace App\Observers;

use App\Models\Verset;
use App\Services\PublicationNewsletterService;

class VersetObserver
{
    public function saved(Verset $verse): void
    {
        $isNewPublication = $verse->wasRecentlyCreated && $verse->actif;
        $becamePublished = $verse->wasChanged('actif') && $verse->actif;

        if (! $isNewPublication && ! $becamePublished) {
            return;
        }

        app(PublicationNewsletterService::class)->sendForPublishedVerse($verse);
    }
}
