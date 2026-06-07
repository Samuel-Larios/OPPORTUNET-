<?php

namespace Tests\Feature;

use App\Models\BlogArticle;
use App\Models\BlogCommentaire;
use App\Models\CandidatureOffre;
use App\Models\Contact;
use App\Models\CvDepot;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\MurDePriere;
use App\Models\Opportunite;
use App\Models\Role;
use App\Models\Temoignage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanelAccessTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_user_registration_redirects_to_email_verification_notice(): void
    {
        Role::query()->firstOrCreate([
            'nom' => 'user',
        ], [
            'libelle' => 'Utilisateur',
            'permissions' => json_encode(['view_public']),
            'actif' => true,
        ]);

        $response = $this->withFormCaptcha()->post('/inscription', $this->captchaPayload([
            'prenom' => 'Marie',
            'nom' => 'K.',
            'email' => 'marie@example.com',
            'telephone' => '22900000000',
            'pays' => 'Benin',
            'password' => 'Password123',
            'password_confirmation' => 'Password123',
        ]));

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();
    }

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $role = Role::query()->firstOrCreate([
            'nom' => 'super_admin',
        ], [
            'libelle' => 'Super Administrateur',
            'permissions' => json_encode(['*']),
            'actif' => true,
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Admin',
            'nom' => 'Principal',
            'actif' => true,
        ]);

        $this->actingAs($user)
            ->get(route('panel.admin.dashboard'))
            ->assertOk();
    }

    public function test_authenticated_admin_can_still_use_legacy_admin_entrypoint(): void
    {
        $role = Role::query()->firstOrCreate([
            'nom' => 'super_admin',
        ], [
            'libelle' => 'Super Administrateur',
            'permissions' => json_encode(['*']),
            'actif' => true,
        ]);

        $user = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Admin',
            'nom' => 'Principal',
            'actif' => true,
        ]);

        $this->actingAs($user)
            ->get('/admin')
            ->assertRedirect(route('panel.admin.dashboard'));
    }

    public function test_editor_can_manage_offers_but_not_users(): void
    {
        $editorRole = Role::query()->firstOrCreate([
            'nom' => 'editeur',
        ], [
            'libelle' => 'Editeur',
            'permissions' => json_encode(['manage_offers']),
            'actif' => true,
        ]);

        $editor = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Edith',
            'nom' => 'Editor',
            'actif' => true,
        ]);

        $this->actingAs($editor)
            ->get(route('panel.editor.offers'))
            ->assertOk();

        $this->actingAs($editor)
            ->get(route('panel.editor.articles'))
            ->assertOk();

        $this->actingAs($editor)
            ->get(route('panel.editor.services'))
            ->assertOk();

        $this->actingAs($editor)
            ->get(route('panel.editor.trainings'))
            ->assertOk();

        $this->actingAs($editor)
            ->get(route('panel.editor.categories'))
            ->assertOk();

        $this->actingAs($editor)
            ->get(route('panel.admin.cv-depots'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->get(route('panel.admin.training-registrations'))
            ->assertForbidden();

        $this->actingAs($editor)
            ->get(route('panel.admin.users'))
            ->assertForbidden();
    }

    public function test_admin_menu_displays_pending_badges_for_follow_up_items(): void
    {
        $role = $this->firstOrCreateRole('super_admin', 'Super Administrateur');

        $admin = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Admin',
            'nom' => 'Principal',
            'actif' => true,
        ]);

        $candidate = User::factory()->create([
            'prenom' => 'Candidat',
            'nom' => 'Test',
            'actif' => true,
        ]);

        $article = BlogArticle::query()->firstOrCreate([
            'slug' => 'article-admin-badge',
        ], [
            'user_id' => $admin->id,
            'titre' => 'Article badge admin',
            'contenu' => 'Contenu de test.',
            'statut' => 'publie',
            'publie_le' => now(),
        ]);
        $formation = Formation::query()->firstOrCreate([
            'slug' => 'formation-admin-badge',
        ], [
            'titre' => 'Formation badge admin',
            'description_courte' => 'Formation de test.',
            'description_longue' => 'Formation de test.',
            'statut' => 'ouverte',
            'date_debut' => now()->addWeek(),
        ]);

        $offer = Opportunite::query()->create([
            'titre' => 'Charge de mission',
            'slug' => 'charge-de-mission-en-attente',
            'organisation' => 'Opportunet Mondiale',
            'type' => 'emploi',
            'contrat' => 'cdd',
            'description' => 'Offre en attente de validation.',
            'statut' => 'en_attente_validation',
        ]);

        CandidatureOffre::query()->create([
            'user_id' => $candidate->id,
            'opportunite_id' => $offer->id,
            'prenom' => 'Candidat',
            'nom' => 'Test',
            'email' => 'candidat@example.com',
            'lettre_motivation' => 'storage/letters/motivation.pdf',
            'diplome_fichiers' => ['storage/diplomas/diplome.pdf'],
            'attestation_fichiers' => ['storage/certificates/attestation.pdf'],
            'statut' => 'en_attente',
        ]);

        Contact::query()->create([
            'prenom' => 'Miriam',
            'nom' => 'Contact',
            'email' => 'miriam@example.com',
            'sujet' => 'information',
            'message' => 'Bonjour, j ai besoin d informations.',
            'statut' => 'non_lu',
        ]);

        Contact::query()->create([
            'prenom' => 'Joel',
            'nom' => 'Contact',
            'email' => 'joel@example.com',
            'sujet' => 'service',
            'message' => 'Je souhaite un accompagnement.',
            'statut' => 'non_lu',
        ]);

        CvDepot::query()->create([
            'prenom' => 'Sarah',
            'nom' => 'CV',
            'email' => 'sarah@example.com',
            'statut' => 'nouveau',
        ]);

        InscriptionFormation::query()->create([
            'formation_id' => $formation->id,
            'prenom' => 'David',
            'nom' => 'Formation',
            'email' => 'david@example.com',
            'statut' => 'en_attente',
            'mode_paiement' => 'en_attente',
            'statut_paiement' => 'en_attente',
        ]);

        BlogCommentaire::query()->create([
            'article_id' => $article->id,
            'auteur_nom' => 'Lecteur',
            'auteur_email' => 'lecteur@example.com',
            'contenu' => 'Commentaire en attente.',
            'statut' => 'en_attente',
        ]);

        Temoignage::query()->create([
            'prenom' => 'Claire',
            'nom' => 'Temoin',
            'contenu' => 'Temoignage en attente.',
            'type' => 'general',
            'statut' => 'en_attente',
        ]);

        MurDePriere::query()->create([
            'prenom' => 'Paul',
            'sujet' => 'Sujet de priere en attente.',
            'type' => 'priere',
            'statut' => 'en_attente',
        ]);

        $response = $this->actingAs($admin)->get(route('panel.admin.users'));

        $response->assertOk();

        $this->assertMenuBadge($response->getContent(), route('panel.editor.offers'), '1');
        $this->assertMenuBadge($response->getContent(), route('panel.admin.applications'), '1');
        $this->assertMenuBadge($response->getContent(), route('panel.admin.contacts'), '2');
        $this->assertMenuBadge($response->getContent(), route('panel.admin.cv-depots'), '1');
        $this->assertMenuBadge($response->getContent(), route('panel.admin.training-registrations'), '1');
        $this->assertMenuBadge($response->getContent(), route('panel.admin.article-comments'), '1');
        $this->assertMenuBadge($response->getContent(), route('panel.admin.testimonials'), '1');
        $this->assertMenuBadge($response->getContent(), route('panel.admin.prayers'), '1');
    }

    public function test_user_menu_displays_application_follow_up_badge(): void
    {
        $role = $this->firstOrCreateRole('user', 'Utilisateur');

        $user = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Grace',
            'nom' => 'User',
            'actif' => true,
        ]);

        $offer = Opportunite::query()->create([
            'titre' => 'Charge de mission',
            'slug' => 'charge-de-mission-user-badge',
            'organisation' => 'Opportunet Mondiale',
            'type' => 'emploi',
            'contrat' => 'cdd',
            'description' => 'Offre pour le suivi utilisateur.',
            'statut' => 'publie',
        ]);

        CandidatureOffre::query()->create([
            'user_id' => $user->id,
            'opportunite_id' => $offer->id,
            'prenom' => 'Grace',
            'nom' => 'User',
            'email' => $user->email,
            'lettre_motivation' => 'storage/letters/motivation.pdf',
            'diplome_fichiers' => ['storage/diplomas/diplome.pdf'],
            'attestation_fichiers' => ['storage/certificates/attestation.pdf'],
            'statut' => 'informations_complementaires',
        ]);

        $response = $this->actingAs($user)->get(route('panel.user.applications'));

        $response->assertOk();
        $this->assertMenuBadge($response->getContent(), route('panel.user.applications'), '1');
    }

    private function assertMenuBadge(string $html, string $route, string $badge): void
    {
        $pattern = '/<a href="' . preg_quote($route, '/') . '" class="[^"]*">.*?<span class="panel-nav-badge">' . preg_quote($badge, '/') . '<\/span>/s';

        $this->assertMatchesRegularExpression($pattern, $html);
    }

    private function firstOrCreateRole(string $name, string $label): Role
    {
        return Role::query()->firstOrCreate([
            'nom' => $name,
        ], [
            'libelle' => $label,
            'permissions' => json_encode(['*']),
            'actif' => true,
        ]);
    }
}
