<?php

namespace Tests\Feature;

use App\Mail\PublicationNewsletterMail;
use App\Models\BlogArticle;
use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\Opportunite;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NewsletterPublishingTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_subscribe_to_newsletter(): void
    {
        $response = $this->post(route('newsletter.subscribe'), [
            'prenom' => 'Ruth',
            'email' => 'ruth@example.com',
        ]);

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
}
