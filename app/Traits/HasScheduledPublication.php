<?php

namespace App\Traits;

use App\Models\BlogArticle;
use App\Models\Formation;
use App\Models\Opportunite;
use Illuminate\Support\Carbon;

trait HasScheduledPublication
{
    public function publishIfDue(?Carbon $now = null): bool
    {
        if (! $this->getAttribute('auto_publish') || ! $this->getAttribute('scheduled_for')) {
            return false;
        }

        $now = $now ?? now((string) config('app.schedule_timezone', config('app.timezone')));
        $scheduledFor = $this->getAttribute('scheduled_for') instanceof Carbon
            ? $this->getAttribute('scheduled_for')
            : Carbon::parse($this->getAttribute('scheduled_for'));

        if ($scheduledFor->gt($now)) {
            return false;
        }

        $this->forceFill($this->buildScheduledPublicationPayload($scheduledFor))->save();

        return true;
    }

    protected function buildScheduledPublicationPayload(Carbon $publishAt): array
    {
        $payload = [
            'auto_publish' => false,
            'scheduled_for' => null,
            'published_at' => $this->getAttribute('published_at') ?: $publishAt,
        ];

        if ($this->hasScheduledAttribute('actif')) {
            $payload['actif'] = true;
        }

        if ($this->hasScheduledAttribute('statut')) {
            $scheduledStatus = $this->getAttribute('scheduled_status');
            $payload['statut'] = $scheduledStatus ?: $this->defaultScheduledStatus();
            $payload['scheduled_status'] = null;

            if ($scheduledStatus === null && $this->getAttribute('statut') === 'brouillon') {
                $payload['statut'] = $this->defaultScheduledStatus();
            }
        }

        if ($this instanceof BlogArticle && $this->hasScheduledAttribute('publie_le')) {
            $payload['publie_le'] = $publishAt;
        }

        if ($this instanceof Opportunite && $this->hasScheduledAttribute('date_publication')) {
            $payload['date_publication'] = $publishAt->toDateString();

            if ($this->hasScheduledAttribute('valide_le') && ! $this->getAttribute('valide_le')) {
                $payload['valide_le'] = $publishAt;
            }
        }

        return $payload;
    }

    protected function hasScheduledAttribute(string $attribute): bool
    {
        return in_array($attribute, $this->getFillable(), true)
            || array_key_exists($attribute, $this->attributes);
    }

    protected function defaultScheduledStatus(): string
    {
        return $this instanceof Formation ? 'ouverte' : 'publie';
    }
}
