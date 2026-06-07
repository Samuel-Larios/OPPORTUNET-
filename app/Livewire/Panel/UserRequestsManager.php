<?php

namespace App\Livewire\Panel;

use App\Models\Contact;
use App\Models\DemandeAccompagnement;
use App\Models\Service;
use App\Support\SubmissionGuard;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UserRequestsManager extends Component
{
    public string $serviceId = '';
    public string $telephone = '';
    public string $whatsapp = '';
    public string $pays = '';
    public string $besoin = '';
    public string $objectif = '';
    public string $budget = '';
    public string $modeContactPrefere = 'whatsapp';
    public string $disponibilite = 'flexible';

    public function mount(): void
    {
        $user = auth()->user();

        $this->telephone = (string) ($user->telephone ?? '');
        $this->whatsapp = (string) ($user->whatsapp ?? $user->telephone ?? '');
        $this->pays = (string) ($user->pays ?? '');
    }

    public function save(): void
    {
        $validated = $this->validate([
            'serviceId' => ['nullable', 'exists:services,id'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'pays' => ['nullable', 'string', 'max:80'],
            'besoin' => ['required', 'string', 'max:2000'],
            'objectif' => ['nullable', 'string', 'max:1000'],
            'budget' => ['nullable', 'string', 'max:100'],
            'modeContactPrefere' => ['required', 'in:whatsapp,email,telephone,presentiel'],
            'disponibilite' => ['required', 'in:matin,apres_midi,soir,flexible'],
        ]);

        SubmissionGuard::ensureSafePayload($validated, [
            'pays',
            'besoin',
            'objectif',
            'budget',
        ]);

        $user = auth()->user();

        DemandeAccompagnement::query()->create([
            'user_id' => $user->id,
            'service_id' => $validated['serviceId'] !== '' ? (int) $validated['serviceId'] : null,
            'prenom' => $user->prenom,
            'nom' => $user->nom,
            'email' => $user->email,
            'telephone' => $validated['telephone'] ?: null,
            'whatsapp' => $validated['whatsapp'] ?: null,
            'pays' => $validated['pays'] ?: null,
            'besoin' => $validated['besoin'],
            'objectif' => $validated['objectif'] ?: null,
            'budget' => $validated['budget'] ?: null,
            'mode_contact_prefere' => $validated['modeContactPrefere'],
            'disponibilite' => $validated['disponibilite'],
            'statut' => 'nouveau',
        ]);

        $this->reset(['serviceId', 'besoin', 'objectif', 'budget']);
        $this->modeContactPrefere = 'whatsapp';
        $this->disponibilite = 'flexible';

        session()->flash('panel_success', __('admin.flash.request_saved'));
    }

    public function render(): View
    {
        $user = auth()->user();

        return view('livewire.panel.user-requests-manager', [
            'services' => Service::query()->where('actif', true)->orderBy('ordre')->get(),
            'requests' => DemandeAccompagnement::query()->with('service')->where('user_id', $user->id)->latest()->get(),
            'contacts' => Contact::query()->where('user_id', $user->id)->latest()->take(6)->get(),
        ]);
    }
}
