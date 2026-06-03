<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlesPageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_articles_page_is_rendered_with_seeded_articles(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get(route('articles.index'));

        $response->assertOk();
        $response->assertSeeText('Decouvrez nos articles, conseils et actualites');
        $response->assertSeeText('5 erreurs qui bloquent votre candidature');
    }

    public function test_articles_page_can_filter_with_search_query(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get(route('articles.index', ['q' => 'vision']));

        $response->assertOk();
        $response->assertSeeText('Rester fidele a sa vision dans les saisons lentes');
        $response->assertDontSeeText('5 erreurs qui bloquent votre candidature');
    }

    public function test_article_detail_page_is_rendered(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/articles/5-erreurs-qui-bloquent-votre-candidature');

        $response->assertOk();
        $response->assertSeeText('5 erreurs qui bloquent votre candidature');
        $response->assertSeeText('Retour aux articles');
        $response->assertSee('tm-622-screen-01.jpg');
    }
}
