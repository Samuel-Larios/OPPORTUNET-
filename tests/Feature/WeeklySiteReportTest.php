<?php

namespace Tests\Feature;

use App\Mail\WeeklySiteReportMail;
use App\Models\BlogArticle;
use App\Models\Contact;
use App\Models\NewsletterSubscriber;
use App\Models\Opportunite;
use App\Models\SiteVisit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WeeklySiteReportTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_public_html_pages_are_tracked_but_login_is_not(): void
    {
        $this->get(route('offers.index'))->assertOk();
        $this->get(route('login'))->assertOk();

        $this->assertDatabaseCount('site_visits', 1);
        $this->assertDatabaseHas('site_visits', [
            'route_name' => 'offers.index',
            'path' => '/offres-opportunites',
        ]);
    }

    public function test_weekly_site_report_command_sends_mail_with_aggregated_metrics(): void
    {
        Mail::fake();
        Carbon::setTestNow('2026-06-14 20:00:00');

        $author = User::factory()->create([
            'prenom' => 'Samuel',
            'nom' => 'Visiteur',
            'email' => 'samuel@example.com',
        ]);

        User::factory()->create([
            'prenom' => 'Mireille',
            'nom' => 'Abonnee',
            'email' => 'mireille@example.com',
        ]);

        NewsletterSubscriber::query()->create([
            'prenom' => 'Naomi',
            'email' => 'naomi@example.com',
            'langue' => 'fr',
            'source' => 'website',
            'is_active' => true,
            'subscribed_at' => now()->subDays(2),
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        Contact::query()->create([
            'prenom' => 'Paul',
            'nom' => 'Contact',
            'email' => 'paul@example.com',
            'message' => 'Bonjour',
            'sujet' => 'information',
            'priorite' => 'normale',
            'statut' => 'non_lu',
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        Opportunite::query()->create([
            'titre' => 'Chargé de mission',
            'slug' => 'charge-mission-report',
            'type' => 'emploi',
            'description' => 'Mission test',
            'statut' => 'publie',
            'date_publication' => now()->subDays(3),
            'created_at' => now()->subDays(3),
            'updated_at' => now()->subDays(3),
        ]);

        BlogArticle::query()->create([
            'user_id' => $author->id,
            'titre' => 'Article test',
            'slug' => 'article-test-report',
            'contenu' => 'Contenu test',
            'statut' => 'publie',
            'publie_le' => now()->subDays(4),
            'created_at' => now()->subDays(4),
            'updated_at' => now()->subDays(4),
        ]);

        SiteVisit::query()->create([
            'route_name' => 'home',
            'path' => '/',
            'locale' => 'fr',
            'visitor_hash' => 'hash-a',
            'visited_at' => now()->subDays(2),
            'created_at' => now()->subDays(2),
            'updated_at' => now()->subDays(2),
        ]);

        SiteVisit::query()->create([
            'route_name' => 'offers.index',
            'path' => '/offres-opportunites',
            'locale' => 'fr',
            'visitor_hash' => 'hash-a',
            'visited_at' => now()->subDays(1),
            'created_at' => now()->subDays(1),
            'updated_at' => now()->subDays(1),
        ]);

        SiteVisit::query()->create([
            'route_name' => 'articles.index',
            'path' => '/articles',
            'locale' => 'fr',
            'visitor_hash' => 'hash-b',
            'visited_at' => now()->subDay(),
            'created_at' => now()->subDay(),
            'updated_at' => now()->subDay(),
        ]);

        $this->artisan('report:weekly-site --email=contact@opportunetmondiale.com')
            ->assertSuccessful();

        Mail::assertSent(WeeklySiteReportMail::class, function (WeeklySiteReportMail $mail) {
            return $mail->hasTo('contact@opportunetmondiale.com')
                && $mail->report['traffic']['total_visits'] === 3
                && $mail->report['traffic']['unique_visitors'] === 2
                && $mail->report['activity']['contacts'] === 1
                && $mail->report['activity']['newsletter_subscribers'] === 1
                && $mail->report['activity']['offers_published'] === 1
                && $mail->report['activity']['articles_published'] === 1;
        });

        Carbon::setTestNow();
    }
}
