<?php

namespace Tests\Feature;

use App\Models\ParametreSite;
use App\Models\SecurityIncident;
use App\Support\SecuritySettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private const EXPECTED_CSP = "default-src 'self'; base-uri 'self'; connect-src 'self'; font-src 'self' https://fonts.gstatic.com data:; form-action 'self'; frame-ancestors 'self'; img-src 'self' data: https:; object-src 'none'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com";

    public function test_public_pages_send_security_headers(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Content-Security-Policy', self::EXPECTED_CSP)
            ->assertHeader('Content-Language', config('app.locale'))
            ->assertHeader('Vary', 'Accept-Language');
    }

    public function test_login_page_sends_noindex_and_no_store_headers(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet')
            ->assertHeader('Cache-Control', 'max-age=0, no-store, private')
            ->assertHeader('Pragma', 'no-cache')
            ->assertHeader('Expires', '0');
    }

    public function test_legacy_admin_path_is_not_exposed_to_guests(): void
    {
        $this->get('/admin')->assertNotFound();
    }

    public function test_robots_txt_disallows_sensitive_paths(): void
    {
        $this->get(route('seo.robots'))
            ->assertOk()
            ->assertSee('Disallow: /admin', false)
            ->assertSee('Disallow: /espace-administration', false)
            ->assertSee('Disallow: /connexion', false)
            ->assertSee('Disallow: /mon-espace', false)
            ->assertSee('Disallow: /espace-entreprise', false);
    }

    public function test_contact_form_blocks_suspicious_phishing_message(): void
    {
        $response = $this->withFormCaptcha()->from(route('home'))->post(route('contact.quick'), $this->captchaPayload([
            'prenom' => 'Samuel',
            'nom' => 'Test',
            'email' => 'samuel@example.com',
            'sujet' => 'information',
            'message' => 'Cliquez sur https://evil.example/login pour verifier votre compte et envoyer votre mot de passe.',
        ]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('message');

        $this->assertDatabaseCount('contacts', 0);
    }

    public function test_contact_form_requires_captcha_when_enabled(): void
    {
        $response = $this->from(route('home'))->post(route('contact.quick'), [
            'prenom' => 'Samuel',
            'nom' => 'Test',
            'email' => 'samuel@example.com',
            'sujet' => 'information',
            'message' => 'Je souhaite en savoir plus sur vos services.',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('captcha_answer');

        $this->assertDatabaseCount('contacts', 0);
    }

    public function test_newsletter_form_honeypot_blocks_bot_submission(): void
    {
        $response = $this->withFormCaptcha()->from(route('home'))->post(route('newsletter.subscribe'), $this->captchaPayload([
            'prenom' => 'Robot',
            'email' => 'robot@example.com',
            'website' => 'https://spam.example',
        ]));

        $response->assertRedirect(route('home'));
        $response->assertSessionHasErrors('form');

        $this->assertDatabaseCount('newsletter_subscribers', 0);
    }

    public function test_repeated_abuse_auto_blocks_the_ip_address(): void
    {
        ParametreSite::query()->where('cle', 'security_ip_auto_block_threshold')->update(['valeur' => '2', 'valeur_fr' => '2', 'valeur_en' => '2']);
        ParametreSite::query()->where('cle', 'security_ip_auto_block_window_minutes')->update(['valeur' => '60', 'valeur_fr' => '60', 'valeur_en' => '60']);
        SecuritySettings::flush();

        $payload = [
            'prenom' => 'Samuel',
            'nom' => 'Test',
            'email' => 'samuel@example.com',
            'sujet' => 'information',
            'message' => 'Cliquez sur https://evil.example/login pour verifier votre mot de passe maintenant.',
        ];

        $this->withFormCaptcha()->post(route('contact.quick'), $this->captchaPayload($payload));
        $this->withFormCaptcha()->post(route('contact.quick'), $this->captchaPayload($payload));

        $this->get(route('home'))->assertForbidden();

        $this->assertDatabaseCount('security_ip_blocks', 1);
        $this->assertDatabaseHas('security_ip_blocks', [
            'ip_address' => '127.0.0.1',
            'is_manual' => false,
        ]);
        $this->assertGreaterThanOrEqual(2, SecurityIncident::query()->count());
    }

    public function test_geo_policy_can_block_a_country_when_proxy_header_is_present(): void
    {
        ParametreSite::query()->where('cle', 'security_geo_mode')->update(['valeur' => 'allowlist', 'valeur_fr' => 'allowlist', 'valeur_en' => 'allowlist']);
        ParametreSite::query()->where('cle', 'security_geo_countries')->update(['valeur' => 'US', 'valeur_fr' => 'US', 'valeur_en' => 'US']);
        SecuritySettings::flush();

        $this->withHeaders([
            'CF-IPCountry' => 'NG',
        ])->get(route('home'))->assertForbidden();

        $this->assertDatabaseHas('security_incidents', [
            'type' => 'geo_blocked',
            'country_code' => 'NG',
        ]);
    }
}
