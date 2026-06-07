<?php

namespace App\Livewire\Panel;

use App\Models\Category;
use App\Models\Opportunite;
use App\Notifications\PlatformDatabaseNotification;
use App\Support\NotificationRecipients;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class EditorOffersManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(as: 'offer', except: null)]
    public ?int $editingOfferId = null;

    public string $categorieId = '';
    public string $titreFr = '';
    public string $titreEn = '';
    public string $organisation = '';
    public string $type = 'emploi';
    public string $contrat = 'cdd';
    public string $lieu = '';
    public string $pays = '';
    public bool $teletravail = false;
    public string $descriptionFr = '';
    public string $descriptionEn = '';
    public string $profilFr = '';
    public string $profilEn = '';
    public string $avantagesFr = '';
    public string $avantagesEn = '';
    public string $lienCandidature = '';
    public string $emailCandidature = '';
    public string $datePublication = '';
    public string $dateExpiration = '';
    public string $statut = 'brouillon';
    public bool $enVedette = false;
    public bool $urgent = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function editOffer(int $offerId): void
    {
        $offer = $this->offersQuery()->findOrFail($offerId);

        $this->editingOfferId = $offer->id;
        $this->categorieId = (string) ($offer->categorie_id ?? '');
        $this->titreFr = (string) ($offer->getRawOriginal('titre_fr') ?? $offer->titre);
        $this->titreEn = (string) ($offer->getRawOriginal('titre_en') ?? $offer->titre);
        $this->organisation = (string) ($offer->organisation ?? '');
        $this->type = (string) $offer->type;
        $this->contrat = (string) ($offer->contrat ?? 'cdd');
        $this->lieu = (string) ($offer->lieu ?? '');
        $this->pays = (string) ($offer->pays ?? '');
        $this->teletravail = (bool) $offer->teletravail;
        $this->descriptionFr = (string) ($offer->getRawOriginal('description_fr') ?? $offer->description);
        $this->descriptionEn = (string) ($offer->getRawOriginal('description_en') ?? $offer->description);
        $this->profilFr = (string) ($offer->getRawOriginal('profil_recherche_fr') ?? $offer->profil_recherche ?? '');
        $this->profilEn = (string) ($offer->getRawOriginal('profil_recherche_en') ?? $offer->profil_recherche ?? '');
        $this->avantagesFr = (string) ($offer->getRawOriginal('avantages_fr') ?? $offer->avantages ?? '');
        $this->avantagesEn = (string) ($offer->getRawOriginal('avantages_en') ?? $offer->avantages ?? '');
        $this->lienCandidature = (string) ($offer->lien_candidature ?? '');
        $this->emailCandidature = (string) ($offer->email_candidature ?? '');
        $this->datePublication = $offer->date_publication?->format('Y-m-d') ?? '';
        $this->dateExpiration = $offer->date_expiration?->format('Y-m-d') ?? '';
        $this->statut = $this->isCompanyUser() && $offer->statut === 'publie'
            ? 'en_attente_validation'
            : (string) $offer->statut;
        $this->enVedette = (bool) $offer->en_vedette;
        $this->urgent = (bool) $offer->urgent;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingOfferId',
            'categorieId',
            'titreFr',
            'titreEn',
            'organisation',
            'lieu',
            'pays',
            'descriptionFr',
            'descriptionEn',
            'profilFr',
            'profilEn',
            'avantagesFr',
            'avantagesEn',
            'lienCandidature',
            'emailCandidature',
            'datePublication',
            'dateExpiration',
        ]);

        $this->type = 'emploi';
        $this->contrat = 'cdd';
        $this->teletravail = false;
        $this->statut = 'brouillon';
        $this->enVedette = false;
        $this->urgent = false;
        $this->resetValidation();
    }

    public function saveOffer(): void
    {
        $isCompany = $this->isCompanyUser();
        $existingOffer = $this->editingOfferId
            ? $this->offersQuery()->findOrFail($this->editingOfferId)
            : null;

        $validated = $this->validate([
            'categorieId' => ['nullable', 'exists:categories,id'],
            'titreFr' => ['required', 'string', 'max:200'],
            'titreEn' => ['nullable', 'string', 'max:200'],
            'organisation' => ['nullable', 'string', 'max:150'],
            'type' => ['required', Rule::in(['emploi', 'stage', 'bourse', 'appel_offre', 'volontariat', 'formation_externe', 'autre'])],
            'contrat' => ['nullable', Rule::in(['cdi', 'cdd', 'stage', 'freelance', 'temps_partiel', 'bénévolat', 'non_applicable'])],
            'lieu' => ['nullable', 'string', 'max:150'],
            'pays' => ['nullable', 'string', 'max:80'],
            'teletravail' => ['boolean'],
            'descriptionFr' => ['required', 'string'],
            'descriptionEn' => ['nullable', 'string'],
            'profilFr' => ['nullable', 'string'],
            'profilEn' => ['nullable', 'string'],
            'avantagesFr' => ['nullable', 'string'],
            'avantagesEn' => ['nullable', 'string'],
            'lienCandidature' => ['nullable', 'url'],
            'emailCandidature' => ['nullable', 'email'],
            'datePublication' => ['nullable', 'date'],
            'dateExpiration' => ['nullable', 'date', 'after_or_equal:datePublication'],
            'statut' => ['required', Rule::in($this->allowedStatuses())],
            'enVedette' => ['boolean'],
            'urgent' => ['boolean'],
        ]);

        $slug = Str::slug($validated['titreFr']);
        $targetStatus = $validated['statut'];

        if ($isCompany && ($existingOffer?->statut === 'publie' || $targetStatus === 'publie')) {
            $targetStatus = 'en_attente_validation';
        }

        $offer = Opportunite::query()->updateOrCreate(
            ['id' => $this->editingOfferId],
            [
                'categorie_id' => $validated['categorieId'] !== '' ? (int) $validated['categorieId'] : null,
                'user_id' => $existingOffer?->user_id ?? auth()->id(),
                'titre' => $validated['titreFr'],
                'titre_fr' => $validated['titreFr'],
                'titre_en' => $validated['titreEn'] !== '' ? $validated['titreEn'] : $validated['titreFr'],
                'slug' => $this->editingOfferId ? Opportunite::query()->find($this->editingOfferId)?->slug ?? $slug : $this->uniqueSlug($slug),
                'organisation' => $validated['organisation'] ?: null,
                'type' => $validated['type'],
                'contrat' => $validated['contrat'] ?: null,
                'lieu' => $validated['lieu'] ?: null,
                'pays' => $validated['pays'] ?: null,
                'teletravail' => $validated['teletravail'],
                'description' => $validated['descriptionFr'],
                'description_fr' => $validated['descriptionFr'],
                'description_en' => $validated['descriptionEn'] !== '' ? $validated['descriptionEn'] : $validated['descriptionFr'],
                'profil_recherche' => $validated['profilFr'] ?: null,
                'profil_recherche_fr' => $validated['profilFr'] ?: null,
                'profil_recherche_en' => $validated['profilEn'] !== '' ? $validated['profilEn'] : ($validated['profilFr'] ?: null),
                'avantages' => $validated['avantagesFr'] ?: null,
                'avantages_fr' => $validated['avantagesFr'] ?: null,
                'avantages_en' => $validated['avantagesEn'] !== '' ? $validated['avantagesEn'] : ($validated['avantagesFr'] ?: null),
                'lien_candidature' => $validated['lienCandidature'] ?: null,
                'email_candidature' => $validated['emailCandidature'] ?: null,
                'date_publication' => $validated['datePublication']
                    ?: ($targetStatus === 'publie'
                        ? ($existingOffer?->date_publication?->toDateString() ?? now()->toDateString())
                        : ($existingOffer?->date_publication?->toDateString())),
                'date_expiration' => $validated['dateExpiration'] ?: null,
                'statut' => $targetStatus,
                'valide_par' => $targetStatus === 'publie' && ! $isCompany ? auth()->id() : null,
                'valide_le' => $targetStatus === 'publie' && ! $isCompany ? now() : null,
                'notes_validation_admin' => $existingOffer?->notes_validation_admin,
                'en_vedette' => ! $isCompany && $targetStatus === 'publie' ? $validated['enVedette'] : false,
                'urgent' => ! $isCompany && $targetStatus === 'publie' ? $validated['urgent'] : false,
            ]
        );

        $offer->loadMissing('user.role');

        if (
            $isCompany
            && $targetStatus === 'en_attente_validation'
            && ($existingOffer === null || $existingOffer->statut !== 'en_attente_validation')
        ) {
            Notification::send(
                NotificationRecipients::offerManagers(),
                new PlatformDatabaseNotification([
                    'title' => __('admin.notifications.events.offer_pending_validation.title'),
                    'message' => __('admin.notifications.events.offer_pending_validation.message', [
                        'offer' => $offer->titre,
                    ]),
                    'action_url' => route('panel.editor.offers', ['offer' => $offer->id]),
                    'action_label' => __('admin.notifications.open'),
                    'category' => 'offer',
                    'level' => 'warning',
                    'resource_type' => 'offer',
                    'resource_id' => $offer->id,
                ])
            );
        }

        if (
            ! $isCompany
            && $offer->user?->isCompany()
            && $existingOffer
            && $existingOffer->statut !== $targetStatus
        ) {
            $offer->user->notify(new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.offer_reviewed.title'),
                'message' => __('admin.notifications.events.offer_reviewed.message', [
                    'offer' => $offer->titre,
                    'status' => $offer->statusLabel(),
                ]),
                'action_url' => route('panel.company.offers', ['offer' => $offer->id]),
                'action_label' => __('admin.notifications.open'),
                'category' => 'offer',
                'level' => $targetStatus === 'publie' ? 'success' : ($targetStatus === 'rejete' ? 'danger' : 'info'),
                'resource_type' => 'offer',
                'resource_id' => $offer->id,
            ]));
        }

        if ($offer->wasRecentlyCreated === false && $offer->slug === '') {
            $offer->update(['slug' => $this->uniqueSlug($slug)]);
        }

        session()->flash('panel_success', __('admin.flash.offer_saved'));
        $this->resetForm();
    }

    public function deleteOffer(int $offerId): void
    {
        $this->offersQuery()->findOrFail($offerId)->delete();
    }

    public function render(): View
    {
        if ($this->editingOfferId !== null && $this->titreFr === '' && $this->descriptionFr === '') {
            $this->editOffer($this->editingOfferId);
        }

        $search = trim($this->search);

        $offers = Opportunite::query()
            ->when($this->isCompanyUser(), fn ($query) => $query->where('user_id', auth()->id()))
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('titre', 'like', $term)
                        ->orWhere('titre_fr', 'like', $term)
                        ->orWhere('organisation', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.panel.editor-offers-manager', [
            'offers' => $offers,
            'categories' => Category::query()->where('type', 'offre')->where('actif', true)->orderBy('ordre')->get(),
            'statusOptions' => $this->allowedStatuses(),
            'statusFilterOptions' => $this->statusFilterOptions(),
            'isCompanyUser' => $this->isCompanyUser(),
        ]);
    }

    protected function offersQuery()
    {
        return Opportunite::query()
            ->when($this->isCompanyUser(), fn ($query) => $query->where('user_id', auth()->id()));
    }

    protected function isCompanyUser(): bool
    {
        return auth()->user()?->isCompany() === true;
    }

    protected function allowedStatuses(): array
    {
        if ($this->isCompanyUser()) {
            return ['brouillon', 'en_attente_validation'];
        }

        return ['brouillon', 'en_attente_validation', 'publie', 'rejete', 'expire', 'archive'];
    }

    protected function statusFilterOptions(): array
    {
        return ['brouillon', 'en_attente_validation', 'publie', 'rejete', 'expire', 'archive'];
    }

    protected function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'offre';
        $original = $slug;
        $counter = 1;

        while (Opportunite::query()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
