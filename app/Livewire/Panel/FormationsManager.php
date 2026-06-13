<?php

namespace App\Livewire\Panel;

use App\Models\Category;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class FormationsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(except: '')]
    public string $modeFilter = '';

    #[Url(except: '')]
    public string $categoryFilter = '';

    public ?int $editingFormationId = null;

    public string $categorieId = '';
    public string $formateurId = '';
    public string $titreFr = '';
    public string $titreEn = '';
    public string $slug = '';
    public string $descriptionCourteFr = '';
    public string $descriptionCourteEn = '';
    public string $descriptionLongueFr = '';
    public string $descriptionLongueEn = '';
    public string $mode = 'en_ligne';
    public string $lieuFr = '';
    public string $lieuEn = '';
    public string $lienEnLigne = '';
    public string $prix = '';
    public string $devise = 'XOF';
    public bool $gratuit = false;
    public string $dureeHeures = '';
    public string $nbSeances = '';
    public string $dateDebut = '';
    public string $dateFin = '';
    public string $heureDebut = '';
    public string $fuseauHoraire = 'Africa/Cotonou';
    public string $placesMax = '';
    public string $placesRestantes = '';
    public string $niveauFr = '';
    public string $niveauEn = '';
    public string $prerequisFr = '';
    public string $prerequisEn = '';
    public string $objectifsFr = '';
    public string $objectifsEn = '';
    public string $programmeFr = '';
    public string $programmeEn = '';
    public string $certificatFr = '';
    public string $certificatEn = '';
    public string $statut = 'brouillon';
    public bool $inscriptionsOuvertes = true;
    public bool $enVedette = false;
    public string $whatsappMessageFr = '';
    public string $whatsappMessageEn = '';
    public bool $scheduleEnabled = false;
    public string $scheduleAt = '';

    public ?TemporaryUploadedFile $imageCouverture = null;
    public string $existingImagePath = '';
    public string $currentImageUrl = '';
    public bool $removeCurrentImage = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingModeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTitreFr(string $value): void
    {
        $this->slug = $this->buildUniqueSlug($value);
    }

    public function updatedImageCouverture(): void
    {
        $this->removeCurrentImage = false;
    }

    public function poll(): void
    {
        $this->refreshScheduledPublications();
    }

    public function refreshScheduledPublications(): void
    {
        $now = now();

        Formation::query()
            ->where('auto_publish', true)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->each(function (Formation $formation) use ($now): void {
                $publishAt = $formation->scheduled_for instanceof Carbon ? $formation->scheduled_for : $now;

                $formation->forceFill([
                    'statut' => $formation->scheduled_status ?: 'ouverte',
                    'auto_publish' => false,
                    'scheduled_for' => null,
                    'scheduled_status' => null,
                    'published_at' => $formation->published_at ?: $publishAt,
                ])->save();
            });
    }

    public function editFormation(int $formationId): void
    {
        $formation = Formation::query()->findOrFail($formationId);

        $this->editingFormationId = $formation->id;
        $this->categorieId = (string) ($formation->categorie_id ?? '');
        $this->formateurId = (string) ($formation->formateur_id ?? '');
        $this->titreFr = (string) ($formation->getRawOriginal('titre_fr') ?? $formation->titre);
        $this->titreEn = (string) ($formation->getRawOriginal('titre_en') ?? $formation->titre);
        $this->slug = (string) $formation->slug;
        $this->descriptionCourteFr = (string) ($formation->getRawOriginal('description_courte_fr') ?? $formation->description_courte);
        $this->descriptionCourteEn = (string) ($formation->getRawOriginal('description_courte_en') ?? $formation->description_courte);
        $this->descriptionLongueFr = (string) ($formation->getRawOriginal('description_longue_fr') ?? $formation->description_longue ?? '');
        $this->descriptionLongueEn = (string) ($formation->getRawOriginal('description_longue_en') ?? $formation->description_longue ?? '');
        $this->mode = (string) $formation->mode;
        $this->lieuFr = (string) ($formation->getRawOriginal('lieu_fr') ?? $formation->lieu ?? '');
        $this->lieuEn = (string) ($formation->getRawOriginal('lieu_en') ?? $formation->lieu ?? '');
        $this->lienEnLigne = (string) ($formation->lien_en_ligne ?? '');
        $this->prix = $formation->prix !== null ? (string) $formation->prix : '';
        $this->devise = (string) ($formation->devise ?: 'XOF');
        $this->gratuit = (bool) $formation->gratuit;
        $this->dureeHeures = $formation->duree_heures !== null ? (string) $formation->duree_heures : '';
        $this->nbSeances = $formation->nb_seances !== null ? (string) $formation->nb_seances : '';
        $this->dateDebut = $formation->date_debut?->format('Y-m-d') ?? '';
        $this->dateFin = $formation->date_fin?->format('Y-m-d') ?? '';
        $this->heureDebut = (string) ($formation->heure_debut ?? '');
        $this->fuseauHoraire = (string) ($formation->fuseau_horaire ?: 'Africa/Cotonou');
        $this->placesMax = $formation->places_max !== null ? (string) $formation->places_max : '';
        $this->placesRestantes = $formation->places_restantes !== null ? (string) $formation->places_restantes : '';
        $this->niveauFr = (string) ($formation->getRawOriginal('niveau_fr') ?? $formation->niveau ?? '');
        $this->niveauEn = (string) ($formation->getRawOriginal('niveau_en') ?? $formation->niveau ?? '');
        $this->prerequisFr = (string) ($formation->getRawOriginal('prerequis_fr') ?? $formation->prerequis ?? '');
        $this->prerequisEn = (string) ($formation->getRawOriginal('prerequis_en') ?? $formation->prerequis ?? '');
        $this->objectifsFr = (string) ($formation->getRawOriginal('objectifs_fr') ?? $formation->objectifs ?? '');
        $this->objectifsEn = (string) ($formation->getRawOriginal('objectifs_en') ?? $formation->objectifs ?? '');
        $this->programmeFr = (string) ($formation->getRawOriginal('programme_fr') ?? $formation->programme ?? '');
        $this->programmeEn = (string) ($formation->getRawOriginal('programme_en') ?? $formation->programme ?? '');
        $this->certificatFr = (string) ($formation->getRawOriginal('certificat_fr') ?? $formation->certificat ?? '');
        $this->certificatEn = (string) ($formation->getRawOriginal('certificat_en') ?? $formation->certificat ?? '');
        $this->scheduleEnabled = (bool) $formation->auto_publish && $formation->scheduled_for?->isFuture();
        $this->scheduleAt = $this->scheduleEnabled ? $formation->scheduled_for?->format('Y-m-d\TH:i') ?? '' : '';
        $this->statut = $this->scheduleEnabled ? (string) ($formation->scheduled_status ?: 'ouverte') : (string) $formation->statut;
        $this->inscriptionsOuvertes = (bool) $formation->inscriptions_ouvertes;
        $this->enVedette = (bool) $formation->en_vedette;
        $this->whatsappMessageFr = (string) ($formation->getRawOriginal('whatsapp_message_fr') ?? $formation->whatsapp_message ?? '');
        $this->whatsappMessageEn = (string) ($formation->getRawOriginal('whatsapp_message_en') ?? $formation->whatsapp_message ?? '');
        $this->imageCouverture = null;
        $this->existingImagePath = (string) ($formation->image_couverture ?? '');
        $this->currentImageUrl = $formation->publicCoverUrl() ?? '';
        $this->removeCurrentImage = false;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingFormationId',
            'categorieId',
            'formateurId',
            'titreFr',
            'titreEn',
            'slug',
            'descriptionCourteFr',
            'descriptionCourteEn',
            'descriptionLongueFr',
            'descriptionLongueEn',
            'lieuFr',
            'lieuEn',
            'lienEnLigne',
            'prix',
            'dureeHeures',
            'nbSeances',
            'dateDebut',
            'dateFin',
            'heureDebut',
            'placesMax',
            'placesRestantes',
            'niveauFr',
            'niveauEn',
            'prerequisFr',
            'prerequisEn',
            'objectifsFr',
            'objectifsEn',
            'programmeFr',
            'programmeEn',
            'certificatFr',
            'certificatEn',
            'whatsappMessageFr',
            'whatsappMessageEn',
            'imageCouverture',
            'existingImagePath',
            'currentImageUrl',
            'scheduleAt',
        ]);

        $this->mode = 'en_ligne';
        $this->devise = 'XOF';
        $this->gratuit = false;
        $this->fuseauHoraire = 'Africa/Cotonou';
        $this->statut = 'brouillon';
        $this->inscriptionsOuvertes = true;
        $this->enVedette = false;
        $this->removeCurrentImage = false;
        $this->scheduleEnabled = false;
        $this->resetValidation();
    }

    public function removeImage(): void
    {
        $this->imageCouverture = null;
        $this->currentImageUrl = '';

        if ($this->existingImagePath !== '') {
            $this->removeCurrentImage = true;
        }
    }

    public function saveFormation(): void
    {
        $validated = $this->validate($this->rules());
        $scheduledFor = $validated['scheduleEnabled']
            ? Carbon::parse($validated['scheduleAt'])
            : null;
        $targetStatus = $scheduledFor ? 'ouverte' : $validated['statut'];

        $formation = $this->editingFormationId
            ? Formation::query()->findOrFail($this->editingFormationId)
            : new Formation();

        $coverPath = $formation->image_couverture;

        if ($this->removeCurrentImage && $coverPath) {
            $this->cleanupStoredImage($coverPath);
            $coverPath = null;
        }

        if ($this->imageCouverture) {
            if ($coverPath) {
                $this->cleanupStoredImage($coverPath);
            }

            $coverPath = $this->imageCouverture->store('formations', 'public');
        }

        $slug = $this->buildUniqueSlug($validated['titreFr']);
        $placesMax = $validated['placesMax'] !== '' ? (int) $validated['placesMax'] : null;
        $placesRestantes = $validated['placesRestantes'] !== ''
            ? (int) $validated['placesRestantes']
            : $placesMax;

        if ($placesMax !== null && $placesRestantes !== null && $placesRestantes > $placesMax) {
            $placesRestantes = $placesMax;
        }

        $formation->fill([
            'categorie_id' => $validated['categorieId'] !== '' ? (int) $validated['categorieId'] : null,
            'formateur_id' => $validated['formateurId'] !== '' ? (int) $validated['formateurId'] : null,
            'titre' => $validated['titreFr'],
            'titre_fr' => $validated['titreFr'],
            'titre_en' => $validated['titreEn'] !== '' ? $validated['titreEn'] : $validated['titreFr'],
            'slug' => $slug !== '' ? $slug : $this->buildUniqueSlug('formation'),
            'description_courte' => $validated['descriptionCourteFr'],
            'description_courte_fr' => $validated['descriptionCourteFr'],
            'description_courte_en' => $validated['descriptionCourteEn'] !== '' ? $validated['descriptionCourteEn'] : $validated['descriptionCourteFr'],
            'description_longue' => $validated['descriptionLongueFr'] ?: null,
            'description_longue_fr' => $validated['descriptionLongueFr'] ?: null,
            'description_longue_en' => $validated['descriptionLongueEn'] !== '' ? $validated['descriptionLongueEn'] : ($validated['descriptionLongueFr'] ?: null),
            'image_couverture' => $coverPath,
            'mode' => $validated['mode'],
            'lieu' => $validated['lieuFr'] ?: null,
            'lieu_fr' => $validated['lieuFr'] ?: null,
            'lieu_en' => $validated['lieuEn'] !== '' ? $validated['lieuEn'] : ($validated['lieuFr'] ?: null),
            'lien_en_ligne' => $validated['lienEnLigne'] ?: null,
            'prix' => $validated['gratuit'] ? null : ($validated['prix'] !== '' ? $validated['prix'] : null),
            'devise' => $validated['devise'],
            'gratuit' => $validated['gratuit'],
            'duree_heures' => $validated['dureeHeures'] !== '' ? (int) $validated['dureeHeures'] : null,
            'nb_seances' => $validated['nbSeances'] !== '' ? (int) $validated['nbSeances'] : null,
            'date_debut' => $validated['dateDebut'] !== '' ? $validated['dateDebut'] : null,
            'date_fin' => $validated['dateFin'] !== '' ? $validated['dateFin'] : null,
            'heure_debut' => $validated['heureDebut'] !== '' ? $validated['heureDebut'] : null,
            'fuseau_horaire' => $validated['fuseauHoraire'],
            'places_max' => $placesMax,
            'places_restantes' => $placesRestantes,
            'niveau' => $validated['niveauFr'] ?: null,
            'niveau_fr' => $validated['niveauFr'] ?: null,
            'niveau_en' => $validated['niveauEn'] !== '' ? $validated['niveauEn'] : ($validated['niveauFr'] ?: null),
            'prerequis' => $validated['prerequisFr'] ?: null,
            'prerequis_fr' => $validated['prerequisFr'] ?: null,
            'prerequis_en' => $validated['prerequisEn'] !== '' ? $validated['prerequisEn'] : ($validated['prerequisFr'] ?: null),
            'objectifs' => $validated['objectifsFr'] ?: null,
            'objectifs_fr' => $validated['objectifsFr'] ?: null,
            'objectifs_en' => $validated['objectifsEn'] !== '' ? $validated['objectifsEn'] : ($validated['objectifsFr'] ?: null),
            'programme' => $validated['programmeFr'] ?: null,
            'programme_fr' => $validated['programmeFr'] ?: null,
            'programme_en' => $validated['programmeEn'] !== '' ? $validated['programmeEn'] : ($validated['programmeFr'] ?: null),
            'certificat' => $validated['certificatFr'] ?: null,
            'certificat_fr' => $validated['certificatFr'] ?: null,
            'certificat_en' => $validated['certificatEn'] !== '' ? $validated['certificatEn'] : ($validated['certificatFr'] ?: null),
            'statut' => $scheduledFor ? 'brouillon' : $targetStatus,
            'inscriptions_ouvertes' => $validated['inscriptionsOuvertes'],
            'en_vedette' => $validated['enVedette'],
            'whatsapp_message' => $validated['whatsappMessageFr'] ?: null,
            'whatsapp_message_fr' => $validated['whatsappMessageFr'] ?: null,
            'whatsapp_message_en' => $validated['whatsappMessageEn'] !== '' ? $validated['whatsappMessageEn'] : ($validated['whatsappMessageFr'] ?: null),
            'auto_publish' => $scheduledFor !== null,
            'scheduled_for' => $scheduledFor,
            'scheduled_status' => $scheduledFor ? $targetStatus : null,
            'published_at' => $scheduledFor === null && $targetStatus === 'ouverte'
                ? ($formation->published_at ?? now())
                : null,
        ]);

        $formation->save();

        session()->flash('panel_success', __('admin.flash.training_saved'));
        $this->resetForm();
    }

    public function deleteFormation(int $formationId): void
    {
        $formation = Formation::query()->findOrFail($formationId);

        if ($formation->image_couverture) {
            $this->cleanupStoredImage($formation->image_couverture);
        }

        $formation->delete();

        if ($this->editingFormationId === $formationId) {
            $this->resetForm();
        }

        session()->flash('panel_success', __('admin.flash.training_deleted'));
    }

    public function render(): View
    {
        $search = trim($this->search);

        $formations = Formation::query()
            ->with(['category', 'formateur'])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('titre', 'like', $term)
                        ->orWhere('titre_fr', 'like', $term)
                        ->orWhere('titre_en', 'like', $term)
                        ->orWhere('description_courte', 'like', $term)
                        ->orWhere('description_courte_fr', 'like', $term)
                        ->orWhere('description_courte_en', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn($query) => $query->where('statut', $this->statusFilter))
            ->when($this->modeFilter !== '', fn($query) => $query->where('mode', $this->modeFilter))
            ->when($this->categoryFilter !== '', fn($query) => $query->where('categorie_id', (int) $this->categoryFilter))
            ->orderByDesc('en_vedette')
            ->orderBy('date_debut')
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.panel.formations-manager', [
            'formations' => $formations,
            'categories' => Category::query()->where('type', 'formation')->where('actif', true)->orderBy('ordre')->get(),
            'trainers' => User::query()->where('actif', true)->orderBy('prenom')->orderBy('nom')->get(),
            'trainingModes' => ['presentiel', 'en_ligne', 'hybride'],
            'trainingStatuses' => ['brouillon', 'ouverte', 'complete', 'terminee', 'annulee'],
        ]);
    }

    protected function rules(): array
    {
        return [
            'categorieId' => [
                'nullable',
                Rule::exists('categories', 'id')->where(fn($query) => $query->where('type', 'formation')),
            ],
            'formateurId' => ['nullable', 'exists:users,id'],
            'titreFr' => ['required', 'string', 'max:200'],
            'titreEn' => ['nullable', 'string', 'max:200'],
            'descriptionCourteFr' => ['required', 'string'],
            'descriptionCourteEn' => ['nullable', 'string'],
            'descriptionLongueFr' => ['nullable', 'string'],
            'descriptionLongueEn' => ['nullable', 'string'],
            'imageCouverture' => ['nullable', 'image', 'max:4096'],
            'mode' => ['required', Rule::in(['presentiel', 'en_ligne', 'hybride'])],
            'lieuFr' => ['nullable', 'string', 'max:200'],
            'lieuEn' => ['nullable', 'string', 'max:200'],
            'lienEnLigne' => ['nullable', 'url', 'max:500'],
            'prix' => ['nullable', 'numeric', 'min:0'],
            'devise' => ['required', 'string', 'max:10'],
            'gratuit' => ['boolean'],
            'dureeHeures' => ['nullable', 'integer', 'min:1'],
            'nbSeances' => ['nullable', 'integer', 'min:1'],
            'dateDebut' => ['nullable', 'date'],
            'dateFin' => ['nullable', 'date', 'after_or_equal:dateDebut'],
            'heureDebut' => ['nullable', 'date_format:H:i'],
            'fuseauHoraire' => ['required', 'string', 'max:50'],
            'placesMax' => ['nullable', 'integer', 'min:1'],
            'placesRestantes' => ['nullable', 'integer', 'min:0'],
            'niveauFr' => ['nullable', 'string', 'max:80'],
            'niveauEn' => ['nullable', 'string', 'max:80'],
            'prerequisFr' => ['nullable', 'string'],
            'prerequisEn' => ['nullable', 'string'],
            'objectifsFr' => ['nullable', 'string'],
            'objectifsEn' => ['nullable', 'string'],
            'programmeFr' => ['nullable', 'string'],
            'programmeEn' => ['nullable', 'string'],
            'certificatFr' => ['nullable', 'string', 'max:100'],
            'certificatEn' => ['nullable', 'string', 'max:100'],
            'statut' => ['required', Rule::in(['brouillon', 'ouverte', 'complete', 'terminee', 'annulee'])],
            'inscriptionsOuvertes' => ['boolean'],
            'enVedette' => ['boolean'],
            'whatsappMessageFr' => ['nullable', 'string', 'max:255'],
            'whatsappMessageEn' => ['nullable', 'string', 'max:255'],
            'scheduleEnabled' => ['boolean'],
            'scheduleAt' => ['nullable', 'date_format:Y-m-d\\TH:i', 'required_if:scheduleEnabled,true', 'after:now'],
        ];
    }

    protected function buildUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);

        if ($slug === '') {
            $slug = 'formation';
        }

        $original = $slug;
        $counter = 1;

        while (Formation::query()
            ->when($this->editingFormationId, fn($query) => $query->whereKeyNot($this->editingFormationId))
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function cleanupStoredImage(string $path): void
    {
        if ($path === '' || preg_match('/^https?:\/\//i', $path) || str_starts_with($path, 'images/')) {
            return;
        }

        $normalizedPath = str_starts_with($path, 'storage/')
            ? Str::after($path, 'storage/')
            : $path;

        Storage::disk('public')->delete($normalizedPath);
    }
}
