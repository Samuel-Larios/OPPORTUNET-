<?php

namespace Tests\Feature;

use App\Livewire\Panel\CategoriesManager;
use App\Models\Category;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CategoriesAdminTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_editor_can_create_update_and_delete_category(): void
    {
        $editorRole = $this->firstOrCreateRole('editeur', 'Editeur');
        $editor = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Clara',
            'nom' => 'Categorie',
            'actif' => true,
        ]);

        $this->actingAs($editor);

        Livewire::test(CategoriesManager::class)
            ->set('type', 'service')
            ->set('nomFr', 'Diagnostic express')
            ->set('nomEn', 'Express review')
            ->assertSet('slug', 'diagnostic-express')
            ->set('icone', 'sparkles')
            ->set('couleur', '#D97706')
            ->set('descriptionFr', 'Categorie de test pour les services.')
            ->set('descriptionEn', 'Test category for services.')
            ->set('ordre', '9')
            ->set('actif', true)
            ->call('saveCategory')
            ->assertHasNoErrors();

        $category = Category::query()->where('slug', 'diagnostic-express')->first();

        $this->assertNotNull($category);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'type' => 'service',
            'nom' => 'Diagnostic express',
            'nom_en' => 'Express review',
            'actif' => true,
        ]);

        Service::query()->create([
            'categorie_id' => $category->id,
            'titre' => 'Service test',
            'titre_fr' => 'Service test',
            'titre_en' => 'Test service',
            'slug' => 'service-test-categorie-admin',
            'description_courte' => 'Description courte',
            'description_courte_fr' => 'Description courte',
            'description_courte_en' => 'Short description',
            'type' => 'autre',
            'devise' => 'XOF',
            'actif' => true,
            'en_vedette' => false,
            'ordre' => 1,
        ]);

        Livewire::test(CategoriesManager::class)
            ->call('editCategory', $category->id)
            ->set('nomFr', 'Diagnostic premium')
            ->assertSet('slug', 'diagnostic-premium')
            ->call('saveCategory')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'slug' => 'diagnostic-premium',
            'nom' => 'Diagnostic premium',
        ]);

        Livewire::test(CategoriesManager::class)
            ->call('deleteCategory', $category->id);

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('services', [
            'slug' => 'service-test-categorie-admin',
            'categorie_id' => null,
        ]);
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
