<?php

namespace Tests\Feature;

use App\Livewire\OffersIndex;
use App\Models\Opportunite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OffersIndexLivewireTest extends TestCase
{
    use RefreshDatabase;

    public function test_offers_page_renders_the_livewire_component(): void
    {
        $response = $this->get(route('offers.index'));

        $response->assertOk();
        $response->assertSeeLivewire(OffersIndex::class);
    }

    public function test_livewire_search_filters_visible_offers(): void
    {
        $this->createOpportunity([
            'titre' => 'Designer Produit',
            'slug' => 'designer-produit',
            'organisation' => 'Studio Horizon',
            'description' => 'Concevoir des experiences produit.',
        ]);

        $this->createOpportunity([
            'titre' => 'Data Analyst',
            'slug' => 'data-analyst',
            'organisation' => 'Insight Africa',
            'description' => 'Analyser les donnees terrain.',
        ]);

        Livewire::test(OffersIndex::class)
            ->set('search', 'Designer')
            ->assertSee('Designer Produit')
            ->assertDontSee('Data Analyst')
            ->assertViewHas('filteredCount', 1);
    }

    public function test_livewire_filters_by_type_country_and_urgent_status(): void
    {
        $this->createOpportunity([
            'titre' => 'Bourse Innovation',
            'slug' => 'bourse-innovation',
            'type' => 'bourse',
            'pays' => 'Benin',
            'urgent' => true,
        ]);

        $this->createOpportunity([
            'titre' => 'Stage Communication',
            'slug' => 'stage-communication',
            'type' => 'stage',
            'pays' => 'Togo',
            'urgent' => false,
        ]);

        Livewire::test(OffersIndex::class)
            ->set('type', 'bourse')
            ->set('pays', 'Benin')
            ->set('urgent', true)
            ->assertSee('Bourse Innovation')
            ->assertDontSee('Stage Communication')
            ->assertViewHas('filteredCount', 1);
    }

    public function test_published_offer_detail_page_renders_and_increments_views(): void
    {
        $opportunity = $this->createOpportunity([
            'titre' => 'Charge de programme',
            'slug' => 'charge-de-programme',
            'description' => 'Une offre detaillee pour suivre des projets a impact.',
            'profil_recherche' => 'Experience en coordination et excellentes competences relationnelles.',
            'avantages' => 'Equipe engagee, cadre stimulant et autonomie.',
            'vues' => 2,
        ]);

        $response = $this->get(route('offers.show', $opportunity->slug));

        $response->assertOk();
        $response->assertSeeText('Charge de programme');
        $response->assertSeeText('Une offre detaillee pour suivre des projets a impact.');

        $this->assertDatabaseHas('opportunites', [
            'id' => $opportunity->id,
            'vues' => 3,
        ]);
    }

    public function test_unpublished_offer_detail_page_returns_404(): void
    {
        $opportunity = $this->createOpportunity([
            'slug' => 'offre-cachee',
            'statut' => 'brouillon',
        ]);

        $this->get(route('offers.show', $opportunity->slug))
            ->assertNotFound();
    }

    private function createOpportunity(array $attributes = []): Opportunite
    {
        return Opportunite::query()->create(array_merge([
            'titre' => 'Offre de test',
            'slug' => 'offre-de-test-' . fake()->unique()->slug(),
            'type' => 'emploi',
            'description' => 'Description de test',
            'statut' => 'publie',
            'date_publication' => now(),
            'teletravail' => false,
            'urgent' => false,
            'en_vedette' => false,
            'vues' => 0,
        ], $attributes));
    }
}
