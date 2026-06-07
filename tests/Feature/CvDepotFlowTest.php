<?php

namespace Tests\Feature;

use App\Livewire\CvDepotForm;
use App\Livewire\Panel\CvDepotsManager;
use App\Livewire\Panel\UserCvDepotsManager;
use App\Models\CvDepot;
use App\Models\CvDepotMessage;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class CvDepotFlowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_user_can_submit_cv_and_exchange_messages_with_admin(): void
    {
        Storage::fake('local');

        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');
        $adminRole = $this->firstOrCreateRole('admin', 'Administrateur');

        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Samuel',
            'nom' => 'Candidat',
            'email' => 'samuel.cv@example.com',
            'actif' => true,
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'prenom' => 'Alice',
            'nom' => 'Admin',
            'actif' => true,
        ]);

        $this->actingAs($user);

        Livewire::test(CvDepotForm::class)
            ->set('prenom', 'Samuel')
            ->set('nom', 'Candidat')
            ->set('email', 'samuel.cv@example.com')
            ->set('telephone', '+22900000000')
            ->set('whatsapp', '+22900000000')
            ->set('pays', 'Benin')
            ->set('ville', 'Cotonou')
            ->set('titrePoste', 'Chef de projet')
            ->set('niveauEtude', 'Master')
            ->set('domaineEtude', 'Gestion de projet')
            ->set('anneesExperience', '5')
            ->set('objectifProfessionnel', 'Trouver une opportunite stable.')
            ->set('secteursInteret', 'ONG, education, innovation')
            ->set('linkedinUrl', 'https://linkedin.com/in/samuel-candidat')
            ->set('message', 'Je souhaite aussi un coaching.')
            ->set('demandeRedactionCv', true)
            ->set('demandeCoaching', true)
            ->set('cvFichier', UploadedFile::fake()->create('cv-samuel.pdf', 400, 'application/pdf'))
            ->call('submit')
            ->assertHasNoErrors();

        $cvDepot = CvDepot::query()->where('email', 'samuel.cv@example.com')->first();

        $this->assertNotNull($cvDepot);
        $this->assertDatabaseHas('cv_depots', [
            'id' => $cvDepot->id,
            'user_id' => $user->id,
            'demande_redaction_cv' => true,
            'demande_coaching' => true,
            'statut' => 'nouveau',
        ]);
        Storage::disk('local')->assertExists($cvDepot->cv_fichier);
        $this->assertDatabaseHas('cv_depot_messages', [
            'cv_depot_id' => $cvDepot->id,
            'sender_role' => 'user',
            'message' => 'Je souhaite aussi un coaching.',
        ]);
        $this->assertSame(
            route('panel.admin.cv-depots', ['cv' => $cvDepot->id]),
            $admin->fresh()->unreadNotifications()->latest()->first()?->data['action_url']
        );

        $this->actingAs($admin);

        Livewire::test(CvDepotsManager::class)
            ->call('selectCvDepot', $cvDepot->id)
            ->set('processingStatus', 'en_traitement')
            ->set('adminNotes', 'Dossier en cours de revue.')
            ->set('replyMessage', 'Merci de completer votre dossier avec une fiche de references.')
            ->set('replyAttachment', UploadedFile::fake()->create('references.docx', 120, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
            ->call('updateCvDepot')
            ->assertHasNoErrors();

        $adminMessage = CvDepotMessage::query()
            ->where('cv_depot_id', $cvDepot->id)
            ->where('sender_role', 'admin')
            ->latest('id')
            ->first();

        $this->assertNotNull($adminMessage);
        $this->assertDatabaseHas('cv_depots', [
            'id' => $cvDepot->id,
            'statut' => 'en_traitement',
            'traite_par' => $admin->id,
        ]);
        Storage::disk('local')->assertExists($adminMessage->attachment_path);

        $this->actingAs($user);

        $this->get(route('panel.user.cv-depots'))->assertOk();

        Livewire::test(UserCvDepotsManager::class)
            ->call('selectCvDepot', $cvDepot->id)
            ->set('replyMessage', 'Bonjour, voici le complement demande.')
            ->set('replyAttachment', UploadedFile::fake()->create('references-completees.pdf', 160, 'application/pdf'))
            ->call('sendReply')
            ->assertHasNoErrors();

        $userReply = CvDepotMessage::query()
            ->where('cv_depot_id', $cvDepot->id)
            ->where('sender_role', 'user')
            ->latest('id')
            ->first();

        $this->assertNotNull($userReply);
        $this->assertEquals(3, CvDepotMessage::query()->where('cv_depot_id', $cvDepot->id)->count());
        Storage::disk('local')->assertExists($userReply->attachment_path);
        $this->assertSame(
            route('panel.admin.cv-depots', ['cv' => $cvDepot->id]),
            $admin->fresh()->unreadNotifications()->latest()->first()?->data['action_url']
        );

        $this->get(route('panel.user.cv-depots.download-message', ['cvDepot' => $cvDepot->id, 'message' => $adminMessage->id]))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('panel.admin.cv-depots.download-cv', $cvDepot->id))
            ->assertOk();
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
