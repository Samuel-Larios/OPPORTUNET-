<?php

namespace Tests\Feature;

use App\Livewire\Panel\ApplicationsManager;
use App\Livewire\Panel\CompanyApplicationsManager;
use App\Livewire\Panel\UserApplicationsManager;
use App\Mail\OfferApplicationProcessedMail;
use App\Mail\OfferApplicationProposedToCompanyMail;
use App\Mail\OfferCandidateValidatedByCompanyMail;
use App\Models\CandidatureOffre;
use App\Models\Opportunite;
use App\Models\Role;
use App\Models\User;
use App\Notifications\PlatformDatabaseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class OfferApplicationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_when_starting_an_application(): void
    {
        $opportunity = $this->createOpportunity();

        $this->get(route('offers.apply.entry', $opportunity->slug))
            ->assertRedirect(route('login'));
    }

    public function test_simple_user_registration_page_is_available(): void
    {
        $this->get(route('register.user'))
            ->assertOk()
            ->assertSeeText('Opportunet Mondiale');
    }

    public function test_authenticated_user_can_submit_an_offer_application_with_documents(): void
    {
        Storage::fake('local');

        $role = $this->firstOrCreateRole('user', 'Utilisateur');
        $user = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Samuel',
            'nom' => 'Larios',
            'actif' => true,
        ]);
        $opportunity = $this->createOpportunity();

        $response = $this->actingAs($user)->post(route('offers.apply.store', $opportunity->slug), [
            'telephone' => '22900000000',
            'whatsapp' => '22900000000',
            'pays' => 'Benin',
            'message' => 'Je suis motive et disponible rapidement.',
            'lettre_motivation' => UploadedFile::fake()->create('lettre.pdf', 400, 'application/pdf'),
            'diplomes' => [
                UploadedFile::fake()->create('diplome-1.pdf', 300, 'application/pdf'),
            ],
            'attestations' => [
                UploadedFile::fake()->create('attestation-1.pdf', 300, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect(route('offers.show', $opportunity->slug) . '#application-form');

        $application = CandidatureOffre::query()->where('user_id', $user->id)->where('opportunite_id', $opportunity->id)->first();

        $this->assertNotNull($application);
        $this->assertDatabaseHas('candidatures_offres', [
            'user_id' => $user->id,
            'opportunite_id' => $opportunity->id,
            'statut' => 'en_attente',
        ]);

        Storage::disk('local')->assertExists($application->lettre_motivation);
        Storage::disk('local')->assertExists($application->diplome_fichiers[0]);
        Storage::disk('local')->assertExists($application->attestation_fichiers[0]);
    }

    public function test_oversized_offer_application_redirects_back_to_form_with_upload_error(): void
    {
        $role = $this->firstOrCreateRole('user', 'Utilisateur');
        $user = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Samuel',
            'nom' => 'Larios',
            'actif' => true,
        ]);
        $opportunity = $this->createOpportunity();

        $response = $this->actingAs($user)->call(
            'POST',
            route('offers.apply.store', $opportunity->slug),
            [],
            [],
            [],
            [
                'CONTENT_LENGTH' => '73400320',
                'CONTENT_TYPE' => 'multipart/form-data',
            ]
        );

        $response->assertRedirect(route('offers.show', $opportunity->slug) . '?upload_error=post_too_large#application-form');
    }

    public function test_non_simple_user_is_redirected_to_own_dashboard_from_application_entry(): void
    {
        $editorRole = $this->firstOrCreateRole('editeur', 'Editeur');
        $editor = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Edith',
            'nom' => 'Editor',
            'actif' => true,
        ]);
        $opportunity = $this->createOpportunity();

        $this->actingAs($editor)
            ->get(route('offers.apply.entry', $opportunity->slug))
            ->assertRedirect(route('panel.editor.offers'));
    }

    public function test_admin_can_process_application_and_send_email(): void
    {
        Mail::fake();

        $adminRole = $this->firstOrCreateRole('super_admin', 'Super Administrateur');
        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'prenom' => 'Admin',
            'nom' => 'Principal',
            'actif' => true,
        ]);

        $candidate = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Grace',
            'nom' => 'A.',
            'actif' => true,
        ]);

        $opportunity = $this->createOpportunity();

        $application = CandidatureOffre::query()->create([
            'user_id' => $candidate->id,
            'opportunite_id' => $opportunity->id,
            'prenom' => 'Grace',
            'nom' => 'A.',
            'email' => $candidate->email,
            'lettre_motivation' => 'offer-applications/letters/test.pdf',
            'diplome_fichiers' => ['offer-applications/diplomas/test.pdf'],
            'attestation_fichiers' => ['offer-applications/certificates/test.pdf'],
            'statut' => 'en_attente',
        ]);

        $this->actingAs($admin);

        Livewire::test(ApplicationsManager::class)
            ->call('selectApplication', $application->id)
            ->set('processingStatus', 'retenue')
            ->set('adminNotes', 'Votre profil est retenu pour la prochaine etape.')
            ->call('processApplication');

        $this->assertDatabaseHas('candidatures_offres', [
            'id' => $application->id,
            'statut' => 'retenue',
            'traite_par' => $admin->id,
        ]);

        Mail::assertSent(OfferApplicationProcessedMail::class, function (OfferApplicationProcessedMail $mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });

        $candidateNotification = $candidate->fresh()->unreadNotifications()->latest()->first();

        $this->assertNotNull($candidateNotification);
        $this->assertSame('offer_application', $candidateNotification->data['resource_type']);
        $this->assertSame($application->id, $candidateNotification->data['resource_id']);
        $this->assertSame(
            route('panel.user.applications', ['application' => $application->id]),
            $candidateNotification->data['action_url']
        );
    }

    public function test_candidate_receives_mail_and_in_app_alert_when_application_is_proposed_to_company(): void
    {
        Mail::fake();

        $adminRole = $this->firstOrCreateRole('super_admin', 'Super Administrateur');
        $companyRole = $this->firstOrCreateRole('entreprise', 'Entreprise');
        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'prenom' => 'Admin',
            'nom' => 'Principal',
            'actif' => true,
        ]);

        $company = User::factory()->create([
            'role_id' => $companyRole->id,
            'prenom' => 'Entreprise',
            'nom' => 'Partenaire',
            'actif' => true,
        ]);

        $candidate = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Grace',
            'nom' => 'A.',
            'actif' => true,
        ]);

        $opportunity = $this->createOpportunity([
            'user_id' => $company->id,
            'organisation' => 'Entreprise Partenaire',
        ]);

        $application = CandidatureOffre::query()->create([
            'user_id' => $candidate->id,
            'opportunite_id' => $opportunity->id,
            'prenom' => 'Grace',
            'nom' => 'A.',
            'email' => $candidate->email,
            'lettre_motivation' => 'offer-applications/letters/test.pdf',
            'diplome_fichiers' => ['offer-applications/diplomas/test.pdf'],
            'attestation_fichiers' => ['offer-applications/certificates/test.pdf'],
            'statut' => 'en_revue',
        ]);

        $this->actingAs($admin);

        Livewire::test(ApplicationsManager::class)
            ->call('selectApplication', $application->id)
            ->set('processingStatus', 'proposee_entreprise')
            ->set('adminNotes', 'Votre candidature a ete proposee a une entreprise partenaire.')
            ->call('processApplication');

        $this->assertDatabaseHas('candidatures_offres', [
            'id' => $application->id,
            'statut' => 'proposee_entreprise',
            'traite_par' => $admin->id,
        ]);

        Mail::assertSent(OfferApplicationProposedToCompanyMail::class, function (OfferApplicationProposedToCompanyMail $mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });

        $candidateNotification = $candidate->fresh()->unreadNotifications()->latest()->first();

        $this->assertNotNull($candidateNotification);
        $this->assertSame('offer_application', $candidateNotification->data['resource_type']);
        $this->assertSame($application->id, $candidateNotification->data['resource_id']);
        $this->assertStringContainsString('propos', (string) $candidateNotification->data['title']);
    }

    public function test_candidate_receives_mail_and_in_app_alert_when_company_validates_profile(): void
    {
        Mail::fake();

        $companyRole = $this->firstOrCreateRole('entreprise', 'Entreprise');
        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');

        $company = User::factory()->create([
            'role_id' => $companyRole->id,
            'prenom' => 'Entreprise',
            'nom' => 'Partenaire',
            'actif' => true,
        ]);

        $candidate = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Grace',
            'nom' => 'A.',
            'actif' => true,
        ]);

        $opportunity = $this->createOpportunity([
            'user_id' => $company->id,
            'organisation' => 'Entreprise Partenaire',
        ]);

        $application = CandidatureOffre::query()->create([
            'user_id' => $candidate->id,
            'opportunite_id' => $opportunity->id,
            'prenom' => 'Grace',
            'nom' => 'A.',
            'email' => $candidate->email,
            'lettre_motivation' => 'offer-applications/letters/test.pdf',
            'diplome_fichiers' => ['offer-applications/diplomas/test.pdf'],
            'attestation_fichiers' => ['offer-applications/certificates/test.pdf'],
            'statut' => 'proposee_entreprise',
        ]);

        $this->actingAs($company);

        Livewire::test(CompanyApplicationsManager::class)
            ->call('selectApplication', $application->id)
            ->set('companyNote', 'Nous souhaitons poursuivre avec ce profil.')
            ->call('validateApplication');

        $this->assertDatabaseHas('candidatures_offres', [
            'id' => $application->id,
            'statut' => 'validee_entreprise',
            'validee_par_entreprise' => $company->id,
        ]);

        Mail::assertSent(OfferCandidateValidatedByCompanyMail::class, function (OfferCandidateValidatedByCompanyMail $mail) use ($candidate) {
            return $mail->hasTo($candidate->email);
        });

        $candidateNotification = $candidate->fresh()->unreadNotifications()->latest()->first();

        $this->assertNotNull($candidateNotification);
        $this->assertSame('offer_application', $candidateNotification->data['resource_type']);
        $this->assertSame($application->id, $candidateNotification->data['resource_id']);
    }

    public function test_candidate_can_reply_to_admin_application_request_from_panel(): void
    {
        Storage::fake('local');
        Mail::fake();

        $adminRole = $this->firstOrCreateRole('super_admin', 'Super Administrateur');
        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'prenom' => 'Admin',
            'nom' => 'Principal',
            'actif' => true,
        ]);

        $candidate = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Grace',
            'nom' => 'A.',
            'actif' => true,
        ]);

        $opportunity = $this->createOpportunity();

        $application = CandidatureOffre::query()->create([
            'user_id' => $candidate->id,
            'opportunite_id' => $opportunity->id,
            'prenom' => 'Grace',
            'nom' => 'A.',
            'email' => $candidate->email,
            'telephone' => '22900000000',
            'lettre_motivation' => 'offer-applications/letters/test.pdf',
            'diplome_fichiers' => ['offer-applications/diplomas/test.pdf'],
            'attestation_fichiers' => ['offer-applications/certificates/test.pdf'],
            'message' => 'Je reste disponible pour completer mon dossier.',
            'statut' => 'en_attente',
        ]);

        Storage::disk('local')->put($application->lettre_motivation, 'letter');
        Storage::disk('local')->put($application->diplome_fichiers[0], 'diploma');
        Storage::disk('local')->put($application->attestation_fichiers[0], 'certificate');

        $this->actingAs($admin);

        Livewire::test(ApplicationsManager::class)
            ->call('selectApplication', $application->id)
            ->set('processingStatus', 'informations_complementaires')
            ->set('adminNotes', 'Merci de completer votre dossier.')
            ->set('replyMessage', 'Merci de joindre un justificatif supplementaire.')
            ->set('replyAttachment', UploadedFile::fake()->create('checklist.pdf', 20, 'application/pdf'))
            ->call('processApplication');

        $this->assertDatabaseHas('candidatures_offres', [
            'id' => $application->id,
            'statut' => 'informations_complementaires',
            'traite_par' => $admin->id,
            'notes_admin' => 'Merci de completer votre dossier.',
        ]);

        $adminAttachmentMessage = $application->fresh()->messages()
            ->where('sender_role', 'admin')
            ->where('attachment_name', 'checklist.pdf')
            ->first();

        $this->assertNotNull($adminAttachmentMessage);
        Storage::disk('local')->assertExists($adminAttachmentMessage->attachment_path);

        $this->actingAs($candidate)
            ->get(route('panel.user.applications'))
            ->assertOk()
            ->assertSeeText($opportunity->titre)
            ->assertSeeText('Merci de completer votre dossier.');

        Livewire::test(UserApplicationsManager::class)
            ->call('selectApplication', $application->id)
            ->set('replyMessage', 'Je vous envoie les informations demandees.')
            ->set('replyAttachment', UploadedFile::fake()->create('reponse.pdf', 20, 'application/pdf'))
            ->call('sendReply');

        $this->assertDatabaseHas('candidature_offre_messages', [
            'candidature_offre_id' => $application->id,
            'sender_role' => 'user',
            'message' => 'Je vous envoie les informations demandees.',
            'attachment_name' => 'reponse.pdf',
        ]);

        $this->assertDatabaseHas('candidatures_offres', [
            'id' => $application->id,
            'statut' => 'en_revue',
        ]);

        $adminNotification = $admin->fresh()->unreadNotifications()->latest()->first();

        $this->assertNotNull($adminNotification);
        $this->assertSame('offer_application', $adminNotification->data['resource_type']);
        $this->assertSame($application->id, $adminNotification->data['resource_id']);
        $this->assertSame(
            route('panel.admin.applications', ['application' => $application->id]),
            $adminNotification->data['action_url']
        );

        $this->actingAs($candidate)
            ->get(route('panel.user.applications.download-message', [
                'candidature' => $application->id,
                'message' => $adminAttachmentMessage->id,
            ]))
            ->assertOk();
    }

    public function test_notification_open_route_marks_notification_as_read_and_redirects(): void
    {
        $role = $this->firstOrCreateRole('user', 'Utilisateur');

        $user = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Samuel',
            'nom' => 'Larios',
            'actif' => true,
        ]);

        $user->notify(new PlatformDatabaseNotification([
            'title' => 'Notification de test',
            'message' => 'Ouverture directe',
            'action_url' => route('panel.user.applications', ['application' => 25]),
            'action_label' => __('admin.notifications.open'),
            'category' => 'application',
            'resource_type' => 'offer_application',
            'resource_id' => 25,
        ]));

        $notification = $user->fresh()->unreadNotifications()->first();

        $this->assertNotNull($notification);

        $this->actingAs($user)
            ->get(route('panel.notifications.open', $notification))
            ->assertRedirect(route('panel.user.applications', ['application' => 25]));

        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_notification_open_route_can_rebuild_detail_query_from_resource_type(): void
    {
        $role = $this->firstOrCreateRole('admin', 'Administrateur');

        $user = User::factory()->create([
            'role_id' => $role->id,
            'prenom' => 'Alice',
            'nom' => 'Admin',
            'actif' => true,
        ]);

        $user->notify(new PlatformDatabaseNotification([
            'title' => 'Notification contact',
            'message' => 'Ouverture du détail',
            'action_url' => route('panel.admin.contacts'),
            'action_label' => __('admin.notifications.open'),
            'category' => 'contact',
            'resource_type' => 'contact',
            'resource_id' => 42,
        ]));

        $notification = $user->fresh()->unreadNotifications()->first();

        $this->assertNotNull($notification);

        $this->actingAs($user)
            ->get(route('panel.notifications.open', $notification))
            ->assertRedirect(route('panel.admin.contacts', ['contact' => 42]));
    }

    private function createOpportunity(array $attributes = []): Opportunite
    {
        return Opportunite::query()->create(array_merge([
            'titre' => 'Coordinateur Projet',
            'slug' => 'coordinateur-projet-' . fake()->unique()->slug(),
            'type' => 'emploi',
            'description' => 'Piloter des activites de terrain et coordonner les parties prenantes.',
            'statut' => 'publie',
            'date_publication' => now(),
            'teletravail' => false,
            'urgent' => false,
            'en_vedette' => false,
            'vues' => 0,
        ], $attributes));
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
