<?php

namespace App\Support;

use App\Models\BlogArticle;
use App\Models\BlogCommentaire;
use App\Models\CandidatureOffre;
use App\Models\Contact;
use App\Models\CvDepot;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\MurDePriere;
use App\Models\NewsletterSubscriber;
use App\Models\Opportunite;
use App\Models\SiteVisit;
use App\Models\Temoignage;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class WeeklySiteReportBuilder
{
    /**
     * @return array<string, mixed>
     */
    public function build(?Carbon $referenceDate = null): array
    {
        $referenceDate ??= now();

        $periodEnd = $referenceDate->copy()->endOfDay();
        $periodStart = $referenceDate->copy()->subDays(6)->startOfDay();

        $visitsQuery = SiteVisit::query()
            ->whereBetween('visited_at', [$periodStart, $periodEnd]);

        $visitsByRoute = (clone $visitsQuery)
            ->selectRaw('COALESCE(route_name, path) as label, COUNT(*) as aggregate')
            ->groupBy('label')
            ->orderByDesc('aggregate')
            ->limit(7)
            ->get()
            ->map(fn ($row) => [
                'label' => $this->humanizeRouteLabel((string) $row->label),
                'count' => (int) $row->aggregate,
            ]);

        $visitsByDay = (clone $visitsQuery)
            ->selectRaw('DATE(visited_at) as date_value, COUNT(*) as aggregate')
            ->groupBy('date_value')
            ->orderBy('date_value')
            ->get()
            ->map(fn ($row) => [
                'date' => Carbon::parse($row->date_value),
                'count' => (int) $row->aggregate,
            ]);

        return [
            'generated_at' => now(),
            'period_start' => $periodStart,
            'period_end' => $periodEnd,
            'traffic' => [
                'total_visits' => (clone $visitsQuery)->count(),
                'unique_visitors' => (clone $visitsQuery)->distinct('visitor_hash')->count('visitor_hash'),
                'connected_visits' => (clone $visitsQuery)->whereNotNull('user_id')->count(),
                'top_pages' => $visitsByRoute,
                'visits_by_day' => $visitsByDay,
            ],
            'activity' => [
                'new_users' => User::query()->whereBetween('created_at', [$periodStart, $periodEnd])->count(),
                'newsletter_subscribers' => NewsletterSubscriber::query()
                    ->where('is_active', true)
                    ->whereBetween('created_at', [$periodStart, $periodEnd])
                    ->count(),
                'contacts' => Contact::query()->whereBetween('created_at', [$periodStart, $periodEnd])->count(),
                'cv_depots' => CvDepot::query()->whereBetween('created_at', [$periodStart, $periodEnd])->count(),
                'offer_applications' => CandidatureOffre::query()->whereBetween('created_at', [$periodStart, $periodEnd])->count(),
                'training_registrations' => InscriptionFormation::query()->whereBetween('created_at', [$periodStart, $periodEnd])->count(),
                'prayer_requests' => MurDePriere::query()
                    ->where('type', 'priere')
                    ->whereBetween('created_at', [$periodStart, $periodEnd])
                    ->count(),
                'testimonials' => Temoignage::query()->whereBetween('created_at', [$periodStart, $periodEnd])->count(),
                'articles_published' => BlogArticle::query()->whereBetween('publie_le', [$periodStart, $periodEnd])->count(),
                'offers_published' => Opportunite::query()->whereBetween('date_publication', [$periodStart, $periodEnd])->count(),
                'trainings_published' => Formation::query()->whereBetween('created_at', [$periodStart, $periodEnd])->count(),
                'article_comments' => BlogCommentaire::query()->whereBetween('created_at', [$periodStart, $periodEnd])->count(),
            ],
            'totals' => [
                'users' => User::query()->count(),
                'offers' => Opportunite::query()->count(),
                'articles' => BlogArticle::query()->count(),
                'trainings' => Formation::query()->count(),
                'newsletter_subscribers' => NewsletterSubscriber::query()->where('is_active', true)->count(),
            ],
        ];
    }

    protected function humanizeRouteLabel(string $label): string
    {
        $map = [
            'home' => 'Accueil',
            'offers.index' => 'Offres & opportunités',
            'offers.show' => 'Détail d’une offre',
            'articles.index' => 'Articles',
            'articles.show' => 'Détail d’un article',
            'cv.services.index' => 'Dépôt CV et services',
            'trainings.index' => 'Formations',
            'contact.prayer.index' => 'Contact et prière',
            'community.prayers.index' => 'Mur de prière',
            'community.testimonials.index' => 'Témoignages',
        ];

        return $map[$label] ?? $label;
    }
}
