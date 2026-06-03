<?php

namespace Tests\Feature;

use App\Livewire\Panel\FormationsManager;
use App\Models\Category;
use App\Models\Formation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class TrainingsAdminTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_editor_can_create_training_with_cover_and_delete_it(): void
    {
        Storage::fake('public');

        $editorRole = $this->firstOrCreateRole('editeur', 'Editeur');
        $editor = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Sonia',
            'nom' => 'Formation',
            'actif' => true,
        ]);

        $categoryId = Category::query()
            ->where('type', 'formation')
            ->value('id');

        $this->actingAs($editor);

        Livewire::test(FormationsManager::class)
            ->set('categorieId', (string) $categoryId)
            ->set('titreFr', 'Masterclass Leadership')
            ->set('titreEn', 'Leadership Masterclass')
            ->set('descriptionCourteFr', 'Une formation intensive pour renforcer votre leadership.')
            ->set('descriptionCourteEn', 'An intensive training to strengthen your leadership.')
            ->set('descriptionLongueFr', 'Programme complet avec strategie, posture et execution.')
            ->set('descriptionLongueEn', 'Full program on strategy, posture, and execution.')
            ->set('mode', 'en_ligne')
            ->set('lienEnLigne', 'https://example.com/training-room')
            ->set('prix', '35000')
            ->set('devise', 'XOF')
            ->set('dureeHeures', '12')
            ->set('nbSeances', '4')
            ->set('dateDebut', now()->addDays(14)->format('Y-m-d'))
            ->set('dateFin', now()->addDays(21)->format('Y-m-d'))
            ->set('heureDebut', '18:00')
            ->set('fuseauHoraire', 'Africa/Cotonou')
            ->set('placesMax', '25')
            ->set('placesRestantes', '25')
            ->set('niveauFr', 'Intermediaire')
            ->set('niveauEn', 'Intermediate')
            ->set('prerequisFr', 'Avoir deja mene une equipe.')
            ->set('objectifsFr', 'Clarifier la vision et la prise de decision.')
            ->set('programmeFr', 'Modules leadership, communication et execution.')
            ->set('certificatFr', 'Certificat de participation')
            ->set('statut', 'ouverte')
            ->set('inscriptionsOuvertes', true)
            ->set('enVedette', true)
            ->set('imageCouverture', UploadedFile::fake()->create('training-cover.jpg', 120, 'image/jpeg'))
            ->call('saveFormation')
            ->assertHasNoErrors();

        $formation = Formation::query()->where('slug', 'masterclass-leadership')->first();

        $this->assertNotNull($formation);
        $this->assertDatabaseHas('formations', [
            'id' => $formation->id,
            'titre' => 'Masterclass Leadership',
            'statut' => 'ouverte',
            'inscriptions_ouvertes' => true,
            'en_vedette' => true,
        ]);
        Storage::disk('public')->assertExists($formation->image_couverture);

        $coverPath = $formation->image_couverture;

        Livewire::test(FormationsManager::class)
            ->call('deleteFormation', $formation->id);

        $this->assertSoftDeleted('formations', ['id' => $formation->id]);
        Storage::disk('public')->assertMissing($coverPath);
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
