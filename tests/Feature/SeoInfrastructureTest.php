<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_home_page_exposes_core_seo_tags(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get(route('home'));

        $response->assertOk();
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<link rel="canonical" href="http://localhost?lang=fr"', false);
        $response->assertSee('hreflang="en"', false);
        $response->assertSee('hreflang="fr-BJ"', false);
        $response->assertSee('application/ld+json', false);
    }

    public function test_language_query_parameter_switches_the_public_locale_variant(): void
    {
        $response = $this->get(route('offers.index', ['lang' => 'en']));

        $response->assertOk();
        $response->assertSee('<html lang="en">', false);
        $response->assertSee('<link rel="canonical" href="http://localhost/offres-opportunites?lang=en"', false);
    }

    public function test_sitemap_lists_major_public_pages_and_published_content(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee('<loc>http://localhost?lang=fr</loc>', false);
        $response->assertSee('<loc>http://localhost/offres-opportunites?lang=en</loc>', false);
        $response->assertSee('<loc>http://localhost/articles/5-erreurs-qui-bloquent-votre-candidature?lang=fr</loc>', false);
        $response->assertSee('<loc>http://localhost/offres-opportunites/assistant-projet-benin?lang=fr</loc>', false);
        $response->assertSee('xhtml:link rel="alternate" hreflang="fr-BJ"', false);
    }

    public function test_robots_endpoint_mentions_the_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertSee('Sitemap: http://localhost/sitemap.xml');
    }
}
