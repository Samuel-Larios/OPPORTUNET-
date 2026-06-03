<?php

namespace App\Livewire\Panel;

use App\Models\Contact;
use App\Models\CvDepot;
use App\Models\DemandeAccompagnement;
use App\Models\InscriptionFormation;
use App\Models\MurDePriere;
use App\Models\Temoignage;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UserDashboard extends Component
{
    public function render(): View
    {
        $user = auth()->user();

        return view('livewire.panel.user-dashboard', [
            'stats' => [
                'requests' => DemandeAccompagnement::query()->where('user_id', $user->id)->count(),
                'contacts' => Contact::query()->where('user_id', $user->id)->count(),
                'cvs' => CvDepot::query()->where('user_id', $user->id)->count(),
                'trainings' => InscriptionFormation::query()->where('user_id', $user->id)->count(),
                'testimonials' => Temoignage::query()->where('user_id', $user->id)->count(),
                'prayers' => MurDePriere::query()->where('user_id', $user->id)->count(),
            ],
            'recentRequests' => DemandeAccompagnement::query()->with('service')->where('user_id', $user->id)->latest()->take(4)->get(),
            'recentContacts' => Contact::query()->where('user_id', $user->id)->latest()->take(4)->get(),
            'recentCvs' => CvDepot::query()->where('user_id', $user->id)->latest()->take(4)->get(),
            'recentTrainings' => InscriptionFormation::query()->with('formation')->where('user_id', $user->id)->latest()->take(4)->get(),
            'recentTestimonials' => Temoignage::query()->where('user_id', $user->id)->latest()->take(4)->get(),
            'recentPrayers' => MurDePriere::query()->where('user_id', $user->id)->latest()->take(4)->get(),
        ]);
    }
}
