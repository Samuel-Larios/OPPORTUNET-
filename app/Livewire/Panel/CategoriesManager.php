<?php

namespace App\Livewire\Panel;

use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CategoriesManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $typeFilter = '';

    #[Url(except: '')]
    public string $activeFilter = '';

    public ?int $editingCategoryId = null;

    public string $type = 'offre';
    public string $nomFr = '';
    public string $nomEn = '';
    public string $slug = '';
    public string $icone = '';
    public string $couleur = '';
    public string $descriptionFr = '';
    public string $descriptionEn = '';
    public string $ordre = '0';
    public bool $actif = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    public function updatedNomFr(string $value): void
    {
        $this->slug = Str::slug($value);
    }

    public function editCategory(int $categoryId): void
    {
        $category = Category::query()->findOrFail($categoryId);

        $this->editingCategoryId = $category->id;
        $this->type = (string) $category->type;
        $this->nomFr = (string) ($category->getRawOriginal('nom_fr') ?? $category->nom);
        $this->nomEn = (string) ($category->getRawOriginal('nom_en') ?? $category->nom);
        $this->slug = (string) $category->slug;
        $this->icone = (string) ($category->icone ?? '');
        $this->couleur = (string) ($category->couleur ?? '');
        $this->descriptionFr = (string) ($category->getRawOriginal('description_fr') ?? $category->description ?? '');
        $this->descriptionEn = (string) ($category->getRawOriginal('description_en') ?? $category->description ?? '');
        $this->ordre = (string) ($category->ordre ?? 0);
        $this->actif = (bool) $category->actif;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingCategoryId',
            'nomFr',
            'nomEn',
            'slug',
            'icone',
            'couleur',
            'descriptionFr',
            'descriptionEn',
            'ordre',
        ]);

        $this->type = 'offre';
        $this->ordre = '0';
        $this->actif = true;
        $this->resetValidation();
    }

    public function saveCategory(): void
    {
        $validated = $this->validate($this->rules());

        $slugBase = Str::slug($validated['nomFr']);
        $category = Category::query()->updateOrCreate(
            ['id' => $this->editingCategoryId],
            [
                'type' => $validated['type'],
                'nom' => $validated['nomFr'],
                'nom_fr' => $validated['nomFr'],
                'nom_en' => $validated['nomEn'] !== '' ? $validated['nomEn'] : $validated['nomFr'],
                'slug' => $this->editingCategoryId
                    ? $this->uniqueSlug($slugBase, $this->editingCategoryId)
                    : $this->uniqueSlug($slugBase),
                'icone' => $validated['icone'] ?: null,
                'couleur' => $validated['couleur'] ?: null,
                'description' => $validated['descriptionFr'] ?: null,
                'description_fr' => $validated['descriptionFr'] ?: null,
                'description_en' => $validated['descriptionEn'] !== '' ? $validated['descriptionEn'] : ($validated['descriptionFr'] ?: null),
                'actif' => $validated['actif'],
                'ordre' => $validated['ordre'] !== '' ? (int) $validated['ordre'] : 0,
            ]
        );

        if ($category->slug === '') {
            $category->update([
                'slug' => $this->uniqueSlug(Str::slug($validated['nomFr']), $category->id),
            ]);
        }

        session()->flash('panel_success', __('admin.flash.category_saved'));
        $this->resetForm();
    }

    public function deleteCategory(int $categoryId): void
    {
        Category::query()->findOrFail($categoryId)->delete();

        if ($this->editingCategoryId === $categoryId) {
            $this->resetForm();
        }

        session()->flash('panel_success', __('admin.flash.category_deleted'));
    }

    public function render(): View
    {
        $search = trim($this->search);

        $categories = Category::query()
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('nom', 'like', $term)
                        ->orWhere('nom_fr', 'like', $term)
                        ->orWhere('nom_en', 'like', $term)
                        ->orWhere('slug', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->activeFilter !== '', fn ($query) => $query->where('actif', $this->activeFilter === '1'))
            ->orderBy('type')
            ->orderBy('ordre')
            ->latest('updated_at')
            ->paginate(12);

        return view('livewire.panel.categories-manager', [
            'categories' => $categories,
            'categoryTypes' => ['offre', 'formation', 'blog', 'service'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'type' => ['required', Rule::in(['offre', 'formation', 'blog', 'service'])],
            'nomFr' => ['required', 'string', 'max:120'],
            'nomEn' => ['nullable', 'string', 'max:120'],
            'slug' => ['nullable', 'string', 'max:150'],
            'icone' => ['nullable', 'string', 'max:80'],
            'couleur' => ['nullable', 'string', 'max:20'],
            'descriptionFr' => ['nullable', 'string'],
            'descriptionEn' => ['nullable', 'string'],
            'ordre' => ['nullable', 'integer', 'min:0'],
            'actif' => ['boolean'],
        ];
    }

    protected function uniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = Str::slug($baseSlug) !== '' ? Str::slug($baseSlug) : 'categorie';
        $original = $slug;
        $counter = 1;

        while (Category::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
