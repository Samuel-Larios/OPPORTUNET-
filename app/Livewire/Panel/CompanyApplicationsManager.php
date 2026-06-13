<?php

namespace App\Livewire\Panel;

use App\Mail\AdminCompanyApplicationValidatedMail;
use App\Mail\OfferCandidateValidatedByCompanyMail;
use App\Models\ParametreSite;
use App\Models\CandidatureOffre;
use App\Models\User;
use App\Notifications\PlatformDatabaseNotification;
use App\Support\NotificationRecipients;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class CompanyApplicationsManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(as: 'application', except: null)]
    public ?int $selectedApplicationId = null;
    public string $companyNote = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function selectApplication(int $applicationId): void
    {
        $application = $this->companyApplicationsQuery()
            ->with(['opportunite', 'processedBy', 'companyValidatedBy'])
            ->findOrFail($applicationId);

        $this->selectedApplicationId = $application->id;
        $this->companyNote = (string) ($application->note_entreprise ?? '');
    }

    public function validateApplication(): void
    {
        $this->validate([
            'selectedApplicationId' => ['required', 'exists:candidatures_offres,id'],
            'companyNote' => ['nullable', 'string', 'max:4000'],
        ]);

        $application = $this->companyApplicationsQuery()
            ->with(['opportunite', 'user'])
            ->findOrFail($this->selectedApplicationId);

        abort_unless($application->statut === 'proposee_entreprise', 403);

        $application->update([
            'statut' => 'validee_entreprise',
            'note_entreprise' => $this->companyNote !== '' ? $this->companyNote : null,
            'validee_par_entreprise' => auth()->id(),
            'validee_entreprise_le' => now(),
            'email_traitement_envoye_le' => now(),
        ]);

        $application = $application->fresh(['opportunite.user']);

        if ($application->email) {
            Mail::to($application->email)->send(new OfferCandidateValidatedByCompanyMail($application));
        }

        $candidateRecipients = $this->candidateRecipients($application);

        if ($candidateRecipients->isNotEmpty()) {
            Notification::send($candidateRecipients, new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.company_validated.title'),
                'message' => __('admin.notifications.events.company_validated.message', [
                    'offer' => $application->opportunite->titre,
                ]),
                'action_url' => route('panel.user.applications', ['application' => $application->id]),
                'action_label' => __('admin.notifications.open'),
                'category' => 'application',
                'level' => 'success',
                'resource_type' => 'offer_application',
                'resource_id' => $application->id,
            ]));
        }

        $adminRecipients = ParametreSite::configuredEmailRecipients()->merge(
            User::query()
                ->whereHas('role', fn ($query) => $query->whereIn('nom', ['super_admin', 'admin']))
                ->pluck('email')
        )
            ->filter(fn ($email) => is_string($email) && $email !== '')
            ->unique()
            ->values();

        foreach ($adminRecipients as $recipient) {
            rescue(fn () => Mail::to($recipient)->send(new AdminCompanyApplicationValidatedMail($application)));
        }

        Notification::send(
            NotificationRecipients::admins(),
            new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.company_validated_admin.title'),
                'message' => __('admin.notifications.events.company_validated_admin.message', [
                    'name' => trim($application->prenom . ' ' . $application->nom),
                    'offer' => $application->opportunite->titre,
                ]),
                'action_url' => route('panel.admin.applications', ['application' => $application->id]),
                'action_label' => __('admin.notifications.open'),
                'category' => 'company',
                'level' => 'success',
                'resource_type' => 'offer_application',
                'resource_id' => $application->id,
            ])
        );

        session()->flash('panel_success', __('admin.flash.company_application_validated'));
    }

    public function render(): View
    {
        $search = trim($this->search);

        $applications = $this->companyApplicationsQuery()
            ->with(['opportunite', 'processedBy', 'companyValidatedBy'])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('prenom', 'like', $term)
                        ->orWhere('nom', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhereHas('opportunite', function ($offerQuery) use ($term) {
                            $offerQuery->where('titre', 'like', $term)
                                ->orWhere('titre_fr', 'like', $term);
                        });
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->latest()
            ->paginate(10);

        $selectedApplication = $this->selectedApplicationId
            ? $this->companyApplicationsQuery()
                ->with(['opportunite', 'processedBy', 'companyValidatedBy'])
                ->find($this->selectedApplicationId)
            : $applications->first();

        if ($selectedApplication && $this->selectedApplicationId === null) {
            $this->selectedApplicationId = $selectedApplication->id;
            $this->companyNote = (string) ($selectedApplication->note_entreprise ?? '');
        }

        return view('livewire.panel.company-applications-manager', [
            'applications' => $applications,
            'selectedApplication' => $selectedApplication,
        ]);
    }

    protected function companyApplicationsQuery()
    {
        return CandidatureOffre::query()
            ->whereHas('opportunite', fn ($query) => $query->where('user_id', auth()->id()))
            ->whereIn('statut', ['proposee_entreprise', 'validee_entreprise']);
    }

    protected function candidateRecipients(CandidatureOffre $application)
    {
        if (! $application->user_id && ! $application->email) {
            return collect();
        }

        return User::query()
            ->where(function ($nested) use ($application) {
                if ($application->user_id) {
                    $nested->orWhere('id', $application->user_id);
                }

                if ($application->email) {
                    $nested->orWhere('email', $application->email);
                }
            })
            ->get()
            ->unique('id')
            ->values();
    }
}
