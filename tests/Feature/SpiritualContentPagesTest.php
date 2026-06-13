<?php

namespace Tests\Feature;

use App\Models\SpiritualPublication;
use App\Models\Verset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpiritualContentPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_shows_three_cards_for_verses_thoughts_exhortations_and_daily_prayers(): void
    {
        foreach (range(1, 4) as $index) {
            Verset::query()->create([
                'reference' => "Reference maison test {$index}",
                'reference_fr' => "Reference maison test {$index}",
                'reference_en' => "Home test reference {$index}",
                'texte' => "Texte maison test {$index}",
                'texte_fr' => "Texte maison test {$index}",
                'texte_en' => "Home test text {$index}",
                'version' => 'LSG',
                'version_fr' => 'LSG',
                'version_en' => 'KJV',
                'actif' => true,
                'afficher_accueil' => $index === 1,
                'ordre' => $index,
            ]);

            SpiritualPublication::query()->create([
                'type' => 'pensee',
                'titre' => "Pensee accueil test {$index}",
                'titre_fr' => "Pensee accueil test {$index}",
                'titre_en' => "Home thought test {$index}",
                'slug' => "pensee-accueil-test-{$index}",
                'extrait' => "Extrait pensee accueil test {$index}",
                'extrait_fr' => "Extrait pensee accueil test {$index}",
                'extrait_en' => "Thought excerpt test {$index}",
                'contenu' => "Contenu pensee accueil test {$index}",
                'contenu_fr' => "Contenu pensee accueil test {$index}",
                'contenu_en' => "Thought content test {$index}",
                'actif' => true,
                'afficher_accueil' => $index === 1,
                'ordre' => $index,
            ]);

            SpiritualPublication::query()->create([
                'type' => 'exhortation',
                'titre' => "Exhortation accueil test {$index}",
                'titre_fr' => "Exhortation accueil test {$index}",
                'titre_en' => "Home exhortation test {$index}",
                'slug' => "exhortation-accueil-test-{$index}",
                'extrait' => "Extrait exhortation accueil test {$index}",
                'extrait_fr' => "Extrait exhortation accueil test {$index}",
                'extrait_en' => "Exhortation excerpt test {$index}",
                'contenu' => "Contenu exhortation accueil test {$index}",
                'contenu_fr' => "Contenu exhortation accueil test {$index}",
                'contenu_en' => "Exhortation content test {$index}",
                'actif' => true,
                'afficher_accueil' => $index === 1,
                'ordre' => $index,
            ]);

            SpiritualPublication::query()->create([
                'type' => 'priere_jour',
                'titre' => "Priere accueil test {$index}",
                'titre_fr' => "Priere accueil test {$index}",
                'titre_en' => "Home prayer test {$index}",
                'slug' => "priere-accueil-test-{$index}",
                'extrait' => "Extrait priere accueil test {$index}",
                'extrait_fr' => "Extrait priere accueil test {$index}",
                'extrait_en' => "Prayer excerpt test {$index}",
                'contenu' => "Contenu priere accueil test {$index}",
                'contenu_fr' => "Contenu priere accueil test {$index}",
                'contenu_en' => "Prayer content test {$index}",
                'actif' => true,
                'afficher_accueil' => $index === 1,
                'ordre' => $index,
            ]);
        }

        $response = $this->get('/?lang=fr');

        $response->assertOk();
        $response->assertSee('Reference maison test 1');
        $response->assertSee('Reference maison test 2');
        $response->assertSee('Reference maison test 3');
        $response->assertDontSee('Reference maison test 4');
        $response->assertSee('Pensee accueil test 1');
        $response->assertSee('Pensee accueil test 2');
        $response->assertSee('Pensee accueil test 3');
        $response->assertDontSee('Pensee accueil test 4');
        $response->assertSee('Exhortation accueil test 1');
        $response->assertSee('Exhortation accueil test 2');
        $response->assertSee('Exhortation accueil test 3');
        $response->assertDontSee('Exhortation accueil test 4');
        $response->assertSee('Priere accueil test 1');
        $response->assertSee('Priere accueil test 2');
        $response->assertSee('Priere accueil test 3');
        $response->assertDontSee('Priere accueil test 4');
    }

    public function test_spiritual_detail_pages_are_available_for_the_three_content_types(): void
    {
        $verse = Verset::query()->create([
            'reference' => 'Psaume 1:1',
            'reference_fr' => 'Psaume 1:1',
            'reference_en' => 'Psalm 1:1',
            'texte' => 'Heureux l homme qui ne marche pas selon le conseil des mechants.',
            'texte_fr' => 'Heureux l homme qui ne marche pas selon le conseil des mechants.',
            'texte_en' => 'Blessed is the man who does not walk in the counsel of the wicked.',
            'version' => 'LSG',
            'version_fr' => 'LSG',
            'version_en' => 'KJV',
            'actif' => true,
            'afficher_accueil' => true,
            'ordre' => 1,
        ]);

        $thought = SpiritualPublication::query()->create([
            'type' => 'pensee',
            'titre' => 'Pensee detail test',
            'titre_fr' => 'Pensee detail test',
            'titre_en' => 'Thought detail test',
            'slug' => 'pensee-detail-test',
            'extrait' => 'Extrait pensee detail test',
            'extrait_fr' => 'Extrait pensee detail test',
            'extrait_en' => 'Thought detail excerpt',
            'contenu' => 'Contenu pensee detail test',
            'contenu_fr' => 'Contenu pensee detail test',
            'contenu_en' => 'Thought detail content',
            'auteur' => 'Auteur test',
            'auteur_fr' => 'Auteur test',
            'auteur_en' => 'Test author',
            'actif' => true,
            'ordre' => 1,
        ]);

        $exhortation = SpiritualPublication::query()->create([
            'type' => 'exhortation',
            'titre' => 'Exhortation detail test',
            'titre_fr' => 'Exhortation detail test',
            'titre_en' => 'Exhortation detail test',
            'slug' => 'exhortation-detail-test',
            'extrait' => 'Extrait exhortation detail test',
            'extrait_fr' => 'Extrait exhortation detail test',
            'extrait_en' => 'Exhortation detail excerpt',
            'contenu' => 'Contenu exhortation detail test',
            'contenu_fr' => 'Contenu exhortation detail test',
            'contenu_en' => 'Exhortation detail content',
            'auteur' => 'Auteur exhortation test',
            'auteur_fr' => 'Auteur exhortation test',
            'auteur_en' => 'Exhortation author test',
            'actif' => true,
            'ordre' => 1,
        ]);

        $this->get('/versets-bibliques/' . $verse->id . '?lang=fr')
            ->assertOk()
            ->assertSee('Psaume 1:1')
            ->assertSee('Heureux l homme qui ne marche pas selon le conseil des mechants.');

        $this->get('/pensees-du-jour/' . $thought->slug . '?lang=fr')
            ->assertOk()
            ->assertSee('Pensee detail test')
            ->assertSee('Contenu pensee detail test');

        $this->get('/exhortations/' . $exhortation->slug . '?lang=fr')
            ->assertOk()
            ->assertSee('Exhortation detail test')
            ->assertSee('Contenu exhortation detail test');
    }
}
