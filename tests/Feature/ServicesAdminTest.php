<?php

namespace Tests\Feature;

use App\Livewire\Panel\ServicesManager;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ServicesAdminTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_editor_can_create_service_with_image_and_delete_it(): void
    {
        Storage::fake('public');

        $editorRole = $this->firstOrCreateRole('editeur', 'Editeur');
        $editor = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Sonia',
            'nom' => 'Service',
            'actif' => true,
        ]);

        $categoryId = \App\Models\Category::query()
            ->where('type', 'service')
            ->where('slug', 'coaching')
            ->value('id');

        $this->actingAs($editor);

        Livewire::test(ServicesManager::class)
            ->set('categorieId', (string) $categoryId)
            ->set('titreFr', 'Diagnostic de profil')
            ->set('titreEn', 'Profile review')
            ->set('descriptionCourteFr', 'Analyse rapide de votre positionnement.')
            ->set('descriptionCourteEn', 'Quick analysis of your positioning.')
            ->set('descriptionLongueFr', 'Une session pour clarifier les priorites de votre profil.')
            ->set('descriptionLongueEn', 'A session to clarify your profile priorities.')
            ->set('icone', 'sparkles')
            ->set('type', 'coaching')
            ->set('prix', '12000')
            ->set('devise', 'XOF')
            ->set('dureeFr', '45 minutes')
            ->set('dureeEn', '45 minutes')
            ->set('whatsappMessageFr', 'Bonjour, je souhaite reserver ce service.')
            ->set('whatsappMessageEn', 'Hello, I would like to book this service.')
            ->set('ordre', '8')
            ->set('actif', true)
            ->set('enVedette', true)
            ->set('image', UploadedFile::fake()->create('service-cover.jpg', 120, 'image/jpeg'))
            ->call('saveService')
            ->assertHasNoErrors();

        $service = Service::query()->where('slug', 'diagnostic-de-profil')->first();

        $this->assertNotNull($service);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'titre' => 'Diagnostic de profil',
            'type' => 'coaching',
            'actif' => true,
            'en_vedette' => true,
        ]);

        $this->assertNotNull($service->image);
        Storage::disk('public')->assertExists($service->image);

        $imagePath = $service->image;

        Livewire::test(ServicesManager::class)
            ->call('deleteService', $service->id);

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
        Storage::disk('public')->assertMissing($imagePath);
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
