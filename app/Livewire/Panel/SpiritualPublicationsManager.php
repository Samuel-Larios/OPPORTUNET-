<?php

namespace App\Livewire\Panel;

use App\Models\SpiritualPublication;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SpiritualPublicationsManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $activeFilter = '';

    #[Url(except: '')]
    public string $homeFilter = '';

    public string $type = 'pensee';

    public ?int $editingPublicationId = null;

    public string $titleFr = '';
    public string $titleEn = '';
    public string $excerptFr = '';
    public string $excerptEn = '';
    public string $referenceFr = '';
    public string $referenceEn = '';
    public string $authorFr = '';
    public string $authorEn = '';
    public string $contentFr = '';
    public string $contentEn = '';
    public string $order = '0';
    public bool $active = true;
    public bool $showOnHome = false;
    public bool $scheduleEnabled = false;
    public string $scheduleAt = '';

    public function mount(string $type): void
    {
        abort_unless(in_array($type, $this->allowedTypes(), true), 404);

        $this->type = $type;
        $this->refreshScheduledPublications();
    }

    public function poll(): void
    {
        $this->refreshScheduledPublications();
    }

    public function refreshScheduledPublications(): void
    {
        $now = now();

        $this->query()
            ->where('auto_publish', true)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->each(function (SpiritualPublication $publication) use ($now): void {
                $publication->forceFill([
                    'actif' => true,
                    'auto_publish' => false,
                    'scheduled_for' => null,
                    'published_at' => $publication->published_at ?: $now,
                ])->save();
            });
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    public function updatingHomeFilter(): void
    {
        $this->resetPage();
    }

    public function editPublication(int $publicationId): void
    {
        $publication = $this->query()->findOrFail($publicationId);

        $this->editingPublicationId = $publication->id;
        $this->titleFr = (string) ($publication->getRawOriginal('titre_fr') ?? $publication->titre);
        $this->titleEn = (string) ($publication->getRawOriginal('titre_en') ?? $publication->titre);
        $this->excerptFr = (string) ($publication->getRawOriginal('extrait_fr') ?? $publication->extrait ?? '');
        $this->excerptEn = (string) ($publication->getRawOriginal('extrait_en') ?? $publication->extrait ?? '');
        $this->referenceFr = (string) ($publication->getRawOriginal('reference_fr') ?? $publication->reference ?? '');
        $this->referenceEn = (string) ($publication->getRawOriginal('reference_en') ?? $publication->reference ?? '');
        $this->authorFr = (string) ($publication->getRawOriginal('auteur_fr') ?? $publication->auteur ?? '');
        $this->authorEn = (string) ($publication->getRawOriginal('auteur_en') ?? $publication->auteur ?? '');
        $this->contentFr = (string) ($publication->getRawOriginal('contenu_fr') ?? $publication->contenu);
        $this->contentEn = (string) ($publication->getRawOriginal('contenu_en') ?? $publication->contenu);
        $this->order = (string) ($publication->ordre ?? 0);
        $this->scheduleEnabled = (bool) $publication->auto_publish && $publication->scheduled_for?->isFuture();
        $this->scheduleAt = $this->scheduleEnabled ? $publication->scheduled_for?->format('Y-m-d\TH:i') ?? '' : '';
        $this->active = $this->scheduleEnabled ? true : (bool) $publication->actif;
        $this->showOnHome = (bool) $publication->afficher_accueil;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingPublicationId',
            'titleFr',
            'titleEn',
            'excerptFr',
            'excerptEn',
            'referenceFr',
            'referenceEn',
            'authorFr',
            'authorEn',
            'contentFr',
            'contentEn',
            'order',
            'scheduleAt',
        ]);

        $this->active = true;
        $this->showOnHome = false;
        $this->scheduleEnabled = false;
        $this->resetValidation();
    }

    public function savePublication(): void
    {
        $existingPublication = $this->editingPublicationId
            ? $this->query()->findOrFail($this->editingPublicationId)
            : null;

        $validated = $this->validate([
            'titleFr' => ['required', 'string', 'max:180'],
            'titleEn' => ['nullable', 'string', 'max:180'],
            'excerptFr' => ['nullable', 'string'],
            'excerptEn' => ['nullable', 'string'],
            'referenceFr' => ['nullable', 'string', 'max:180'],
            'referenceEn' => ['nullable', 'string', 'max:180'],
            'authorFr' => ['nullable', 'string', 'max:180'],
            'authorEn' => ['nullable', 'string', 'max:180'],
            'contentFr' => ['required', 'string'],
            'contentEn' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'active' => ['boolean'],
            'showOnHome' => ['boolean'],
            'scheduleEnabled' => ['boolean'],
            'scheduleAt' => ['nullable', 'date_format:Y-m-d\\TH:i', 'required_if:scheduleEnabled,true', 'after:now'],
        ]);

        $scheduledFor = $validated['scheduleEnabled']
            ? Carbon::parse($validated['scheduleAt'])
            : null;
        $slug = $this->uniqueSlug(Str::slug($validated['titleFr']), $this->editingPublicationId);

        SpiritualPublication::query()->updateOrCreate(
            ['id' => $this->editingPublicationId],
            [
                'type' => $this->type,
                'titre' => $validated['titleFr'],
                'titre_fr' => $validated['titleFr'],
                'titre_en' => $validated['titleEn'] !== '' ? $validated['titleEn'] : $validated['titleFr'],
                'slug' => $slug !== '' ? $slug : $this->uniqueSlug('contenu-spirituel', $this->editingPublicationId),
                'extrait' => $validated['excerptFr'] ?: null,
                'extrait_fr' => $validated['excerptFr'] ?: null,
                'extrait_en' => $validated['excerptEn'] !== '' ? $validated['excerptEn'] : ($validated['excerptFr'] ?: null),
                'reference' => $validated['referenceFr'] ?: null,
                'reference_fr' => $validated['referenceFr'] ?: null,
                'reference_en' => $validated['referenceEn'] !== '' ? $validated['referenceEn'] : ($validated['referenceFr'] ?: null),
                'auteur' => $validated['authorFr'] ?: null,
                'auteur_fr' => $validated['authorFr'] ?: null,
                'auteur_en' => $validated['authorEn'] !== '' ? $validated['authorEn'] : ($validated['authorFr'] ?: null),
                'contenu' => $validated['contentFr'],
                'contenu_fr' => $validated['contentFr'],
                'contenu_en' => $validated['contentEn'] !== '' ? $validated['contentEn'] : $validated['contentFr'],
                'actif' => $scheduledFor ? false : $validated['active'],
                'afficher_accueil' => $validated['showOnHome'],
                'auto_publish' => $scheduledFor !== null,
                'scheduled_for' => $scheduledFor,
                'published_at' => $scheduledFor === null && $validated['active']
                    ? ($existingPublication?->published_at ?? now())
                    : ($existingPublication?->published_at),
                'ordre' => $validated['order'] !== '' ? (int) $validated['order'] : 0,
            ]
        );

        session()->flash('panel_success', app()->getLocale() === 'fr'
            ? 'Le contenu spirituel a bien ete enregistre.'
            : 'The spiritual content has been saved.');

        $this->resetForm();
    }

    public function deletePublication(int $publicationId): void
    {
        $this->query()->findOrFail($publicationId)->delete();

        if ($this->editingPublicationId === $publicationId) {
            $this->resetForm();
        }

        session()->flash('panel_success', app()->getLocale() === 'fr'
            ? 'Le contenu spirituel a bien ete supprime.'
            : 'The spiritual content has been deleted.');
    }

    public function render(): View
    {
        $search = trim($this->search);

        $publications = $this->query()
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('titre', 'like', $term)
                        ->orWhere('titre_fr', 'like', $term)
                        ->orWhere('titre_en', 'like', $term)
                        ->orWhere('extrait', 'like', $term)
                        ->orWhere('extrait_fr', 'like', $term)
                        ->orWhere('extrait_en', 'like', $term)
                        ->orWhere('reference', 'like', $term)
                        ->orWhere('reference_fr', 'like', $term)
                        ->orWhere('reference_en', 'like', $term);
                });
            })
            ->when($this->activeFilter !== '', fn($query) => $query->where('actif', $this->activeFilter === '1'))
            ->when($this->homeFilter !== '', fn($query) => $query->where('afficher_accueil', $this->homeFilter === '1'))
            ->orderByDesc('afficher_accueil')
            ->orderByDesc('actif')
            ->orderBy('ordre')
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.panel.spiritual-publications-manager', [
            'publications' => $publications,
            'typeLabel' => $this->typeLabel(),
            'langLabel' => app()->getLocale() === 'fr' ? 'FR' : 'EN',
        ]);
    }

    protected function query()
    {
        return SpiritualPublication::query()->where('type', $this->type);
    }

    protected function typeLabel(): string
    {
        return match ($this->type) {
            'pensee' => app()->getLocale() === 'fr' ? 'Pensee du jour' : 'Thought of the day',
            'exhortation' => app()->getLocale() === 'fr' ? 'Exhortation' : 'Exhortation',
            'priere_jour' => app()->getLocale() === 'fr' ? 'Priere du jour' : 'Prayer of the day',
            default => app()->getLocale() === 'fr' ? 'Contenu spirituel' : 'Spiritual content',
        };
    }

    /**
     * @return array<int, string>
     */
    protected function allowedTypes(): array
    {
        return ['pensee', 'exhortation', 'priere_jour'];
    }

    protected function uniqueSlug(string $baseSlug, ?int $ignoreId = null): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'contenu-spirituel';
        $original = $slug;
        $counter = 1;

        while (SpiritualPublication::query()
            ->when($ignoreId, fn($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
