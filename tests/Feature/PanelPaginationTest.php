<?php

namespace Tests\Feature;

use App\Livewire\Panel\EditorOffersManager;
use App\Models\Category;
use App\Models\Opportunite;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Livewire\Livewire;
use Tests\TestCase;

class PanelPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_editor_offers_manager_can_navigate_between_pages(): void
    {
        $editorRole = Role::query()->firstOrCreate([
            'nom' => 'editeur',
        ], [
            'libelle' => 'Editeur',
            'permissions' => json_encode(['*']),
            'actif' => true,
        ]);

        $editor = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Paula',
            'nom' => 'Pagination',
            'actif' => true,
        ]);

        Category::query()->create([
            'type' => 'offre',
            'nom' => 'Opportunites',
            'nom_fr' => 'Opportunites',
            'nom_en' => 'Opportunities',
            'slug' => 'opportunites-pagination',
            'actif' => true,
            'ordre' => 1,
        ]);

        $baseCreatedAt = Carbon::create(2026, 6, 15, 12, 0, 0);

        for ($index = 1; $index <= 15; $index++) {
            $suffix = str_pad((string) $index, 2, '0', STR_PAD_LEFT);
            $createdAt = $baseCreatedAt->copy()->addMinutes($index);

            Opportunite::query()->forceCreate([
                'user_id' => $editor->id,
                'titre' => "Offre pagination {$suffix}",
                'titre_fr' => "Offre pagination {$suffix}",
                'titre_en' => "Offer pagination {$suffix}",
                'slug' => "offre-pagination-{$suffix}",
                'type' => 'emploi',
                'description' => "Description {$index}",
                'description_fr' => "Description {$index}",
                'description_en' => "Description {$index}",
                'statut' => 'publie',
                'date_publication' => now()->subDays($index),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->actingAs($editor);

        Livewire::test(EditorOffersManager::class)
            ->assertSee('Offre pagination 15')
            ->assertDontSee('Offre pagination 05')
            ->call('gotoPage', 2)
            ->assertSee('Offre pagination 05')
            ->assertDontSee('Offre pagination 15');
    }
}
