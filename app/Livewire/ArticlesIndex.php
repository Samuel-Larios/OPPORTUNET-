<?php

namespace App\Livewire;

use App\Models\BlogArticle;
use App\Models\Category;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ArticlesIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $category = '';

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'category'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'category']);
        $this->resetPage();
    }

    public function pageUrl(int $page): string
    {
        $query = [
            'q' => $this->search !== '' ? $this->search : null,
            'category' => $this->category !== '' ? $this->category : null,
        ];

        if ($page > 1) {
            $query['page'] = $page;
        }

        return Seo::localizedUrl(route('articles.index'), app()->getLocale(), array_filter($query, fn ($value) => $value !== null));
    }

    public function getAvailableCategoriesProperty()
    {
        return Category::query()
            ->where('type', 'blog')
            ->where('actif', true)
            ->orderBy('ordre')
            ->get();
    }

    public function render(): View
    {
        $filteredQuery = $this->filteredQuery();
        $articles = (clone $filteredQuery)
            ->with(['category', 'featuredImage'])
            ->orderByDesc('en_vedette')
            ->orderByDesc('publie_le')
            ->paginate(9);

        return view('livewire.articles-index', [
            'articles' => $articles,
            'availableCategories' => $this->availableCategories,
            'publishedCount' => BlogArticle::query()->where('statut', 'publie')->count(),
            'filteredCount' => $articles->total(),
            'featuredCount' => BlogArticle::query()->where('statut', 'publie')->where('en_vedette', true)->count(),
        ]);
    }

    protected function filteredQuery(): Builder
    {
        $search = trim($this->search);

        return BlogArticle::query()
            ->where('statut', 'publie')
            ->when($search !== '', function (Builder $builder) use ($search) {
                $term = '%' . $search . '%';

                $builder->where(function (Builder $nested) use ($term) {
                    $nested
                        ->where('titre', 'like', $term)
                        ->orWhere('titre_fr', 'like', $term)
                        ->orWhere('titre_en', 'like', $term)
                        ->orWhere('extrait', 'like', $term)
                        ->orWhere('extrait_fr', 'like', $term)
                        ->orWhere('extrait_en', 'like', $term)
                        ->orWhere('contenu', 'like', $term)
                        ->orWhere('contenu_fr', 'like', $term)
                        ->orWhere('contenu_en', 'like', $term)
                        ->orWhere('tags', 'like', $term);
                });
            })
            ->when($this->category !== '', fn (Builder $builder) => $builder->whereHas(
                'category',
                fn (Builder $categoryQuery) => $categoryQuery->where('slug', $this->category)
            ));
    }
}
