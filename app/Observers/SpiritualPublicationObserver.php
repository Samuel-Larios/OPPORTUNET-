<?php

namespace App\Observers;

use App\Models\SpiritualPublication;
use App\Services\PublicationNewsletterService;
use Throwable;

class SpiritualPublicationObserver
{
    public function saved(SpiritualPublication $publication): void
    {
        $isNewPublication = $publication->wasRecentlyCreated && $publication->actif;
        $becamePublished = $publication->wasChanged('actif') && $publication->actif;

        if (! $isNewPublication && ! $becamePublished) {
            return;
        }

        try {
            app(PublicationNewsletterService::class)->sendForPublishedSpiritualPublication($publication);
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
