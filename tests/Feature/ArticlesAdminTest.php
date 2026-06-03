<?php

namespace Tests\Feature;

use App\Livewire\Panel\ArticlesManager;
use App\Models\BlogArticle;
use App\Models\Category;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ArticlesAdminTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_editor_can_create_article_with_featured_image_and_delete_it(): void
    {
        Storage::fake('public');

        $editorRole = $this->firstOrCreateRole('editeur', 'Editeur');
        $editor = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Edith',
            'nom' => 'Editor',
            'actif' => true,
        ]);

        $category = Category::query()->create([
            'type' => 'blog',
            'nom' => 'Actualites',
            'nom_fr' => 'Actualites',
            'nom_en' => 'News',
            'slug' => 'actualites-admin',
            'actif' => true,
            'ordre' => 1,
        ]);

        $this->actingAs($editor);

        Livewire::test(ArticlesManager::class)
            ->set('categorieId', (string) $category->id)
            ->set('titreFr', 'Article admin test')
            ->set('titreEn', 'Admin test article')
            ->set('extraitFr', 'Extrait de demonstration')
            ->set('extraitEn', 'Demo excerpt')
            ->set('contenuFr', 'Contenu FR complet pour l article.')
            ->set('contenuEn', 'Full EN content for the article.')
            ->set('metaTitreFr', 'Meta FR')
            ->set('metaTitreEn', 'Meta EN')
            ->set('metaDescriptionFr', 'Description FR')
            ->set('metaDescriptionEn', 'Description EN')
            ->set('tags', 'admin, test')
            ->set('tempsLecture', '6 min')
            ->set('statut', 'publie')
            ->set('newImages', [
                UploadedFile::fake()->create('article-1.jpg', 120, 'image/jpeg'),
                UploadedFile::fake()->create('article-2.jpg', 120, 'image/jpeg'),
            ])
            ->set('newImageAltsFr', ['Image FR 1', 'Image FR 2'])
            ->set('newImageAltsEn', ['Image EN 1', 'Image EN 2'])
            ->set('featuredImageSelection', 'new:1')
            ->call('saveArticle')
            ->assertHasNoErrors();

        $article = BlogArticle::query()->where('slug', 'article-admin-test')->first();

        $this->assertNotNull($article);
        $this->assertDatabaseHas('blog_articles', [
            'id' => $article->id,
            'titre' => 'Article admin test',
            'statut' => 'publie',
        ]);
        $this->assertEquals(2, $article->images()->count());
        $this->assertDatabaseHas('blog_article_images', [
            'blog_article_id' => $article->id,
            'is_featured' => true,
            'alt_fr' => 'Image FR 2',
            'alt_en' => 'Image EN 2',
        ]);

        foreach ($article->images as $image) {
            Storage::disk('public')->assertExists($image->image_path);
        }

        $imagePaths = $article->images->pluck('image_path')->all();

        Livewire::test(ArticlesManager::class)
            ->call('deleteArticle', $article->id);

        $this->assertSoftDeleted('blog_articles', ['id' => $article->id]);
        $this->assertDatabaseMissing('blog_article_images', ['blog_article_id' => $article->id]);

        foreach ($imagePaths as $path) {
            Storage::disk('public')->assertMissing($path);
        }
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
