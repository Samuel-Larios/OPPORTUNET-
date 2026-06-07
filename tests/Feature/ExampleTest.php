<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_the_homepage_is_rendered_in_french_for_a_french_browser(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/');

        $response->assertOk();
        $response->assertSee('lang="fr"', false);
        $response->assertSeeText('Des services concrets pour votre avenir');
        $response->assertSeeText('Une plateforme née au Bénin, avec un cœur africain et une vision mondiale.');
        $response->assertSeeText('Car je connais les projets');
    }

    public function test_the_homepage_is_rendered_in_english_for_an_english_browser(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en-US,en;q=0.9,fr;q=0.8',
        ])->get('/');

        $response->assertOk();
        $response->assertSee('lang="en"', false);
        $response->assertSeeText('Practical services for your future');
        $response->assertSeeText('A platform born in Benin, with an African heart and a global vision.');
        $response->assertSeeText('For I know the plans I have for you');
    }

    public function test_the_homepage_falls_back_to_french_when_browser_language_is_unsupported(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'es-ES,es;q=0.9,de;q=0.8',
        ])->get('/');

        $response->assertOk();
        $response->assertSee('lang="fr"', false);
        $response->assertSeeText('Des services concrets pour votre avenir');
        $response->assertSeeText('Une plateforme née au Bénin, avec un cœur africain et une vision mondiale.');
    }

    public function test_manual_language_switch_overrides_browser_language(): void
    {
        $this->withHeaders([
            'referer' => url('/'),
            'Accept-Language' => 'fr-FR,fr;q=0.9',
        ])->get('/locale/en')->assertRedirect('/');

        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9',
        ])->get('/');

        $response->assertOk();
        $response->assertSee('lang="en"', false);
        $response->assertSeeText('Practical services for your future');
        $response->assertSeeText('A platform born in Benin, with an African heart and a global vision.');
    }

    public function test_quick_contact_form_is_saved(): void
    {
        $response = $this->withFormCaptcha()->post('/contact-rapide', $this->captchaPayload([
            'prenom' => 'Samuel',
            'nom' => 'Larios',
            'email' => 'samuel@example.com',
            'telephone' => '+22900000000',
            'pays' => 'Benin',
            'sujet' => 'service',
            'message' => 'Je souhaite etre aide pour mon CV.',
        ]));

        $response->assertRedirect(route('home') . '#home-contact');

        $this->assertDatabaseHas('contacts', [
            'prenom' => 'Samuel',
            'email' => 'samuel@example.com',
            'sujet' => 'service',
        ]);
    }

    public function test_prayer_wall_form_is_saved_as_pending(): void
    {
        $response = $this->withFormCaptcha()->post('/mur-de-priere', $this->captchaPayload([
            'prenom' => 'Grace',
            'email' => 'grace@example.com',
            'pays' => 'Togo',
            'sujet' => 'Merci de prier pour une orientation claire.',
            'anonyme' => '1',
        ]));

        $response->assertRedirect(route('home') . '#home-prayer');

        $this->assertDatabaseHas('mur_de_prieres', [
            'prenom' => 'Grace',
            'email' => 'grace@example.com',
            'type' => 'priere',
            'statut' => 'en_attente',
            'anonyme' => true,
        ]);
    }

    public function test_offers_page_is_rendered(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/offres-opportunites');

        $response->assertOk();
        $response->assertSeeText('Explorez les offres et opportunités du moment');
        $response->assertSeeText('Assistant Projet et Communication');
        $response->assertSeeText('Les services disponibles pour aller plus loin');
        $response->assertSeeText('Accompagnement Complet');
    }

    public function test_offers_page_filters_by_type(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/offres-opportunites?type=bourse');

        $response->assertOk();
        $response->assertSeeText('Bourse Leadership Afrique Francophone');
        $response->assertDontSeeText('Assistant Projet et Communication');
    }

    public function test_homepage_displays_recent_articles(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/');

        $response->assertOk();
        $response->assertSeeText('Conseils, inspiration et actualités à lire');
        $response->assertSeeText('Opportunet Mondiale ouvre un espace articles');
    }

    public function test_cv_services_page_is_rendered(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/depot-cv-services');

        $response->assertOk();
        $response->assertSeeText('Demandez votre service CV et choisissez le bon accompagnement');
        $response->assertSeeText('Formulaire de service CV');
    }

    public function test_guest_must_log_in_before_submitting_cv(): void
    {
        $response = $this->post('/depot-cv-services', [
            'prenom' => 'Samuel',
            'nom' => 'Larios',
            'email' => 'samuel@example.com',
            'telephone' => '+22900000000',
            'whatsapp' => '+22900000000',
            'pays' => 'Benin',
            'ville' => 'Cotonou',
            'titre_poste' => 'Chef de projet',
            'niveau_etude' => 'Master',
            'annees_experience' => 5,
            'objectif_professionnel' => 'Trouver une opportunite stable.',
            'type_contrat_recherche' => 'cdi',
            'teletravail_souhaite' => '1',
            'message' => 'Je souhaite aussi un coaching.',
            'demande_redaction_cv' => '1',
            'demande_coaching' => '1',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_trainings_page_is_rendered(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/formations');

        $response->assertOk();
        $response->assertSeeText('Découvrez les formations disponibles');
        $response->assertSeeText('Bootcamp Impact Professionnel');
    }

    public function test_training_registration_is_saved(): void
    {
        $formationId = \App\Models\Formation::query()->where('slug', 'bootcamp-impact-professionnel')->value('id');

        $role = \App\Models\Role::query()->firstOrCreate([
            'nom' => 'user',
        ], [
            'libelle' => 'Utilisateur',
            'permissions' => json_encode(['view_public']),
            'actif' => true,
        ]);

        $user = \App\Models\User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Samuel',
            'nom' => 'Larios',
            'email' => 'samuel.training@example.com',
            'actif' => true,
        ]);

        $response = $this->actingAs($user)->post('/formations/inscription', [
            'formation_id' => $formationId,
            'prenom' => 'Samuel',
            'nom' => 'Larios',
            'email' => 'samuel.training@example.com',
            'telephone' => '+22900000000',
            'whatsapp' => '+22900000000',
            'pays' => 'Benin',
            'profession' => 'Consultant',
            'niveau_etude' => 'Master',
            'motivation' => 'Je souhaite renforcer mon impact professionnel.',
        ]);

        $response->assertRedirect(route('trainings.index', ['formation' => $formationId]) . '#training-registration');

        $this->assertDatabaseHas('inscriptions_formations', [
            'formation_id' => $formationId,
            'user_id' => $user->id,
            'email' => 'samuel.training@example.com',
            'prenom' => 'Samuel',
            'statut' => 'en_attente',
        ]);
    }

    public function test_contact_prayer_page_is_rendered(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'fr-FR,fr;q=0.9,en;q=0.8',
        ])->get('/contact-priere');

        $response->assertOk();
        $response->assertSeeText('Parlons ensemble et partagez votre sujet de prière');
        $response->assertSeeText('Envoyez votre message');
        $response->assertSeeText('Déposez votre sujet');
    }

    public function test_contact_prayer_page_renders_form_text_in_english(): void
    {
        $response = $this->withHeaders([
            'Accept-Language' => 'en-US,en;q=0.9,fr;q=0.8',
        ])->get('/contact-priere');

        $response->assertOk();
        $response->assertSee('lang="en"', false);
        $response->assertSeeText('Let us connect and share your prayer request');
        $response->assertSeeText('Share your request');
        $response->assertSeeText('Approved encouragement');
        $response->assertSeeText('Take heart. God does not forget any effort sown in faith. Keep moving forward, even in small steps.');
        $response->assertDontSeeText('Courage, Dieu n oublie aucun effort seme avec foi. Continue d avancer, meme a petits pas.');
    }

    public function test_contact_form_can_redirect_back_to_contact_prayer_page(): void
    {
        $response = $this->withFormCaptcha()->post('/contact-rapide', $this->captchaPayload([
            'redirect_to' => '/contact-priere#contact-form',
            'prenom' => 'Samuel',
            'nom' => 'Larios',
            'email' => 'samuel.contact@example.com',
            'telephone' => '+22900000000',
            'pays' => 'Benin',
            'sujet' => 'information',
            'message' => 'Je souhaite etre oriente vers vos services.',
        ]));

        $response->assertRedirect('/contact-priere#contact-form');
    }

    public function test_prayer_form_can_redirect_back_to_contact_prayer_page(): void
    {
        $response = $this->withFormCaptcha()->post('/mur-de-priere', $this->captchaPayload([
            'redirect_to' => '/contact-priere#prayer-form',
            'prenom' => 'Grace',
            'email' => 'grace.contact@example.com',
            'pays' => 'Togo',
            'sujet' => 'Merci de prier pour ma famille et ma direction.',
            'anonyme' => '1',
        ]));

        $response->assertRedirect('/contact-priere#prayer-form');
    }
}
