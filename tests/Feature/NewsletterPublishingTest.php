<?php

namespace Tests\Feature;

use App\Mail\PublicationNewsletterMail;
use App\Models\BlogArticle;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\Opportunite;
use App\Models\Role;
use App\Models\SpiritualPublication;
use App\Models\User;
use App\Models\Verset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_subscribe_to_newsletter(): void
    {
        $response = $this->withFormCaptcha()->post(route('newsletter.subscribe'), $this->captchaPayload([
            'prenom' => 'Ruth',
            'email' => 'ruth@example.com',
        ]));

        $response->assertRedirect(route('home'));

        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'ruth@example.com',
            'is_active' => true,
        ]);
    }

    public function test_publishing_an_opportunity_sends_newsletter_to_subscribers_and_users_once(): void
    {
        Mail::fake();

        $role = Role::query()->firstOrCreate([
            'nom' => 'user',
        ], [
            'libelle' => 'Utilisateur',
            'permissions' => json_encode(['view_public']),
            'actif' => true,
        ]);

        NewsletterSubscriber::query()->create([
            'prenom' => 'Abonne',
            'email' => 'abonne@example.com',
            'langue' => 'fr',
            'source' => 'website',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Compte',
            'nom' => 'Actif',
            'email' => 'compte@example.com',
            'actif' => true,
        ]);

        User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Double',
            'nom' => 'Adresse',
            'email' => 'abonne@example.com',
            'actif' => true,
        ]);

        $opportunity = Opportunite::query()->create([
            'titre' => 'Responsable partenariats',
            'slug' => 'responsable-partenariats',
            'type' => 'emploi',
            'description' => 'Structurer les partenariats et accompagner la croissance.',
            'statut' => 'brouillon',
            'teletravail' => false,
            'urgent' => false,
            'en_vedette' => false,
            'vues' => 0,
        ]);

        Mail::assertNothingSent();

        $opportunity->update([
            'statut' => 'publie',
            'date_publication' => now(),
        ]);

        $expectedRecipients = NewsletterSubscriber::query()
            ->where('is_active', true)
            ->pluck('email')
            ->map(fn (string $email) => strtolower($email))
            ->merge(
                User::query()
                    ->where('actif', true)
                    ->pluck('email')
                    ->map(fn (string $email) => strtolower($email))
            )
            ->unique()
            ->count();

        Mail::assertSent(PublicationNewsletterMail::class, $expectedRecipients);
        $this->assertDatabaseHas('newsletters', [
            'content_type' => Opportunite::class,
            'content_id' => $opportunity->id,
            'recipients_count' => $expectedRecipients,
        ]);

        $opportunity->update([
            'titre' => 'Responsable partenariats internationaux',
        ]);

        Mail::assertSent(PublicationNewsletterMail::class, $expectedRecipients);
        $this->assertEquals(1, Newsletter::query()->count());
    }

    public function test_publishing_an_article_sends_newsletter_once(): void
    {
        Mail::fake();

        $role = Role::query()->firstOrCreate([
            'nom' => 'user',
        ], [
            'libelle' => 'Utilisateur',
            'permissions' => json_encode(['view_public']),
            'actif' => true,
        ]);

        NewsletterSubscriber::query()->create([
            'prenom' => 'Lectrice',
            'email' => 'lectrice@example.com',
            'langue' => 'fr',
            'source' => 'website',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Paul',
            'nom' => 'Lecteur',
            'email' => 'paul@example.com',
            'actif' => true,
        ]);

        $author = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Alice',
            'nom' => 'Autrice',
            'email' => 'alice@example.com',
            'actif' => true,
        ]);

        $article = BlogArticle::query()->create([
            'user_id' => $author->id,
            'titre' => 'Article newsletter test',
            'slug' => 'article-newsletter-test',
            'contenu' => 'Contenu de test pour verifier l envoi de la newsletter article.',
            'statut' => 'brouillon',
            'commentaires_actifs' => true,
            'en_vedette' => false,
            'vues' => 0,
            'partages' => 0,
        ]);

        Mail::assertNothingSent();

        $article->update([
            'statut' => 'publie',
            'publie_le' => now(),
        ]);

        $expectedRecipients = NewsletterSubscriber::query()
            ->where('is_active', true)
            ->pluck('email')
            ->map(fn (string $email) => strtolower($email))
            ->merge(
                User::query()
                    ->where('actif', true)
                    ->pluck('email')
                    ->map(fn (string $email) => strtolower($email))
            )
            ->unique()
            ->count();

        Mail::assertSent(PublicationNewsletterMail::class, $expectedRecipients);
        $this->assertDatabaseHas('newsletters', [
            'content_type' => BlogArticle::class,
            'content_id' => $article->id,
            'recipients_count' => $expectedRecipients,
        ]);

        $article->update([
            'titre' => 'Article newsletter test mis a jour',
        ]);

        Mail::assertSent(PublicationNewsletterMail::class, $expectedRecipients);
        $this->assertEquals(1, Newsletter::query()->where('content_type', BlogArticle::class)->count());
    }

    public function test_publishing_a_verse_and_spiritual_publication_sends_newsletters_once(): void
    {
        Mail::fake();

        NewsletterSubscriber::query()->create([
            'prenom' => 'Abonne',
            'email' => 'abonne@example.com',
            'langue' => 'fr',
            'source' => 'website',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        $expectedRecipients = NewsletterSubscriber::query()
            ->where('is_active', true)
            ->pluck('email')
            ->map(fn (string $email) => strtolower($email))
            ->unique()
            ->count();

        $verse = Verset::query()->create([
            'reference' => 'Romains 8:28',
            'reference_fr' => 'Romains 8:28',
            'reference_en' => 'Romans 8:28',
            'texte' => 'Toutes choses concourent au bien de ceux qui aiment Dieu.',
            'texte_fr' => 'Toutes choses concourent au bien de ceux qui aiment Dieu.',
            'texte_en' => 'All things work together for good to those who love God.',
            'version' => 'LSG',
            'version_fr' => 'LSG',
            'version_en' => 'KJV',
            'actif' => false,
            'afficher_accueil' => false,
            'ordre' => 1,
        ]);

        $verse->update([
            'actif' => true,
        ]);

        $publication = SpiritualPublication::query()->create([
            'type' => 'pensee',
            'titre' => 'Pensee newsletter test',
            'titre_fr' => 'Pensee newsletter test',
            'titre_en' => 'Newsletter thought test',
            'slug' => 'pensee-newsletter-test',
            'extrait' => 'Extrait pensee newsletter',
            'extrait_fr' => 'Extrait pensee newsletter',
            'extrait_en' => 'Newsletter thought excerpt',
            'contenu' => 'Contenu pensee newsletter',
            'contenu_fr' => 'Contenu pensee newsletter',
            'contenu_en' => 'Newsletter thought content',
            'actif' => false,
            'ordre' => 1,
        ]);

        $publication->update([
            'actif' => true,
        ]);

        Mail::assertSent(PublicationNewsletterMail::class, $expectedRecipients * 2);
        $this->assertDatabaseHas('newsletters', [
            'content_type' => Verset::class,
            'content_id' => $verse->id,
            'recipients_count' => $expectedRecipients,
        ]);
        $this->assertDatabaseHas('newsletters', [
            'content_type' => SpiritualPublication::class,
            'content_id' => $publication->id,
            'recipients_count' => $expectedRecipients,
        ]);

        $verse->update(['texte' => 'Texte mis a jour.']);
        $publication->update(['titre' => 'Pensee newsletter test mise a jour']);

        Mail::assertSent(PublicationNewsletterMail::class, $expectedRecipients * 2);
        $this->assertEquals(1, Newsletter::query()->where('content_type', Verset::class)->count());
        $this->assertEquals(1, Newsletter::query()->where('content_type', SpiritualPublication::class)->count());
    }

    public function test_scheduled_publications_also_send_newsletters_when_they_go_live(): void
    {
        Mail::fake();

        NewsletterSubscriber::query()->create([
            'prenom' => 'Abonnee',
            'email' => 'abonnee@example.com',
            'langue' => 'fr',
            'source' => 'website',
            'is_active' => true,
            'subscribed_at' => now(),
        ]);

        $publishAt = now()->subMinute();

        $verse = Verset::query()->create([
            'reference' => 'Jean 10:10',
            'reference_fr' => 'Jean 10:10',
            'reference_en' => 'John 10:10',
            'texte' => 'Je suis venu afin que les brebis aient la vie.',
            'texte_fr' => 'Je suis venu afin que les brebis aient la vie.',
            'texte_en' => 'I came that they may have life.',
            'version' => 'LSG',
            'version_fr' => 'LSG',
            'version_en' => 'KJV',
            'actif' => false,
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'ordre' => 1,
        ]);

        $publication = SpiritualPublication::query()->create([
            'type' => 'exhortation',
            'titre' => 'Exhortation programmee',
            'titre_fr' => 'Exhortation programmee',
            'titre_en' => 'Scheduled exhortation',
            'slug' => 'exhortation-programmee-newsletter',
            'contenu' => 'Contenu exhortation programmee',
            'contenu_fr' => 'Contenu exhortation programmee',
            'contenu_en' => 'Scheduled exhortation content',
            'actif' => false,
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'ordre' => 1,
        ]);

        Mail::assertNothingSent();

        Artisan::call('content:publish-scheduled');

        $expectedRecipients = NewsletterSubscriber::query()
            ->where('is_active', true)
            ->pluck('email')
            ->map(fn (string $email) => strtolower($email))
            ->unique()
            ->count();

        Mail::assertSent(PublicationNewsletterMail::class, $expectedRecipients * 2);

        $verse->refresh();
        $publication->refresh();

        $this->assertTrue($verse->actif);
        $this->assertTrue($publication->actif);
        $this->assertDatabaseHas('newsletters', [
            'content_type' => Verset::class,
            'content_id' => $verse->id,
        ]);
        $this->assertDatabaseHas('newsletters', [
            'content_type' => SpiritualPublication::class,
            'content_id' => $publication->id,
        ]);
    }
}
