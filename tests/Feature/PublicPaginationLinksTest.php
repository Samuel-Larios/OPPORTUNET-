<?php

namespace Tests\Feature;

use App\Models\BlogArticle;
use App\Models\Formation;
use App\Models\MurDePriere;
use App\Models\Opportunite;
use App\Models\Temoignage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPaginationLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_offers_page_renders_a_working_second_page_link_with_locale(): void
    {
        foreach (range(1, 10) as $index) {
            Opportunite::query()->create([
                'titre' => 'Offre pagination ' . $index,
                'slug' => 'offre-pagination-' . $index,
                'type' => 'emploi',
                'description' => 'Description offre pagination ' . $index,
                'statut' => 'publie',
                'date_publication' => now()->subDays($index),
                'teletravail' => false,
                'urgent' => false,
                'en_vedette' => false,
                'vues' => 0,
            ]);
        }

        $this->get('/offres-opportunites?lang=fr')
            ->assertOk()
            ->assertSee('/offres-opportunites?lang=fr&amp;page=2', false);
    }

    public function test_articles_page_renders_a_working_second_page_link_with_locale(): void
    {
        $author = User::factory()->create();

        foreach (range(1, 10) as $index) {
            BlogArticle::query()->create([
                'user_id' => $author->id,
                'titre' => 'Article pagination ' . $index,
                'slug' => 'article-pagination-' . $index,
                'contenu' => 'Contenu article pagination ' . $index,
                'statut' => 'publie',
                'publie_le' => now()->subDays($index),
                'commentaires_actifs' => true,
                'en_vedette' => false,
                'vues' => 0,
                'partages' => 0,
            ]);
        }

        $this->get('/articles?lang=fr')
            ->assertOk()
            ->assertSee('/articles?lang=fr&amp;page=2', false);
    }

    public function test_community_prayers_page_renders_a_working_second_page_link_with_locale(): void
    {
        foreach (range(1, 13) as $index) {
            MurDePriere::query()->create([
                'prenom' => 'Priere ' . $index,
                'pays' => 'Benin',
                'email' => 'priere' . $index . '@example.test',
                'sujet' => 'Sujet pagination priere ' . $index,
                'type' => 'priere',
                'anonyme' => false,
                'priants' => 0,
                'statut' => 'approuve',
            ]);
        }

        $this->get('/mur-de-priere/communaute?lang=fr')
            ->assertOk()
            ->assertSee('/mur-de-priere/communaute?lang=fr&amp;page=2', false);
    }

    public function test_community_testimonials_page_renders_a_working_second_page_link_with_locale(): void
    {
        foreach (range(1, 10) as $index) {
            Temoignage::query()->create([
                'prenom' => 'Temoin ' . $index,
                'nom' => 'Pagination',
                'email' => 'temoin' . $index . '@example.test',
                'pays' => 'Benin',
                'profession' => 'Consultant',
                'contenu' => 'Temoignage pagination ' . $index,
                'type' => 'general',
                'note' => 5,
                'statut' => 'approuve',
                'en_vedette' => false,
                'ordre' => $index,
            ]);
        }

        $this->get('/temoignages/communaute?lang=fr')
            ->assertOk()
            ->assertSee('/temoignages/communaute?lang=fr&amp;page=2', false);
    }

    public function test_trainings_page_renders_a_working_second_page_link_with_locale(): void
    {
        foreach (range(1, 10) as $index) {
            Formation::query()->create([
                'titre' => 'Formation pagination ' . $index,
                'slug' => 'formation-pagination-' . $index,
                'description_courte' => 'Description formation pagination ' . $index,
                'mode' => 'en_ligne',
                'devise' => 'XOF',
                'fuseau_horaire' => 'Africa/Cotonou',
                'statut' => 'ouverte',
                'inscriptions_ouvertes' => true,
                'en_vedette' => false,
                'vues' => 0,
                'date_debut' => now()->addDays($index)->startOfDay(),
                'date_fin' => now()->addDays($index + 3)->startOfDay(),
            ]);
        }

        $this->get('/formations?lang=fr')
            ->assertOk()
            ->assertSee('/formations?lang=fr&amp;page=2', false);
    }
}
