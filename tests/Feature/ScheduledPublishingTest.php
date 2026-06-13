<?php

namespace Tests\Feature;

use App\Models\BlogArticle;
use App\Models\Category;
use App\Models\Formation;
use App\Models\Opportunite;
use App\Models\Service;
use App\Models\SpiritualPublication;
use App\Models\Verset;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Panel\ArticlesManager;
use App\Livewire\Panel\CategoriesManager;
use App\Livewire\Panel\EditorOffersManager;
use App\Livewire\Panel\FormationsManager;
use App\Livewire\Panel\ServicesManager;
use App\Livewire\Panel\SpiritualPublicationsManager;
use App\Livewire\Panel\VersesManager;

class ScheduledPublishingTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_scheduled_publishing_command_publishes_all_supported_content_types(): void
    {
        $publishAt = Carbon::now()->subMinute();
        $editorRole = Role::query()->firstOrCreate([
            'nom' => 'editeur',
        ], [
            'libelle' => 'Editeur',
            'permissions' => json_encode(['*']),
            'actif' => true,
        ]);
        $user = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Paul',
            'nom' => 'Programmation',
            'actif' => true,
        ]);

        $category = Category::query()->create([
            'type' => 'service',
            'nom' => 'Categorie planifiee',
            'nom_fr' => 'Categorie planifiee',
            'nom_en' => 'Scheduled category',
            'slug' => 'categorie-planifiee',
            'actif' => false,
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'ordre' => 1,
        ]);

        $service = Service::query()->create([
            'titre' => 'Service planifie',
            'titre_fr' => 'Service planifie',
            'titre_en' => 'Scheduled service',
            'slug' => 'service-planifie',
            'description_courte' => 'Description',
            'description_courte_fr' => 'Description',
            'description_courte_en' => 'Description',
            'type' => 'autre',
            'devise' => 'XOF',
            'actif' => false,
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'ordre' => 1,
        ]);

        $verse = Verset::query()->create([
            'reference' => 'Jean 3:16',
            'reference_fr' => 'Jean 3:16',
            'reference_en' => 'John 3:16',
            'texte' => 'Car Dieu a tant aime le monde.',
            'texte_fr' => 'Car Dieu a tant aime le monde.',
            'texte_en' => 'For God so loved the world.',
            'version' => 'LSG',
            'version_fr' => 'LSG',
            'version_en' => 'KJV',
            'actif' => false,
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'ordre' => 1,
        ]);

        $spiritualPublication = SpiritualPublication::query()->create([
            'type' => 'pensee',
            'titre' => 'Pensee planifiee',
            'titre_fr' => 'Pensee planifiee',
            'titre_en' => 'Scheduled thought',
            'slug' => 'pensee-planifiee',
            'contenu' => 'Contenu spirituel.',
            'contenu_fr' => 'Contenu spirituel.',
            'contenu_en' => 'Spiritual content.',
            'actif' => false,
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'ordre' => 1,
        ]);

        $article = BlogArticle::query()->create([
            'user_id' => $user->id,
            'titre' => 'Article planifie',
            'titre_fr' => 'Article planifie',
            'titre_en' => 'Scheduled article',
            'slug' => 'article-planifie',
            'contenu' => 'Contenu article',
            'contenu_fr' => 'Contenu article',
            'contenu_en' => 'Article content',
            'statut' => 'brouillon',
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'scheduled_status' => 'publie',
        ]);

        $formation = Formation::query()->create([
            'titre' => 'Formation planifiee',
            'titre_fr' => 'Formation planifiee',
            'titre_en' => 'Scheduled training',
            'slug' => 'formation-planifiee',
            'description_courte' => 'Description courte',
            'description_courte_fr' => 'Description courte',
            'description_courte_en' => 'Short description',
            'mode' => 'en_ligne',
            'devise' => 'XOF',
            'statut' => 'brouillon',
            'inscriptions_ouvertes' => true,
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'scheduled_status' => 'ouverte',
        ]);

        $offer = Opportunite::query()->create([
            'user_id' => $user->id,
            'titre' => 'Offre planifiee',
            'titre_fr' => 'Offre planifiee',
            'titre_en' => 'Scheduled offer',
            'slug' => 'offre-planifiee',
            'type' => 'emploi',
            'description' => 'Description',
            'description_fr' => 'Description',
            'description_en' => 'Description',
            'statut' => 'brouillon',
            'auto_publish' => true,
            'scheduled_for' => $publishAt,
            'scheduled_status' => 'publie',
        ]);

        Artisan::call('content:publish-scheduled');

        $category->refresh();
        $service->refresh();
        $verse->refresh();
        $spiritualPublication->refresh();
        $article->refresh();
        $formation->refresh();
        $offer->refresh();

        $this->assertTrue($category->actif);
        $this->assertFalse($category->auto_publish);
        $this->assertNull($category->scheduled_for);
        $this->assertNotNull($category->published_at);

        $this->assertTrue($service->actif);
        $this->assertFalse($service->auto_publish);
        $this->assertNull($service->scheduled_for);
        $this->assertNotNull($service->published_at);

        $this->assertTrue($verse->actif);
        $this->assertFalse($verse->auto_publish);
        $this->assertNull($verse->scheduled_for);
        $this->assertNotNull($verse->published_at);

        $this->assertTrue($spiritualPublication->actif);
        $this->assertFalse($spiritualPublication->auto_publish);
        $this->assertNull($spiritualPublication->scheduled_for);
        $this->assertNotNull($spiritualPublication->published_at);

        $this->assertSame('publie', $article->statut);
        $this->assertFalse($article->auto_publish);
        $this->assertNull($article->scheduled_for);
        $this->assertNull($article->scheduled_status);
        $this->assertNotNull($article->publie_le);
        $this->assertNotNull($article->published_at);

        $this->assertSame('ouverte', $formation->statut);
        $this->assertFalse($formation->auto_publish);
        $this->assertNull($formation->scheduled_for);
        $this->assertNull($formation->scheduled_status);
        $this->assertNotNull($formation->published_at);

        $this->assertSame('publie', $offer->statut);
        $this->assertFalse($offer->auto_publish);
        $this->assertNull($offer->scheduled_for);
        $this->assertNull($offer->scheduled_status);
        $this->assertSame($publishAt->toDateString(), $offer->date_publication?->toDateString());
        $this->assertNotNull($offer->published_at);
    }

    public function test_admin_forms_can_save_scheduled_publications_consistently(): void
    {
        Storage::fake('public');

        $editorRole = Role::query()->firstOrCreate([
            'nom' => 'editeur',
        ], [
            'libelle' => 'Editeur',
            'permissions' => json_encode(['*']),
            'actif' => true,
        ]);

        $editor = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Esther',
            'nom' => 'Edition',
            'actif' => true,
        ]);

        $blogCategory = Category::query()->create([
            'type' => 'blog',
            'nom' => 'Actualites',
            'nom_fr' => 'Actualites',
            'nom_en' => 'News',
            'slug' => 'actualites-scheduled',
            'actif' => true,
            'ordre' => 1,
        ]);

        $serviceCategory = Category::query()->create([
            'type' => 'service',
            'nom' => 'Coaching',
            'nom_fr' => 'Coaching',
            'nom_en' => 'Coaching',
            'slug' => 'coaching-scheduled',
            'actif' => true,
            'ordre' => 1,
        ]);

        $trainingCategory = Category::query()->create([
            'type' => 'formation',
            'nom' => 'Leadership',
            'nom_fr' => 'Leadership',
            'nom_en' => 'Leadership',
            'slug' => 'leadership-scheduled',
            'actif' => true,
            'ordre' => 1,
        ]);

        $offerCategory = Category::query()->create([
            'type' => 'offre',
            'nom' => 'Emploi',
            'nom_fr' => 'Emploi',
            'nom_en' => 'Job',
            'slug' => 'emploi-scheduled',
            'actif' => true,
            'ordre' => 1,
        ]);

        $trainer = User::factory()->create([
            'role_id' => $editorRole->id,
            'prenom' => 'Theo',
            'nom' => 'Trainer',
            'actif' => true,
        ]);

        $this->actingAs($editor);

        $scheduleAt = now()->addHour()->format('Y-m-d\TH:i');

        Livewire::test(ArticlesManager::class)
            ->set('categorieId', (string) $blogCategory->id)
            ->set('titreFr', 'Article programme')
            ->set('contenuFr', 'Contenu article programme')
            ->set('scheduleEnabled', true)
            ->set('scheduleAt', $scheduleAt)
            ->set('newImages', [UploadedFile::fake()->create('article.jpg', 120, 'image/jpeg')])
            ->set('newImageAltsFr', ['Visuel'])
            ->set('newImageAltsEn', ['Visual'])
            ->set('featuredImageSelection', 'new:0')
            ->call('saveArticle')
            ->assertHasNoErrors();

        $article = BlogArticle::query()->where('slug', 'article-programme')->firstOrFail();
        $this->assertTrue($article->auto_publish);
        $this->assertSame('brouillon', $article->statut);
        $this->assertSame('publie', $article->scheduled_status);
        $this->assertFalse($article->images->isEmpty());

        Livewire::test(CategoriesManager::class)
            ->set('type', 'service')
            ->set('nomFr', 'Categorie programmee')
            ->set('scheduleEnabled', true)
            ->set('scheduleAt', $scheduleAt)
            ->call('saveCategory')
            ->assertHasNoErrors();

        $scheduledCategory = Category::query()->where('slug', 'categorie-programmee')->firstOrFail();
        $this->assertTrue($scheduledCategory->auto_publish);
        $this->assertFalse($scheduledCategory->actif);

        Livewire::test(ServicesManager::class)
            ->set('categorieId', (string) $serviceCategory->id)
            ->set('titreFr', 'Service programme')
            ->set('descriptionCourteFr', 'Description courte')
            ->set('type', 'coaching')
            ->set('devise', 'XOF')
            ->set('scheduleEnabled', true)
            ->set('scheduleAt', $scheduleAt)
            ->call('saveService')
            ->assertHasNoErrors();

        $service = Service::query()->where('slug', 'service-programme')->firstOrFail();
        $this->assertTrue($service->auto_publish);
        $this->assertFalse($service->actif);

        Livewire::test(FormationsManager::class)
            ->set('categorieId', (string) $trainingCategory->id)
            ->set('formateurId', (string) $trainer->id)
            ->set('titreFr', 'Formation programmee')
            ->set('descriptionCourteFr', 'Description formation')
            ->set('mode', 'en_ligne')
            ->set('devise', 'XOF')
            ->set('scheduleEnabled', true)
            ->set('scheduleAt', $scheduleAt)
            ->call('saveFormation')
            ->assertHasNoErrors();

        $formation = Formation::query()->where('slug', 'formation-programmee')->firstOrFail();
        $this->assertTrue($formation->auto_publish);
        $this->assertSame('brouillon', $formation->statut);
        $this->assertSame('ouverte', $formation->scheduled_status);

        Livewire::test(EditorOffersManager::class)
            ->set('categorieId', (string) $offerCategory->id)
            ->set('titreFr', 'Offre programmee')
            ->set('type', 'emploi')
            ->set('contrat', 'cdd')
            ->set('descriptionFr', 'Description offre')
            ->set('scheduleEnabled', true)
            ->set('scheduleAt', $scheduleAt)
            ->call('saveOffer')
            ->assertHasNoErrors();

        $offer = Opportunite::query()->where('slug', 'offre-programmee')->firstOrFail();
        $this->assertTrue($offer->auto_publish);
        $this->assertSame('brouillon', $offer->statut);
        $this->assertSame('publie', $offer->scheduled_status);

        Livewire::test(VersesManager::class)
            ->set('referenceFr', 'Psaume 23:1')
            ->set('texteFr', 'L Eternel est mon berger.')
            ->set('versionFr', 'LSG')
            ->set('scheduleEnabled', true)
            ->set('scheduleAt', $scheduleAt)
            ->call('saveVerse')
            ->assertHasNoErrors();

        $verse = Verset::query()->where('reference', 'Psaume 23:1')->firstOrFail();
        $this->assertTrue($verse->auto_publish);
        $this->assertFalse($verse->actif);

        Livewire::test(SpiritualPublicationsManager::class, ['type' => 'pensee'])
            ->set('titleFr', 'Pensee programmee')
            ->set('contentFr', 'Contenu pensee')
            ->set('scheduleEnabled', true)
            ->set('scheduleAt', $scheduleAt)
            ->call('savePublication')
            ->assertHasNoErrors();

        $publication = SpiritualPublication::query()->where('slug', 'pensee-programmee')->firstOrFail();
        $this->assertTrue($publication->auto_publish);
        $this->assertFalse($publication->actif);

        Livewire::test(SpiritualPublicationsManager::class, ['type' => 'priere_jour'])
            ->set('titleFr', 'Priere programmee')
            ->set('contentFr', 'Contenu priere')
            ->set('scheduleEnabled', true)
            ->set('scheduleAt', $scheduleAt)
            ->call('savePublication')
            ->assertHasNoErrors();

        $dailyPrayer = SpiritualPublication::query()
            ->where('type', 'priere_jour')
            ->where('slug', 'priere-programmee')
            ->firstOrFail();

        $this->assertTrue($dailyPrayer->auto_publish);
        $this->assertFalse($dailyPrayer->actif);
    }

    public function test_livewire_daily_prayers_manager_publishes_due_scheduled_content(): void
    {
        $publication = SpiritualPublication::query()->create([
            'type' => 'priere_jour',
            'titre' => 'Priere arrivee',
            'titre_fr' => 'Priere arrivee',
            'titre_en' => 'Due prayer',
            'slug' => 'priere-arrivee',
            'contenu' => 'Contenu de priere',
            'contenu_fr' => 'Contenu de priere',
            'contenu_en' => 'Prayer content',
            'actif' => false,
            'auto_publish' => true,
            'scheduled_for' => now()->subMinute(),
            'ordre' => 1,
        ]);

        Livewire::test(SpiritualPublicationsManager::class, ['type' => 'priere_jour'])
            ->call('refreshScheduledPublications');

        $publication->refresh();

        $this->assertTrue($publication->actif);
        $this->assertFalse($publication->auto_publish);
        $this->assertNull($publication->scheduled_for);
        $this->assertNotNull($publication->published_at);
    }
}
