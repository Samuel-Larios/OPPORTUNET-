<?php

namespace App\Livewire;

use App\Models\Opportunite;
use App\Support\Seo;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class OffersIndex extends Component
{
    use WithPagination;

    public string $siteEmail = 'contact@opportunetmondiale.com';

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $type = '';

    #[Url(except: '')]
    public string $contrat = '';

    #[Url(except: '')]
    public string $pays = '';

    #[Url(except: false)]
    public bool $teletravail = false;

    #[Url(except: false)]
    public bool $urgent = false;

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'type', 'contrat', 'pays', 'teletravail', 'urgent'], true)) {
            $this->resetPage();
        }
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'type', 'contrat', 'pays', 'teletravail', 'urgent']);
        $this->resetPage();
    }

    public function pageUrl(int $page): string
    {
        $query = [
            'q' => $this->search !== '' ? $this->search : null,
            'type' => $this->type !== '' ? $this->type : null,
            'contrat' => $this->contrat !== '' ? $this->contrat : null,
            'pays' => $this->pays !== '' ? $this->pays : null,
            'teletravail' => $this->teletravail ? 1 : null,
            'urgent' => $this->urgent ? 1 : null,
        ];

        if ($page > 1) {
            $query['page'] = $page;
        }

        return Seo::localizedUrl(route('offers.index'), app()->getLocale(), array_filter($query, fn ($value) => $value !== null));
    }

    public function getAvailableCountriesProperty()
    {
        return Opportunite::query()
            ->where('statut', 'publie')
            ->whereNotNull('pays')
            ->where('pays', '!=', '')
            ->distinct()
            ->orderBy('pays')
            ->pluck('pays');
    }

    public function render(): View
    {
        $filteredQuery = $this->filteredQuery();
        $opportunities = (clone $filteredQuery)
            ->orderByDesc('en_vedette')
            ->orderByDesc('urgent')
            ->orderByDesc('date_publication')
            ->paginate(9);

        return view('livewire.offers-index', [
            'opportunities' => $opportunities,
            'availableCountries' => $this->availableCountries,
            'publishedCount' => Opportunite::query()
                ->where('statut', 'publie')
                ->count(),
            'filteredCount' => $opportunities->total(),
        ]);
    }

    protected function filteredQuery(): Builder
    {
        $search = trim($this->search);

        return Opportunite::query()
            ->where('statut', 'publie')
            ->when($search !== '', function (Builder $builder) use ($search) {
                $term = '%' . $search . '%';

                $builder->where(function (Builder $nested) use ($term) {
                    $nested
                        ->where('titre', 'like', $term)
                        ->orWhere('titre_fr', 'like', $term)
                        ->orWhere('titre_en', 'like', $term)
                        ->orWhere('organisation', 'like', $term)
                        ->orWhere('description', 'like', $term)
                        ->orWhere('description_fr', 'like', $term)
                        ->orWhere('description_en', 'like', $term);
                });
            })
            ->when($this->type !== '', fn (Builder $builder) => $builder->where('type', $this->type))
            ->when($this->pays !== '', fn (Builder $builder) => $builder->where('pays', $this->pays))
            ->when($this->contrat !== '', fn (Builder $builder) => $builder->where('contrat', $this->contrat))
            ->when($this->teletravail, fn (Builder $builder) => $builder->where('teletravail', true))
            ->when($this->urgent, fn (Builder $builder) => $builder->where('urgent', true));
    }
}
