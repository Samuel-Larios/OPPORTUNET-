<?php

namespace Tests\Feature;

use App\Livewire\Panel\PrayersManager;
use App\Models\MurDePriere;
use App\Models\PriereSoutien;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PrayerWallFlowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_authenticated_user_submission_is_linked_and_can_be_moderated(): void
    {
        $userRole = $this->firstOrCreateRole('user', 'Utilisateur');
        $adminRole = $this->firstOrCreateRole('admin', 'Administrateur');

        $user = User::factory()->create([
            'role_id' => $userRole->id,
            'prenom' => 'Grace',
            'nom' => 'Intercesseur',
            'email' => 'grace.prayer@example.com',
            'actif' => true,
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'prenom' => 'Alice',
            'nom' => 'Admin',
            'actif' => true,
        ]);

        $response = $this->actingAs($user)->post(route('prayer.store'), [
            'prenom' => 'Grace',
            'email' => 'grace.prayer@example.com',
            'pays' => 'Togo',
            'sujet' => 'Merci de prier pour une orientation claire.',
            'anonyme' => '1',
        ]);

        $response->assertRedirect(route('home') . '#home-prayer');

        $prayer = MurDePriere::query()
            ->where('user_id', $user->id)
            ->where('email', $user->email)
            ->latest('id')
            ->first();

        $this->assertNotNull($prayer);
        $this->assertDatabaseHas('mur_de_prieres', [
            'id' => $prayer->id,
            'statut' => 'en_attente',
            'type' => 'priere',
            'anonyme' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(PrayersManager::class)
            ->call('selectPrayer', $prayer->id)
            ->set('processingStatus', 'approuve')
            ->set('prayerType', 'priere')
            ->set('anonyme', true)
            ->call('updatePrayer')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('mur_de_prieres', [
            'id' => $prayer->id,
            'statut' => 'approuve',
            'type' => 'priere',
            'anonyme' => true,
        ]);

        $this->actingAs($user)
            ->get(route('panel.user.prayers'))
            ->assertOk()
            ->assertSeeText('Merci de prier pour une orientation claire.');
    }

    public function test_guest_support_is_counted_once_per_ip(): void
    {
        $prayer = MurDePriere::query()->create([
            'prenom' => 'Naomi',
            'pays' => 'Benin',
            'email' => 'naomi@example.com',
            'sujet' => 'Merci de prier pour ma famille.',
            'type' => 'priere',
            'anonyme' => false,
            'priants' => 0,
            'statut' => 'approuve',
        ]);

        $firstResponse = $this->post(route('prayer.support', $prayer->id), [
            'redirect_to' => route('contact.prayer.index') . '#prayer-wall',
        ]);

        $firstResponse->assertRedirect(route('contact.prayer.index') . '#prayer-wall');

        $secondResponse = $this->post(route('prayer.support', $prayer->id), [
            'redirect_to' => route('contact.prayer.index') . '#prayer-wall',
        ]);

        $secondResponse->assertRedirect(route('contact.prayer.index') . '#prayer-wall');

        $this->assertDatabaseHas('mur_de_prieres', [
            'id' => $prayer->id,
            'priants' => 1,
        ]);

        $this->assertEquals(1, PriereSoutien::query()->where('priere_id', $prayer->id)->count());
    }

    public function test_contact_prayer_page_shows_only_approved_prayer_requests(): void
    {
        MurDePriere::query()->create([
            'prenom' => 'Naomi',
            'pays' => 'Benin',
            'email' => 'naomi@example.com',
            'sujet' => 'Sujet visible sur le mur.',
            'type' => 'priere',
            'anonyme' => false,
            'priants' => 2,
            'statut' => 'approuve',
        ]);

        MurDePriere::query()->create([
            'prenom' => 'Paul',
            'pays' => 'Togo',
            'email' => 'paul@example.com',
            'sujet' => 'Sujet non approuve.',
            'type' => 'priere',
            'anonyme' => false,
            'priants' => 0,
            'statut' => 'en_attente',
        ]);

        $this->get(route('contact.prayer.index'))
            ->assertOk()
            ->assertSeeText('Sujet visible sur le mur.')
            ->assertDontSeeText('Sujet non approuve.');
    }

    public function test_home_page_shows_approved_prayer_requests_in_scrolling_section(): void
    {
        MurDePriere::query()->create([
            'prenom' => 'Naomi',
            'pays' => 'Benin',
            'email' => 'naomi@example.com',
            'sujet' => 'Merci de prier pour ma famille.',
            'type' => 'priere',
            'anonyme' => false,
            'priants' => 4,
            'statut' => 'approuve',
        ]);

        MurDePriere::query()->create([
            'prenom' => 'Paul',
            'pays' => 'Togo',
            'email' => 'paul@example.com',
            'sujet' => 'Sujet en attente qui ne doit pas apparaitre.',
            'type' => 'priere',
            'anonyme' => false,
            'priants' => 0,
            'statut' => 'en_attente',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSeeText('Merci de prier pour ma famille.')
            ->assertDontSeeText('Sujet en attente qui ne doit pas apparaitre.')
            ->assertSeeText(__('home.sections.prayer.requests_title'));
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
