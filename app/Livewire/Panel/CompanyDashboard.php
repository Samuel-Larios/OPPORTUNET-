<?php

namespace App\Livewire\Panel;

use App\Models\CandidatureOffre;
use App\Models\Opportunite;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CompanyDashboard extends Component
{
    public function render(): View
    {
        $userId = auth()->id();

        $offersQuery = Opportunite::query()->where('user_id', $userId);
        $applicationsQuery = CandidatureOffre::query()
            ->whereHas('opportunite', fn ($query) => $query->where('user_id', $userId));

        return view('livewire.panel.company-dashboard', [
            'stats' => [
                'offers' => (clone $offersQuery)->count(),
                'pendingOffers' => (clone $offersQuery)->where('statut', 'en_attente_validation')->count(),
                'publishedOffers' => (clone $offersQuery)->where('statut', 'publie')->count(),
                'proposedProfiles' => (clone $applicationsQuery)->where('statut', 'proposee_entreprise')->count(),
                'validatedProfiles' => (clone $applicationsQuery)->where('statut', 'validee_entreprise')->count(),
            ],
            'recentOffers' => (clone $offersQuery)->latest()->take(5)->get(),
            'recentApplications' => (clone $applicationsQuery)
                ->with(['opportunite'])
                ->whereIn('statut', ['proposee_entreprise', 'validee_entreprise'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
