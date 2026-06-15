<?php

namespace App\Console\Commands;

use App\Models\BlogArticle;
use App\Models\Category;
use App\Models\Formation;
use App\Models\Newsletter;
use App\Models\Opportunite;
use App\Models\Service;
use App\Models\SpiritualPublication;
use App\Models\Verset;
use App\Services\PublicationNewsletterService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Throwable;

class PublishScheduledContentCommand extends Command
{
    protected $signature = 'content:publish-scheduled';

    protected $description = 'Publishes scheduled content entries once their configured time is reached.';

    public function handle(): int
    {
        $now = now((string) config('app.schedule_timezone', config('app.timezone')));
        $published = 0;

        $published += $this->publishBooleanContent(Category::class, $now);
        $published += $this->publishBooleanContent(Service::class, $now);
        $published += $this->publishBooleanContent(Verset::class, $now);
        $published += $this->publishBooleanContent(SpiritualPublication::class, $now);
        $published += $this->publishStatusContent(BlogArticle::class, $now, 'publie', function (BlogArticle $article, Carbon $publishAt): void {
            $article->publie_le = $publishAt;
        });
        $published += $this->publishStatusContent(Formation::class, $now, 'ouverte');
        $published += $this->publishStatusContent(Opportunite::class, $now, 'publie', function (Opportunite $offer, Carbon $publishAt): void {
            $offer->date_publication = $publishAt->toDateString();
            $offer->valide_le = $offer->valide_le ?: $publishAt;
        });
        $published += $this->publishNewsletters($now);

        $this->info("Scheduled publications processed: {$published}");

        return self::SUCCESS;
    }

    /**
     * @param  class-string<Model>  $modelClass
     */
    protected function publishBooleanContent(string $modelClass, Carbon $now): int
    {
        $count = 0;

        $modelClass::query()
            ->where('auto_publish', true)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->each(function (Model $record) use (&$count, $now): void {
                $record->forceFill([
                    'actif' => true,
                    'auto_publish' => false,
                    'scheduled_for' => null,
                    'published_at' => $record->published_at ?: $now,
                ])->save();

                $count++;
            });

        return $count;
    }

    /**
     * @param  class-string<Model>  $modelClass
     * @param  callable(Model, Carbon): void|null  $beforeSave
     */
    protected function publishStatusContent(string $modelClass, Carbon $now, string $defaultStatus, ?callable $beforeSave = null): int
    {
        $count = 0;

        $modelClass::query()
            ->where('auto_publish', true)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->each(function (Model $record) use (&$count, $now, $defaultStatus, $beforeSave): void {
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
                $count++;
            });

        return $count;
    }

    /**
     * Publishes scheduled newsletters.
     */
    protected function publishNewsletters(Carbon $now): int
    {
        $count = 0;
        $newsletterService = app(PublicationNewsletterService::class);

        Newsletter::query()
            ->where('auto_publish', true)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->each(function (Newsletter $newsletter) use (&$count, $now, $newsletterService): void {
                $publishAt = $newsletter->scheduled_for instanceof Carbon ? $newsletter->scheduled_for : $now;

                try {
                    $newsletterService->deliverNewsletter($newsletter, sentAt: $publishAt);
                } catch (Throwable $exception) {
                    report($exception);

                    $newsletter->forceFill([
                        'status' => 'failed',
                        'auto_publish' => false,
                        'scheduled_for' => null,
                    ])->save();

                    $this->error("Scheduled newsletter {$newsletter->id} failed: {$exception->getMessage()}");

                    return;
                }

                $count++;
            });

        return $count;
    }
}
