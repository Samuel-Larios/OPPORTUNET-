<?php

namespace App\Services;

use App\Mail\PublicationNewsletterMail;
use App\Models\BlogArticle;
use App\Models\Formation;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\Opportunite;
use App\Models\SpiritualPublication;
use App\Models\User;
use App\Models\Verset;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PublicationNewsletterService
{
    public function sendFor(Model $content): ?Newsletter
    {
        if (! $this->supports($content)) {
            throw new InvalidArgumentException('Unsupported newsletter content type.');
        }

        if ($this->wasAlreadySent($content)) {
            return null;
        }

        $payload = $this->buildPayload($content);

        $newsletter = Newsletter::query()->create([
            'subject' => $payload['subject'],
            'audience' => 'platform_users_and_subscribers',
            'content_type' => $content::class,
            'content_id' => $content->getKey(),
            'content_title' => $payload['title'],
            'content_url' => $payload['url'],
            'status' => 'draft',
            'meta' => [
                'label' => $payload['label'],
                'summary' => $payload['summary'],
            ],
        ]);

        $this->deliverNewsletter($newsletter, $payload);

        return $newsletter;
    }

    public function deliverNewsletter(Newsletter $newsletter, ?array $payload = null, ?Carbon $sentAt = null): int
    {
        $payload ??= $this->buildPayloadFromNewsletter($newsletter);
        $sentAt ??= now();
        $recipients = $this->resolveRecipients((string) ($newsletter->audience ?: 'platform_users_and_subscribers'));

        foreach ($recipients as $recipient) {
            Mail::to($recipient['email'])->send(new PublicationNewsletterMail([
                ...$payload,
                'recipient_name' => $recipient['name'],
            ]));
        }

        $newsletter->forceFill([
            'subject' => $payload['subject'],
            'content_title' => $payload['title'],
            'content_url' => $payload['url'],
            'status' => 'sent',
            'recipients_count' => $recipients->count(),
            'sent_at' => $sentAt,
            'auto_publish' => false,
            'scheduled_for' => null,
            'published_at' => $newsletter->published_at ?: $sentAt,
            'meta' => array_merge($newsletter->meta ?? [], [
                'label' => $payload['label'],
                'summary' => $payload['summary'],
            ]),
        ])->save();

        return $recipients->count();
    }

    public function sendForPublishedOpportunity(Opportunite $opportunity): ?Newsletter
    {
        if ($opportunity->statut !== 'publie') {
            return null;
        }

        return $this->sendFor($opportunity);
    }

    public function sendForVisibleTraining(Formation $training): ?Newsletter
    {
        if (! in_array($training->statut, ['ouverte', 'complete', 'terminee'], true)) {
            return null;
        }

        return $this->sendFor($training);
    }

    public function sendForPublishedArticle(BlogArticle $article): ?Newsletter
    {
        if ($article->statut !== 'publie') {
            return null;
        }

        return $this->sendFor($article);
    }

    public function sendForPublishedVerse(Verset $verse): ?Newsletter
    {
        if (! $verse->actif) {
            return null;
        }

        return $this->sendFor($verse);
    }

    public function sendForPublishedSpiritualPublication(SpiritualPublication $publication): ?Newsletter
    {
        if (! $publication->actif) {
            return null;
        }

        return $this->sendFor($publication);
    }

    protected function supports(Model $content): bool
    {
        return $content instanceof Opportunite
            || $content instanceof Formation
            || $content instanceof BlogArticle
            || $content instanceof Verset
            || $content instanceof SpiritualPublication;
    }

    protected function wasAlreadySent(Model $content): bool
    {
        return Newsletter::query()
            ->where('content_type', $content::class)
            ->where('content_id', $content->getKey())
            ->exists();
    }

    /**
     * @return array{subject:string,label:string,title:string,summary:string,url:string}
     */
    protected function buildPayloadFromNewsletter(Newsletter $newsletter): array
    {
        $contentModel = $this->resolveSupportedContentFromNewsletter($newsletter);

        if ($contentModel !== null) {
            return $this->buildPayload($contentModel);
        }

        $meta = is_array($newsletter->meta) ? $newsletter->meta : [];
        $label = $meta['label'] ?? $newsletter->content_type ?? 'Publication';
        $summary = trim((string) ($meta['summary'] ?? ''));

        return [
            'subject' => (string) $newsletter->subject,
            'label' => Str::headline((string) $label),
            'title' => (string) ($newsletter->content_title ?: $newsletter->subject),
            'summary' => $summary !== '' ? $summary : 'Une nouvelle publication Opportunet Mondiale est disponible.',
            'url' => (string) ($newsletter->content_url ?: route('home')),
        ];
    }

    /**
     * @return array{subject:string,label:string,title:string,summary:string,url:string}
     */
    protected function buildPayload(Model $content): array
    {
        if ($content instanceof Opportunite) {
            return [
                'subject' => 'Nouvelle opportunite publiee : ' . $content->titre,
                'label' => 'Offre / Opportunite',
                'title' => (string) $content->titre,
                'summary' => Str::limit(trim(strip_tags((string) $content->description)), 220),
                'url' => route('offers.show', $content->slug),
            ];
        }

        if ($content instanceof Formation) {
            return [
                'subject' => 'Nouvelle formation disponible : ' . $content->titre,
                'label' => 'Formation',
                'title' => (string) $content->titre,
                'summary' => Str::limit(trim(strip_tags((string) $content->description_courte)), 220),
                'url' => route('trainings.index', ['formation' => $content->id]),
            ];
        }

        if ($content instanceof BlogArticle) {
            return [
                'subject' => 'Nouvel article publie : ' . $content->titre,
                'label' => 'Article',
                'title' => (string) $content->titre,
                'summary' => Str::limit(trim(strip_tags((string) ($content->extrait ?: $content->contenu))), 220),
                'url' => route('articles.show', $content->slug),
            ];
        }

        if ($content instanceof Verset) {
            return [
                'subject' => 'Nouveau verset publie : ' . $content->reference,
                'label' => 'Verset biblique',
                'title' => (string) $content->reference,
                'summary' => Str::limit(trim(strip_tags((string) $content->texte)), 220),
                'url' => route('spiritual.verses.show', $content),
            ];
        }

        if ($content instanceof SpiritualPublication) {
            return [
                'subject' => $this->spiritualSubject($content),
                'label' => $this->spiritualLabel($content),
                'title' => (string) $content->titre,
                'summary' => Str::limit(trim(strip_tags((string) ($content->extrait ?: $content->contenu))), 220),
                'url' => $this->spiritualUrl($content),
            ];
        }

        throw new InvalidArgumentException('Unsupported newsletter content type.');
    }

    protected function resolveSupportedContentFromNewsletter(Newsletter $newsletter): ?Model
    {
        $contentType = $newsletter->content_type;
        $contentId = $newsletter->content_id;

        if (! is_string($contentType) || $contentType === '' || ! is_numeric($contentId) || ! class_exists($contentType)) {
            return null;
        }

        $content = $contentType::query()->find($contentId);

        return $content instanceof Model && $this->supports($content) ? $content : null;
    }

    protected function spiritualSubject(SpiritualPublication $publication): string
    {
        return match ($publication->type) {
            'pensee' => 'Nouvelle pensee publiee : ' . $publication->titre,
            'exhortation' => 'Nouvelle exhortation publiee : ' . $publication->titre,
            'priere_jour' => 'Nouvelle priere du jour publiee : ' . $publication->titre,
            default => 'Nouveau contenu spirituel publie : ' . $publication->titre,
        };
    }

    protected function spiritualLabel(SpiritualPublication $publication): string
    {
        return match ($publication->type) {
            'pensee' => 'Pensee du jour',
            'exhortation' => 'Exhortation',
            'priere_jour' => 'Priere du jour',
            default => 'Contenu spirituel',
        };
    }

    protected function spiritualUrl(SpiritualPublication $publication): string
    {
        return match ($publication->type) {
            'pensee' => route('spiritual.thoughts.show', $publication->slug),
            'exhortation' => route('spiritual.exhortations.show', $publication->slug),
            'priere_jour' => route('spiritual.daily-prayers.index', ['item' => $publication->slug]),
            default => route('home'),
        };
    }

    /**
     * @return \Illuminate\Support\Collection<int, array{email:string,name:?string}>
     */
    protected function resolveRecipients(string $audience = 'platform_users_and_subscribers'): Collection
    {
        $subscribers = in_array($audience, ['platform_users_and_subscribers', 'subscribers_only'], true)
            ? NewsletterSubscriber::query()
                ->where('is_active', true)
                ->get(['email', 'prenom'])
                ->map(fn (NewsletterSubscriber $subscriber) => [
                    'email' => Str::lower((string) $subscriber->email),
                    'name' => $subscriber->prenom ?: null,
                ])
            : collect();

        $users = in_array($audience, ['platform_users_and_subscribers', 'users_only'], true)
            ? User::query()
                ->where('actif', true)
                ->whereNotNull('email')
                ->get(['email', 'name', 'prenom', 'nom'])
                ->map(fn (User $user) => [
                    'email' => Str::lower((string) $user->email),
                    'name' => $user->fullName(),
                ])
            : collect();

        return collect($subscribers->all())
            ->merge($users->all())
            ->filter(fn (array $recipient) => $recipient['email'] !== '')
            ->unique('email')
            ->values();
    }
}
