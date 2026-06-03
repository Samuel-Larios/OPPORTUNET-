<?php

namespace App\Livewire\Panel;

use App\Models\BlogArticle;
use App\Models\BlogArticleImage;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ArticlesManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(except: '')]
    public string $categoryFilter = '';

    public ?int $editingArticleId = null;

    public string $categorieId = '';
    public string $titreFr = '';
    public string $titreEn = '';
    public string $extraitFr = '';
    public string $extraitEn = '';
    public string $contenuFr = '';
    public string $contenuEn = '';
    public string $metaTitreFr = '';
    public string $metaTitreEn = '';
    public string $metaDescriptionFr = '';
    public string $metaDescriptionEn = '';
    public string $tags = '';
    public string $tempsLecture = '';
    public string $datePublication = '';
    public string $statut = 'brouillon';
    public bool $enVedette = false;
    public bool $commentairesActifs = true;

    /** @var array<int, TemporaryUploadedFile> */
    public array $newImages = [];

    /** @var array<int, string> */
    public array $newImageAltsFr = [];

    /** @var array<int, string> */
    public array $newImageAltsEn = [];

    /** @var array<int, array<string, mixed>> */
    public array $existingImages = [];

    /** @var array<int, string> */
    public array $existingImageAltsFr = [];

    /** @var array<int, string> */
    public array $existingImageAltsEn = [];

    public string $featuredImageSelection = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedNewImages(): void
    {
        $this->syncNewImageAltArrays();

        if ($this->featuredImageSelection === '' && $this->newImages !== []) {
            $this->featuredImageSelection = 'new:0';
        }
    }

    public function editArticle(int $articleId): void
    {
        $article = BlogArticle::query()
            ->with('images')
            ->findOrFail($articleId);

        $this->editingArticleId = $article->id;
        $this->categorieId = (string) ($article->categorie_id ?? '');
        $this->titreFr = (string) ($article->getRawOriginal('titre_fr') ?? $article->titre);
        $this->titreEn = (string) ($article->getRawOriginal('titre_en') ?? $article->titre);
        $this->extraitFr = (string) ($article->getRawOriginal('extrait_fr') ?? $article->extrait ?? '');
        $this->extraitEn = (string) ($article->getRawOriginal('extrait_en') ?? $article->extrait ?? '');
        $this->contenuFr = (string) ($article->getRawOriginal('contenu_fr') ?? $article->contenu);
        $this->contenuEn = (string) ($article->getRawOriginal('contenu_en') ?? $article->contenu);
        $this->metaTitreFr = (string) ($article->getRawOriginal('meta_titre_fr') ?? $article->meta_titre ?? '');
        $this->metaTitreEn = (string) ($article->getRawOriginal('meta_titre_en') ?? $article->meta_titre ?? '');
        $this->metaDescriptionFr = (string) ($article->getRawOriginal('meta_description_fr') ?? $article->meta_description ?? '');
        $this->metaDescriptionEn = (string) ($article->getRawOriginal('meta_description_en') ?? $article->meta_description ?? '');
        $this->tags = implode(', ', $article->tags ?? []);
        $this->tempsLecture = (string) ($article->temps_lecture ?? '');
        $this->datePublication = $article->publie_le?->format('Y-m-d') ?? '';
        $this->statut = (string) $article->statut;
        $this->enVedette = (bool) $article->en_vedette;
        $this->commentairesActifs = (bool) $article->commentaires_actifs;
        $this->newImages = [];
        $this->newImageAltsFr = [];
        $this->newImageAltsEn = [];

        $this->existingImages = $article->images->map(fn (BlogArticleImage $image) => [
            'id' => $image->id,
            'path' => $image->image_path,
            'url' => $image->publicUrl(),
            'sort_order' => $image->sort_order,
            'is_featured' => $image->is_featured,
        ])->values()->all();

        $this->existingImageAltsFr = $article->images
            ->mapWithKeys(fn (BlogArticleImage $image) => [$image->id => (string) ($image->getRawOriginal('alt_fr') ?? $image->alt ?? '')])
            ->all();

        $this->existingImageAltsEn = $article->images
            ->mapWithKeys(fn (BlogArticleImage $image) => [$image->id => (string) ($image->getRawOriginal('alt_en') ?? $image->alt ?? '')])
            ->all();

        $featuredExisting = $article->images->firstWhere('is_featured', true);
        $this->featuredImageSelection = $featuredExisting ? 'existing:' . $featuredExisting->id : '';
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingArticleId',
            'categorieId',
            'titreFr',
            'titreEn',
            'extraitFr',
            'extraitEn',
            'contenuFr',
            'contenuEn',
            'metaTitreFr',
            'metaTitreEn',
            'metaDescriptionFr',
            'metaDescriptionEn',
            'tags',
            'tempsLecture',
            'datePublication',
            'newImages',
            'newImageAltsFr',
            'newImageAltsEn',
            'existingImages',
            'existingImageAltsFr',
            'existingImageAltsEn',
            'featuredImageSelection',
        ]);

        $this->statut = 'brouillon';
        $this->enVedette = false;
        $this->commentairesActifs = true;
        $this->resetValidation();
    }

    public function removeExistingImage(int $imageId): void
    {
        $image = BlogArticleImage::query()->findOrFail($imageId);

        if ($this->editingArticleId && $image->blog_article_id !== $this->editingArticleId) {
            return;
        }

        $wasFeatured = $this->featuredImageSelection === 'existing:' . $imageId;
        $this->cleanupStoredImage($image->image_path);
        $image->delete();

        unset($this->existingImageAltsFr[$imageId], $this->existingImageAltsEn[$imageId]);
        $this->existingImages = array_values(array_filter(
            $this->existingImages,
            fn (array $existingImage) => (int) $existingImage['id'] !== $imageId
        ));

        if ($wasFeatured) {
            $this->featuredImageSelection = $this->existingImages !== []
                ? 'existing:' . $this->existingImages[0]['id']
                : ($this->newImages !== [] ? 'new:0' : '');
        }
    }

    public function removeNewImage(int $index): void
    {
        if (! isset($this->newImages[$index])) {
            return;
        }

        $newImages = $this->newImages;
        unset($newImages[$index]);
        $this->newImages = array_values($newImages);

        $newAltsFr = $this->newImageAltsFr;
        unset($newAltsFr[$index]);
        $this->newImageAltsFr = array_values($newAltsFr);

        $newAltsEn = $this->newImageAltsEn;
        unset($newAltsEn[$index]);
        $this->newImageAltsEn = array_values($newAltsEn);

        if ($this->featuredImageSelection === 'new:' . $index) {
            $this->featuredImageSelection = $this->existingImages !== []
                ? 'existing:' . $this->existingImages[0]['id']
                : ($this->newImages !== [] ? 'new:0' : '');
        } else {
            $this->reindexFeaturedSelectionForNewImages($index);
        }
    }

    public function saveArticle(): void
    {
        $validated = $this->validate($this->rules());

        $currentExistingCount = count($this->existingImages);
        $newImagesCount = count($this->newImages);
        $totalImages = $currentExistingCount + $newImagesCount;

        if ($totalImages < 1) {
            $this->addError('newImages', __('admin.articles.validation.image_required'));
            return;
        }

        if ($totalImages > 5) {
            $this->addError('newImages', __('admin.articles.validation.image_limit'));
            return;
        }

        if ($this->featuredImageSelection === '') {
            $this->addError('featuredImageSelection', __('admin.articles.validation.featured_required'));
            return;
        }

        if (str_starts_with($this->featuredImageSelection, 'existing:')) {
            $imageId = (int) Str::after($this->featuredImageSelection, 'existing:');
            if (! collect($this->existingImages)->contains(fn (array $image) => (int) $image['id'] === $imageId)) {
                $this->addError('featuredImageSelection', __('admin.articles.validation.featured_required'));
                return;
            }
        }

        if (str_starts_with($this->featuredImageSelection, 'new:')) {
            $newIndex = (int) Str::after($this->featuredImageSelection, 'new:');
            if (! isset($this->newImages[$newIndex])) {
                $this->addError('featuredImageSelection', __('admin.articles.validation.featured_required'));
                return;
            }
        }

        $slug = Str::slug($validated['titreFr']);

        $article = BlogArticle::query()->updateOrCreate(
            ['id' => $this->editingArticleId],
            [
                'user_id' => auth()->id(),
                'categorie_id' => $validated['categorieId'] !== '' ? (int) $validated['categorieId'] : null,
                'titre' => $validated['titreFr'],
                'titre_fr' => $validated['titreFr'],
                'titre_en' => $validated['titreEn'] !== '' ? $validated['titreEn'] : $validated['titreFr'],
                'slug' => $this->editingArticleId
                    ? BlogArticle::query()->find($this->editingArticleId)?->slug ?? $slug
                    : $this->uniqueSlug($slug),
                'extrait' => $validated['extraitFr'] ?: null,
                'extrait_fr' => $validated['extraitFr'] ?: null,
                'extrait_en' => $validated['extraitEn'] !== '' ? $validated['extraitEn'] : ($validated['extraitFr'] ?: null),
                'contenu' => $validated['contenuFr'],
                'contenu_fr' => $validated['contenuFr'],
                'contenu_en' => $validated['contenuEn'] !== '' ? $validated['contenuEn'] : $validated['contenuFr'],
                'meta_titre' => $validated['metaTitreFr'] ?: null,
                'meta_titre_fr' => $validated['metaTitreFr'] ?: null,
                'meta_titre_en' => $validated['metaTitreEn'] !== '' ? $validated['metaTitreEn'] : ($validated['metaTitreFr'] ?: null),
                'meta_description' => $validated['metaDescriptionFr'] ?: null,
                'meta_description_fr' => $validated['metaDescriptionFr'] ?: null,
                'meta_description_en' => $validated['metaDescriptionEn'] !== '' ? $validated['metaDescriptionEn'] : ($validated['metaDescriptionFr'] ?: null),
                'tags' => $this->parseTags($validated['tags']),
                'statut' => $validated['statut'],
                'publie_le' => $validated['datePublication'] ?: ($validated['statut'] === 'publie' ? now() : null),
                'en_vedette' => $validated['enVedette'],
                'commentaires_actifs' => $validated['commentairesActifs'],
                'temps_lecture' => $validated['tempsLecture'] ?: null,
            ]
        );

        if ($article->wasRecentlyCreated === false && $article->slug === '') {
            $article->update(['slug' => $this->uniqueSlug($slug)]);
        }

        $this->syncExistingImageTranslations($article);
        $this->storeNewImages($article);
        $this->syncFeaturedImage($article);
        $this->syncLegacyCoverFields($article);

        session()->flash('panel_success', __('admin.flash.article_saved'));
        $this->resetForm();
    }

    public function deleteArticle(int $articleId): void
    {
        $article = BlogArticle::query()->with('images')->findOrFail($articleId);

        foreach ($article->images as $image) {
            $this->cleanupStoredImage($image->image_path);
        }

        $article->images()->delete();
        $article->delete();

        if ($this->editingArticleId === $articleId) {
            $this->resetForm();
        }

        session()->flash('panel_success', __('admin.flash.article_deleted'));
    }

    public function render(): View
    {
        $search = trim($this->search);

        $articles = BlogArticle::query()
            ->with(['category', 'images'])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('titre', 'like', $term)
                        ->orWhere('titre_fr', 'like', $term)
                        ->orWhere('titre_en', 'like', $term)
                        ->orWhere('extrait', 'like', $term)
                        ->orWhere('extrait_fr', 'like', $term)
                        ->orWhere('extrait_en', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->when($this->categoryFilter !== '', fn ($query) => $query->whereHas(
                'category',
                fn ($categoryQuery) => $categoryQuery->where('slug', $this->categoryFilter)
            ))
            ->latest()
            ->paginate(10);

        return view('livewire.panel.articles-manager', [
            'articles' => $articles,
            'categories' => Category::query()->where('type', 'blog')->where('actif', true)->orderBy('ordre')->get(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'categorieId' => ['nullable', 'exists:categories,id'],
            'titreFr' => ['required', 'string', 'max:250'],
            'titreEn' => ['nullable', 'string', 'max:250'],
            'extraitFr' => ['nullable', 'string'],
            'extraitEn' => ['nullable', 'string'],
            'contenuFr' => ['required', 'string'],
            'contenuEn' => ['nullable', 'string'],
            'metaTitreFr' => ['nullable', 'string', 'max:200'],
            'metaTitreEn' => ['nullable', 'string', 'max:200'],
            'metaDescriptionFr' => ['nullable', 'string'],
            'metaDescriptionEn' => ['nullable', 'string'],
            'tags' => ['nullable', 'string', 'max:600'],
            'tempsLecture' => ['nullable', 'string', 'max:20'],
            'datePublication' => ['nullable', 'date'],
            'statut' => ['required', Rule::in(['brouillon', 'publie', 'archive'])],
            'enVedette' => ['boolean'],
            'commentairesActifs' => ['boolean'],
            'newImages' => ['array', 'max:5'],
            'newImages.*' => ['image', 'max:4096'],
            'newImageAltsFr' => ['array'],
            'newImageAltsFr.*' => ['nullable', 'string', 'max:200'],
            'newImageAltsEn' => ['array'],
            'newImageAltsEn.*' => ['nullable', 'string', 'max:200'],
            'existingImageAltsFr' => ['array'],
            'existingImageAltsFr.*' => ['nullable', 'string', 'max:200'],
            'existingImageAltsEn' => ['array'],
            'existingImageAltsEn.*' => ['nullable', 'string', 'max:200'],
        ];
    }

    protected function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'article';
        $original = $slug;
        $counter = 1;

        while (BlogArticle::query()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function parseTags(string $tags): array
    {
        return collect(explode(',', $tags))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
    }

    protected function syncNewImageAltArrays(): void
    {
        foreach ($this->newImages as $index => $image) {
            $this->newImageAltsFr[$index] = $this->newImageAltsFr[$index] ?? '';
            $this->newImageAltsEn[$index] = $this->newImageAltsEn[$index] ?? '';
        }

        $this->newImageAltsFr = array_values($this->newImageAltsFr);
        $this->newImageAltsEn = array_values($this->newImageAltsEn);
    }

    protected function reindexFeaturedSelectionForNewImages(int $removedIndex): void
    {
        if (! str_starts_with($this->featuredImageSelection, 'new:')) {
            return;
        }

        $currentIndex = (int) Str::after($this->featuredImageSelection, 'new:');

        if ($currentIndex > $removedIndex) {
            $this->featuredImageSelection = 'new:' . ($currentIndex - 1);
        }
    }

    protected function syncExistingImageTranslations(BlogArticle $article): void
    {
        foreach ($article->images as $image) {
            $image->update([
                'alt' => $this->existingImageAltsFr[$image->id] ?: ($this->existingImageAltsEn[$image->id] ?: $article->titre),
                'alt_fr' => $this->existingImageAltsFr[$image->id] ?: null,
                'alt_en' => $this->existingImageAltsEn[$image->id] ?: ($this->existingImageAltsFr[$image->id] ?: null),
            ]);
        }
    }

    protected function storeNewImages(BlogArticle $article): void
    {
        $existingCount = $article->images()->count();
        $newImages = array_slice($this->newImages, 0, max(0, 5 - $existingCount));

        foreach ($newImages as $index => $image) {
            $path = $image->store('blog-articles', 'public');

            $createdImage = $article->images()->create([
                'image_path' => $path,
                'alt' => $this->newImageAltsFr[$index] ?: ($this->newImageAltsEn[$index] ?: $article->titre),
                'alt_fr' => $this->newImageAltsFr[$index] ?: null,
                'alt_en' => $this->newImageAltsEn[$index] ?: ($this->newImageAltsFr[$index] ?: null),
                'is_featured' => false,
                'sort_order' => $article->images()->max('sort_order') + 1,
            ]);

            if ($this->featuredImageSelection === 'new:' . $index) {
                $this->featuredImageSelection = 'existing:' . $createdImage->id;
            }
        }
    }

    protected function syncFeaturedImage(BlogArticle $article): void
    {
        $article->images()->update(['is_featured' => false]);

        if (! str_starts_with($this->featuredImageSelection, 'existing:')) {
            $featuredImage = $article->images()->orderBy('sort_order')->first();

            if ($featuredImage) {
                $featuredImage->update(['is_featured' => true]);
            }

            return;
        }

        $featuredImageId = (int) Str::after($this->featuredImageSelection, 'existing:');
        $article->images()->whereKey($featuredImageId)->update(['is_featured' => true]);
    }

    protected function syncLegacyCoverFields(BlogArticle $article): void
    {
        $featuredImage = $article->images()->where('is_featured', true)->first() ?? $article->images()->orderBy('sort_order')->first();

        $article->update([
            'image_couverture' => $featuredImage?->image_path,
            'image_alt' => $featuredImage?->alt,
            'image_alt_fr' => $featuredImage?->getRawOriginal('alt_fr'),
            'image_alt_en' => $featuredImage?->getRawOriginal('alt_en'),
        ]);
    }

    protected function cleanupStoredImage(string $path): void
    {
        if ($path === '' || preg_match('/^https?:\/\//i', $path) || str_starts_with($path, 'images/')) {
            return;
        }

        $normalizedPath = str_starts_with($path, 'storage/')
            ? Str::after($path, 'storage/')
            : $path;

        Storage::disk('public')->delete($normalizedPath);
    }
}
