<?php

namespace Tests\Feature;

use App\Livewire\Panel\TestimonialsManager;
use App\Models\Role;
use App\Models\Temoignage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TestimonialFlowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_guest_is_redirected_to_login_when_submitting_a_testimonial(): void
    {
        $response = $this->post(route('testimonials.store'), [
            'prenom' => 'Samuel',
            'nom' => 'Temoin',
            'pays' => 'Benin',
            'profession' => 'Consultant',
            'type' => 'general',
            'note' => '5',
            'contenu' => 'Merci pour cet accompagnement.',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertDatabaseMissing('temoignages', [
            'contenu' => 'Merci pour cet accompagnement.',
        ]);
    }

    public function test_user_can_submit_testimonial_and_admin_can_approve_it(): void
    {
        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');
        $adminRole = $this->firstOrCreateRole('admin', 'Administrateur');

        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Samuel',
            'nom' => 'Temoin',
            'email' => 'samuel.temoignage@example.com',
            'pays' => 'Benin',
            'profession' => 'Consultant',
            'actif' => true,
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'prenom' => 'Alice',
            'nom' => 'Admin',
            'actif' => true,
        ]);

        $response = $this->actingAs($user)->post(route('testimonials.store'), [
            'prenom' => 'Samuel',
            'nom' => 'Temoin',
            'pays' => 'Benin',
            'profession' => 'Consultant',
            'type' => 'service_cv',
            'note' => '5',
            'contenu' => 'Mon CV a ete restructure et cela m a ouvert des portes.',
        ]);

        $response->assertRedirect(route('home') . '#testimonial-form');

        $testimonial = Temoignage::query()
            ->where('user_id', $user->id)
            ->where('email', $user->email)
            ->latest('id')
            ->first();

        $this->assertNotNull($testimonial);
        $this->assertDatabaseHas('temoignages', [
            'id' => $testimonial->id,
            'statut' => 'en_attente',
            'en_vedette' => false,
            'type' => 'service_cv',
        ]);

        $this->actingAs($admin);

        Livewire::test(TestimonialsManager::class)
            ->call('selectTestimonial', $testimonial->id)
            ->set('processingStatus', 'approuve')
            ->set('enVedette', true)
            ->set('ordre', '2')
            ->call('updateTestimonial')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('temoignages', [
            'id' => $testimonial->id,
            'statut' => 'approuve',
            'en_vedette' => true,
            'ordre' => 2,
        ]);

        $this->actingAs($user)
            ->get(route('panel.user.testimonials'))
            ->assertOk()
            ->assertSeeText('Mon CV a ete restructure et cela m a ouvert des portes.');
    }

    public function test_home_page_displays_only_approved_testimonials(): void
    {
        Temoignage::query()->create([
            'prenom' => 'Ruth',
            'nom' => 'Visible',
            'email' => 'ruth.visible@example.com',
            'contenu' => 'Temoignage visible sur la page d accueil.',
            'type' => 'general',
            'statut' => 'approuve',
            'en_vedette' => true,
            'ordre' => 1,
        ]);

        Temoignage::query()->create([
            'prenom' => 'Jean',
            'nom' => 'Cache',
            'email' => 'jean.cache@example.com',
            'contenu' => 'Temoignage qui ne doit pas apparaitre.',
            'type' => 'general',
            'statut' => 'en_attente',
            'en_vedette' => false,
            'ordre' => 0,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Temoignage visible sur la page d accueil.')
            ->assertDontSeeText('Temoignage qui ne doit pas apparaitre.');
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
