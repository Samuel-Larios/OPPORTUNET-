<?php

namespace App\Livewire\Panel;

use App\Models\BlogArticle;
use App\Models\BlogCommentaire;
use App\Models\Contact;
use App\Models\CandidatureOffre;
use App\Models\DemandeAccompagnement;
use App\Models\CvDepot;
use App\Models\InscriptionFormation;
use App\Models\MurDePriere;
use App\Models\Opportunite;
use App\Models\Service;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class AdminDashboard extends Component
{
    public function render(): View
    {
        return view('livewire.panel.admin-dashboard', [
            'stats' => [
                'users' => User::query()->count(),
                'offers' => Opportunite::query()->count(),
                'articles' => BlogArticle::query()->count(),
                'article_comments' => BlogCommentaire::query()->where('statut', 'en_attente')->count(),
                'services' => Service::query()->count(),
                'cvs' => CvDepot::query()->count(),
                'requests' => DemandeAccompagnement::query()->count(),
                'applications' => CandidatureOffre::query()->count(),
                'contacts' => Contact::query()->where('statut', 'non_lu')->count(),
                'trainings' => InscriptionFormation::query()->count(),
                'prayers' => MurDePriere::query()->where('statut', 'en_attente')->count(),
            ],
            'recentUsers' => User::query()->with('role')->latest()->take(5)->get(),
            'recentRequests' => DemandeAccompagnement::query()->with(['service', 'user'])->latest()->take(5)->get(),
            'recentApplications' => CandidatureOffre::query()->with('opportunite')->latest()->take(5)->get(),
            'recentContacts' => Contact::query()->with('user')->latest()->take(5)->get(),
            'recentOffers' => Opportunite::query()->latest()->take(5)->get(),
            'recentArticles' => BlogArticle::query()->latest()->take(5)->get(),
            'recentArticleComments' => BlogCommentaire::query()->with('article')->latest()->take(5)->get(),
            'recentServices' => Service::query()->latest()->take(5)->get(),
            'recentCvs' => CvDepot::query()->latest()->take(5)->get(),
        ]);
    }
}
