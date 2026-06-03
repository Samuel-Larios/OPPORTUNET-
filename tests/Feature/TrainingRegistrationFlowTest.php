<?php

namespace Tests\Feature;

use App\Livewire\Panel\TrainingRegistrationsManager;
use App\Livewire\Panel\UserTrainingRegistrationsManager;
use App\Mail\TrainingRegistrationReceivedMail;
use App\Models\FormationRegistrationMessage;
use App\Models\InscriptionFormation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TrainingRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_user_can_register_for_training_and_exchange_messages_with_admin(): void
    {
        Storage::fake('public');
        Mail::fake();

        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');
        $adminRole = $this->firstOrCreateRole('admin', 'Administrateur');

        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Samuel',
            'nom' => 'Apprenant',
            'email' => 'samuel.formation@example.com',
            'telephone' => '+22900000000',
            'pays' => 'Benin',
            'profession' => 'Consultant',
            'niveau_etude' => 'Master',
            'whatsapp' => '+22900000000',
            'actif' => true,
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'prenom' => 'Aline',
            'nom' => 'Admin',
            'email' => 'admin-formation@example.com',
            'actif' => true,
        ]);

        $formation = \App\Models\Formation::query()
            ->where('slug', 'bootcamp-impact-professionnel')
            ->firstOrFail();

        $formation->update([
            'statut' => 'ouverte',
            'inscriptions_ouvertes' => true,
            'date_debut' => now()->addDays(10)->startOfDay(),
            'date_fin' => now()->addDays(14)->startOfDay(),
            'places_max' => 10,
            'places_restantes' => 10,
        ]);

        $response = $this->actingAs($user)->post(route('trainings.register'), [
            'formation_id' => $formation->id,
            'prenom' => 'Samuel',
            'nom' => 'Apprenant',
            'email' => 'samuel.formation@example.com',
            'telephone' => '+22900000000',
            'whatsapp' => '+22900000000',
            'pays' => 'Benin',
            'profession' => 'Consultant',
            'niveau_etude' => 'Master',
            'motivation' => 'Je veux structurer ma progression professionnelle.',
        ]);

        $response->assertRedirect(route('trainings.index', ['formation' => $formation->id]) . '#training-registration');

        $registration = InscriptionFormation::query()
            ->where('formation_id', $formation->id)
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($registration);
        $this->assertDatabaseHas('inscriptions_formations', [
            'id' => $registration->id,
            'statut' => 'en_attente',
            'statut_paiement' => 'en_attente',
        ]);
        $this->assertDatabaseHas('inscription_formation_messages', [
            'inscription_formation_id' => $registration->id,
            'sender_role' => 'user',
            'message' => 'Je veux structurer ma progression professionnelle.',
        ]);

        Mail::assertSent(TrainingRegistrationReceivedMail::class);

        $this->actingAs($admin);

        Livewire::test(TrainingRegistrationsManager::class)
            ->call('selectRegistration', $registration->id)
            ->set('processingStatus', 'confirme')
            ->set('paymentStatus', 'paye')
            ->set('paymentMode', 'mobile_money')
            ->set('paymentReference', 'MOMO-REF-123')
            ->set('amountPaid', '15000')
            ->set('adminNotes', 'Paiement recu et inscription confirmee.')
            ->set('replyMessage', 'Votre place est confirmee. Merci de telecharger la convocation.')
            ->set('replyAttachment', UploadedFile::fake()->create('convocation.pdf', 160, 'application/pdf'))
            ->call('updateRegistration')
            ->assertHasNoErrors();

        $registration->refresh();

        $adminMessage = FormationRegistrationMessage::query()
            ->where('inscription_formation_id', $registration->id)
            ->where('sender_role', 'admin')
            ->latest('id')
            ->first();

        $this->assertNotNull($adminMessage);
        $this->assertDatabaseHas('inscriptions_formations', [
            'id' => $registration->id,
            'statut' => 'confirme',
            'statut_paiement' => 'paye',
            'traite_par' => $admin->id,
            'reference_paiement' => 'MOMO-REF-123',
        ]);
        $this->assertEquals(9, $formation->fresh()->places_restantes);
        Storage::disk('public')->assertExists($adminMessage->attachment_path);

        $this->actingAs($user);

        $this->get(route('panel.user.trainings'))->assertOk();

        Livewire::test(UserTrainingRegistrationsManager::class)
            ->call('selectRegistration', $registration->id)
            ->set('replyMessage', 'Merci, je confirme la reception du document.')
            ->set('replyAttachment', UploadedFile::fake()->create('justificatif.docx', 120, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
            ->call('sendReply')
            ->assertHasNoErrors();

        $userReply = FormationRegistrationMessage::query()
            ->where('inscription_formation_id', $registration->id)
            ->where('sender_role', 'user')
            ->latest('id')
            ->first();

        $this->assertNotNull($userReply);
        $this->assertEquals(3, FormationRegistrationMessage::query()->where('inscription_formation_id', $registration->id)->count());
        Storage::disk('public')->assertExists($userReply->attachment_path);

        $this->get(route('panel.user.trainings.download-message', ['registration' => $registration->id, 'message' => $adminMessage->id]))
            ->assertOk();
    }

    public function test_existing_registration_with_same_email_is_detected_and_linked_to_user(): void
    {
        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');

        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Samuel',
            'nom' => 'Apprenant',
            'email' => 'samuel.formation@example.com',
            'actif' => true,
        ]);

        $formation = \App\Models\Formation::query()
            ->where('slug', 'bootcamp-impact-professionnel')
            ->firstOrFail();

        $formation->update([
            'statut' => 'ouverte',
            'inscriptions_ouvertes' => true,
            'date_debut' => now()->addDays(10)->startOfDay(),
            'date_fin' => now()->addDays(14)->startOfDay(),
            'places_max' => 10,
            'places_restantes' => 10,
        ]);

        $registration = InscriptionFormation::query()->create([
            'formation_id' => $formation->id,
            'user_id' => null,
            'prenom' => 'Samuel',
            'nom' => 'Apprenant',
            'email' => $user->email,
            'telephone' => '+22900000000',
            'whatsapp' => '+22900000000',
            'pays' => 'Benin',
            'profession' => 'Consultant',
            'niveau_etude' => 'Master',
            'motivation' => 'Inscription deja importee.',
            'mode_paiement' => 'en_attente',
            'statut_paiement' => 'en_attente',
            'statut' => 'en_attente',
            'certificat_delivre' => false,
        ]);

        $response = $this->actingAs($user)->post(route('trainings.register'), [
            'formation_id' => $formation->id,
            'prenom' => 'Samuel',
            'nom' => 'Apprenant',
            'email' => $user->email,
            'telephone' => '+22900000000',
            'whatsapp' => '+22900000000',
            'pays' => 'Benin',
            'profession' => 'Consultant',
            'niveau_etude' => 'Master',
            'motivation' => 'Je veux confirmer mon inscription.',
        ]);

        $response
            ->assertRedirect(route('trainings.index', ['formation' => $formation->id]) . '#training-registration')
            ->assertSessionHasErrors('formation_id');

        $this->assertDatabaseCount('inscriptions_formations', 1);
        $this->assertDatabaseHas('inscriptions_formations', [
            'id' => $registration->id,
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        $this->actingAs($user)->get(route('panel.user.trainings'))
            ->assertOk()
            ->assertSeeText($formation->titre);
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
