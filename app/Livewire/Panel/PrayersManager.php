<?php

namespace App\Livewire\Panel;

use App\Models\MurDePriere;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PrayersManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(except: '')]
    public string $typeFilter = '';

    public ?int $selectedPrayerId = null;
    public string $prenom = '';
    public string $pays = '';
    public string $email = '';
    public string $sujet = '';
    public string $prayerType = 'priere';
    public string $processingStatus = 'en_attente';
    public bool $anonyme = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function selectPrayer(int $prayerId): void
    {
        $prayer = MurDePriere::query()->with('user')->findOrFail($prayerId);

        $this->selectedPrayerId = $prayer->id;
        $this->prenom = (string) $prayer->prenom;
        $this->pays = (string) ($prayer->pays ?? '');
        $this->email = (string) ($prayer->email ?? '');
        $this->sujet = (string) $prayer->sujet;
        $this->prayerType = (string) $prayer->type;
        $this->processingStatus = (string) $prayer->statut;
        $this->anonyme = (bool) $prayer->anonyme;
    }

    public function updatePrayer(): void
    {
        $validated = $this->validate([
            'selectedPrayerId' => ['required', 'exists:mur_de_prieres,id'],
            'prenom' => ['required', 'string', 'max:80'],
            'pays' => ['nullable', 'string', 'max:80'],
            'email' => ['nullable', 'email', 'max:191'],
            'sujet' => ['required', 'string', 'max:4000'],
            'prayerType' => ['required', 'in:priere,temoignage_reponse,encouragement,verset_partage'],
            'processingStatus' => ['required', 'in:en_attente,approuve,rejete'],
            'anonyme' => ['boolean'],
        ]);

        $prayer = MurDePriere::query()->findOrFail($validated['selectedPrayerId']);

        $prayer->update([
            'prenom' => $validated['prenom'],
            'pays' => $validated['pays'] !== '' ? $validated['pays'] : null,
            'email' => $validated['email'] !== '' ? $validated['email'] : null,
            'sujet' => $validated['sujet'],
            'type' => $validated['prayerType'],
            'statut' => $validated['processingStatus'],
            'anonyme' => $validated['anonyme'],
        ]);

        session()->flash('panel_success', __('admin.flash.prayer_updated'));
    }

    public function render(): View
    {
        $search = trim($this->search);

        $prayers = MurDePriere::query()
            ->with(['user', 'soutiens'])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('prenom', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('pays', 'like', $term)
                        ->orWhere('sujet', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->orderByRaw("CASE WHEN statut = 'en_attente' THEN 0 WHEN statut = 'approuve' THEN 1 ELSE 2 END")
            ->latest('updated_at')
            ->paginate(10);

        $selectedPrayer = $this->selectedPrayerId
            ? MurDePriere::query()->with(['user', 'soutiens'])->find($this->selectedPrayerId)
            : $prayers->first();

        if ($selectedPrayer && $this->selectedPrayerId === null) {
            $this->selectedPrayerId = $selectedPrayer->id;
            $this->prenom = (string) $selectedPrayer->prenom;
            $this->pays = (string) ($selectedPrayer->pays ?? '');
            $this->email = (string) ($selectedPrayer->email ?? '');
            $this->sujet = (string) $selectedPrayer->sujet;
            $this->prayerType = (string) $selectedPrayer->type;
            $this->processingStatus = (string) $selectedPrayer->statut;
            $this->anonyme = (bool) $selectedPrayer->anonyme;
        }

        return view('livewire.panel.prayers-manager', [
            'prayers' => $prayers,
            'selectedPrayer' => $selectedPrayer,
        ]);
    }
}
