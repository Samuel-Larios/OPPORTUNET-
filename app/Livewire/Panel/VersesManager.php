<?php

namespace App\Livewire\Panel;

use App\Models\Verset;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class VersesManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $activeFilter = '';

    #[Url(except: '')]
    public string $homeFilter = '';

    public ?int $editingVerseId = null;

    public string $referenceFr = '';
    public string $referenceEn = '';
    public string $texteFr = '';
    public string $texteEn = '';
    public string $versionFr = 'LSG';
    public string $versionEn = 'LSG';
    public string $ordre = '0';
    public bool $actif = true;
    public bool $afficherAccueil = false;

    protected ?bool $referenceLocalizationAvailable = null;

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

    public function editVerse(int $verseId): void
    {
        $verse = Verset::query()->findOrFail($verseId);
        $hasReferenceLocalization = $this->hasReferenceLocalization();

        $this->editingVerseId = $verse->id;
        $this->referenceFr = (string) (($hasReferenceLocalization ? $verse->getRawOriginal('reference_fr') : null) ?? $verse->getRawOriginal('reference'));
        $this->referenceEn = (string) (($hasReferenceLocalization ? $verse->getRawOriginal('reference_en') : null) ?? $verse->getRawOriginal('reference'));
        $this->texteFr = (string) ($verse->getRawOriginal('texte_fr') ?? $verse->getRawOriginal('texte'));
        $this->texteEn = (string) ($verse->getRawOriginal('texte_en') ?? $verse->getRawOriginal('texte'));
        $this->versionFr = (string) (($verse->getRawOriginal('version_fr') ?? $verse->getRawOriginal('version')) ?: 'LSG');
        $this->versionEn = (string) (($verse->getRawOriginal('version_en') ?? $verse->getRawOriginal('version')) ?: 'LSG');
        $this->ordre = (string) ($verse->ordre ?? 0);
        $this->actif = (bool) $verse->actif;
        $this->afficherAccueil = (bool) $verse->afficher_accueil;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingVerseId',
            'referenceFr',
            'referenceEn',
            'texteFr',
            'texteEn',
            'ordre',
        ]);

        $this->versionFr = 'LSG';
        $this->versionEn = 'LSG';
        $this->actif = true;
        $this->afficherAccueil = false;
        $this->resetValidation();
    }

    public function saveVerse(): void
    {
        $validated = $this->validate([
            'referenceFr' => ['required', 'string'],
            'referenceEn' => ['nullable', 'string'],
            'texteFr' => ['required', 'string'],
            'texteEn' => ['nullable', 'string'],
            'versionFr' => ['required', 'string', 'max:50'],
            'versionEn' => ['nullable', 'string', 'max:50'],
            'ordre' => ['nullable', 'integer', 'min:0'],
            'actif' => ['boolean'],
            'afficherAccueil' => ['boolean'],
        ]);

        $payload = [
            'reference' => $validated['referenceFr'],
            'texte' => $validated['texteFr'],
            'texte_fr' => $validated['texteFr'],
            'texte_en' => $validated['texteEn'] !== '' ? $validated['texteEn'] : $validated['texteFr'],
            'version' => $validated['versionFr'],
            'version_fr' => $validated['versionFr'],
            'version_en' => $validated['versionEn'] !== '' ? $validated['versionEn'] : $validated['versionFr'],
            'ordre' => $validated['ordre'] !== '' ? (int) $validated['ordre'] : 0,
            'actif' => $validated['actif'],
            'afficher_accueil' => $validated['afficherAccueil'],
        ];

        if ($this->hasReferenceLocalization()) {
            $payload['reference_fr'] = $validated['referenceFr'];
            $payload['reference_en'] = $validated['referenceEn'] !== '' ? $validated['referenceEn'] : $validated['referenceFr'];
        }

        Verset::query()->updateOrCreate(
            ['id' => $this->editingVerseId],
            $payload
        );

        session()->flash('panel_success', __('admin.flash.verse_saved'));
        $this->resetForm();
    }

    public function deleteVerse(int $verseId): void
    {
        Verset::query()->findOrFail($verseId)->delete();

        if ($this->editingVerseId === $verseId) {
            $this->resetForm();
        }

        session()->flash('panel_success', __('admin.flash.verse_deleted'));
    }

    public function render(): View
    {
        $search = trim($this->search);
        $hasReferenceLocalization = $this->hasReferenceLocalization();

        $verses = Verset::query()
            ->when($search !== '', function ($query) use ($search, $hasReferenceLocalization) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term, $hasReferenceLocalization) {
                    $nested
                        ->where('reference', 'like', $term)
                        ->orWhere('texte', 'like', $term)
                        ->orWhere('texte_fr', 'like', $term)
                        ->orWhere('texte_en', 'like', $term)
                        ->orWhere('version', 'like', $term)
                        ->orWhere('version_fr', 'like', $term)
                        ->orWhere('version_en', 'like', $term);

                    if ($hasReferenceLocalization) {
                        $nested
                            ->orWhere('reference_fr', 'like', $term)
                            ->orWhere('reference_en', 'like', $term);
                    }
                });
            })
            ->when($this->activeFilter !== '', fn ($query) => $query->where('actif', $this->activeFilter === '1'))
            ->when($this->homeFilter !== '', fn ($query) => $query->where('afficher_accueil', $this->homeFilter === '1'))
            ->orderByDesc('afficher_accueil')
            ->orderByDesc('actif')
            ->orderBy('ordre')
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.panel.verses-manager', [
            'verses' => $verses,
        ]);
    }

    protected function hasReferenceLocalization(): bool
    {
        if ($this->referenceLocalizationAvailable === null) {
            $this->referenceLocalizationAvailable =
                Schema::hasColumn('versets', 'reference_fr')
                && Schema::hasColumn('versets', 'reference_en');
        }

        return $this->referenceLocalizationAvailable;
    }
}
