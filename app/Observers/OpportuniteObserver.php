<?php

namespace App\Observers;

use App\Models\Opportunite;
use App\Services\PublicationNewsletterService;

class OpportuniteObserver
{
    public function saved(Opportunite $opportunite): void
    {
        $isNewPublication = $opportunite->wasRecentlyCreated && $opportunite->statut === 'publie';
        $becamePublished = $opportunite->wasChanged('statut') && $opportunite->statut === 'publie';

        if (! $isNewPublication && ! $becamePublished) {
            return;
        }

        app(PublicationNewsletterService::class)->sendForPublishedOpportunity($opportunite);
    }
}
