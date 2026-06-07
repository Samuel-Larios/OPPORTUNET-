<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_public_information_pages_are_available(): void
    {
        foreach ([
            route('site.about'),
            route('site.help'),
            route('site.documentation'),
            route('site.security'),
            route('site.privacy'),
            route('site.terms'),
            route('site.cookies'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_home_page_no_longer_contains_empty_or_hash_only_footer_links(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertDontSee('href="#"', false);
        $response->assertDontSee('href=""', false);
    }
}
