<?php

namespace App\Livewire;

use App\Models\CvDepot;
use App\Notifications\PlatformDatabaseNotification;
use App\Support\NotificationRecipients;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class CvDepotForm extends Component
{
    use WithFileUploads;

    public string $prenom = '';
    public string $nom = '';
    public string $email = '';
    public string $telephone = '';
    public string $whatsapp = '';
    public string $pays = '';
    public string $ville = '';
    public string $dateNaissance = '';
    public string $genre = '';
    public string $titrePoste = '';
    public string $niveauEtude = '';
    public string $domaineEtude = '';
    public string $competences = '';
    public string $langues = '';
    public string $anneesExperience = '';
    public string $objectifProfessionnel = '';
    public string $secteursInteret = '';
    public string $typeContratRecherche = 'tous';
    public bool $teletravailSouhaite = false;
    public string $linkedinUrl = '';
    public string $portfolioUrl = '';
    public string $message = '';
    public bool $demandeRedactionCv = false;
    public bool $demandeCoaching = false;
    public bool $demandeOrientation = false;

    public ?TemporaryUploadedFile $cvFichier = null;

    public function mount(): void
    {
        if (! auth()->check()) {
            return;
        }

        $user = auth()->user();

        $this->prenom = (string) ($user->prenom ?? '');
        $this->nom = (string) ($user->nom ?? '');
        $this->email = (string) ($user->email ?? '');
        $this->telephone = (string) ($user->telephone ?? '');
        $this->whatsapp = (string) ($user->whatsapp ?? $user->telephone ?? '');
        $this->pays = (string) ($user->pays ?? '');
        $this->ville = (string) ($user->ville ?? '');
        $this->dateNaissance = $user->date_naissance?->format('Y-m-d') ?? '';
        $this->genre = (string) ($user->genre ?? '');
        $this->niveauEtude = (string) ($user->niveau_etude ?? '');
        $this->linkedinUrl = '';
        $this->portfolioUrl = '';
    }

    public function submit(): void
    {
        abort_unless(auth()->check(), 403);

        $validated = $this->validate([
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:191'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'pays' => ['nullable', 'string', 'max:80'],
            'ville' => ['nullable', 'string', 'max:80'],
            'dateNaissance' => ['nullable', 'date'],
            'genre' => ['nullable', 'in:homme,femme,non_precise'],
            'titrePoste' => ['nullable', 'string', 'max:150'],
            'niveauEtude' => ['nullable', 'string', 'max:80'],
            'domaineEtude' => ['nullable', 'string', 'max:120'],
            'competences' => ['nullable', 'string', 'max:3000'],
            'langues' => ['nullable', 'string', 'max:2000'],
            'anneesExperience' => ['nullable', 'integer', 'min:0', 'max:60'],
            'objectifProfessionnel' => ['nullable', 'string', 'max:3000'],
            'secteursInteret' => ['nullable', 'string', 'max:2000'],
            'typeContratRecherche' => ['required', 'in:cdi,cdd,stage,freelance,tous'],
            'teletravailSouhaite' => ['boolean'],
            'linkedinUrl' => ['nullable', 'url', 'max:300'],
            'portfolioUrl' => ['nullable', 'url', 'max:300'],
            'message' => ['nullable', 'string', 'max:3000'],
            'demandeRedactionCv' => ['boolean'],
            'demandeCoaching' => ['boolean'],
            'demandeOrientation' => ['boolean'],
            'cvFichier' => ['required', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $cvPath = $this->cvFichier?->store('cv-depots', 'public');

        $cvDepot = CvDepot::query()->create([
            'user_id' => auth()->id(),
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?: null,
            'whatsapp' => $validated['whatsapp'] ?: null,
            'pays' => $validated['pays'] ?: null,
            'ville' => $validated['ville'] ?: null,
            'date_naissance' => $validated['dateNaissance'] ?: null,
            'genre' => $validated['genre'] ?: null,
            'titre_poste' => $validated['titrePoste'] ?: null,
            'niveau_etude' => $validated['niveauEtude'] ?: null,
            'domaine_etude' => $validated['domaineEtude'] ?: null,
            'competences' => $validated['competences'] ?: null,
            'langues' => $validated['langues'] ?: null,
            'annees_experience' => $validated['anneesExperience'] !== '' ? (int) $validated['anneesExperience'] : null,
            'objectif_professionnel' => $validated['objectifProfessionnel'] ?: null,
            'secteurs_interet' => $validated['secteursInteret'] ?: null,
            'type_contrat_recherche' => $validated['typeContratRecherche'],
            'teletravail_souhaite' => $validated['teletravailSouhaite'],
            'cv_fichier' => $cvPath,
            'linkedin_url' => $validated['linkedinUrl'] ?: null,
            'portfolio_url' => $validated['portfolioUrl'] ?: null,
            'message' => $validated['message'] ?: null,
            'demande_redaction_cv' => $validated['demandeRedactionCv'],
            'demande_coaching' => $validated['demandeCoaching'],
            'demande_orientation' => $validated['demandeOrientation'],
            'statut' => 'nouveau',
        ]);

        if ($validated['message'] !== '') {
            $cvDepot->messages()->create([
                'sender_id' => auth()->id(),
                'sender_role' => 'user',
                'message' => $validated['message'],
            ]);
        }

        Notification::send(
            NotificationRecipients::admins(),
            new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.cv_depot_received.title'),
                'message' => __('admin.notifications.events.cv_depot_received.message', [
                    'name' => trim($cvDepot->prenom . ' ' . $cvDepot->nom),
                ]),
                'action_url' => route('panel.admin.cv-depots', ['cv' => $cvDepot->id]),
                'action_label' => __('admin.notifications.open'),
                'category' => 'application',
                'level' => 'info',
                'resource_type' => 'cv_depot',
                'resource_id' => $cvDepot->id,
            ])
        );

        $this->resetRequestFields();
        session()->flash('cv_success', __('cv_services.form.success'));
        $this->redirect(route('cv.services.index') . '#cv-form', navigate: false);
    }

    public function render(): View
    {
        return view('livewire.cv-depot-form');
    }

    protected function resetRequestFields(): void
    {
        $this->reset([
            'ville',
            'dateNaissance',
            'genre',
            'titrePoste',
            'niveauEtude',
            'domaineEtude',
            'competences',
            'langues',
            'anneesExperience',
            'objectifProfessionnel',
            'secteursInteret',
            'typeContratRecherche',
            'teletravailSouhaite',
            'linkedinUrl',
            'portfolioUrl',
            'message',
            'demandeRedactionCv',
            'demandeCoaching',
            'demandeOrientation',
            'cvFichier',
        ]);

        $this->typeContratRecherche = 'tous';
        $this->teletravailSouhaite = false;
    }
}
