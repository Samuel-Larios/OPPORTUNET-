<?php

namespace App\Livewire\Panel;

use App\Mail\CompanyProfileProposalMail;
use App\Mail\OfferApplicationProcessedMail;
use App\Models\CandidatureOffre;
use App\Notifications\PlatformDatabaseNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ApplicationsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(as: 'application', except: null)]
    public ?int $selectedApplicationId = null;
    public string $processingStatus = 'en_attente';
    public string $adminNotes = '';
    public string $replyMessage = '';
    public ?TemporaryUploadedFile $replyAttachment = null;

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
        $application = CandidatureOffre::query()
            ->with(['messages.sender', 'opportunite', 'user', 'processedBy'])
            ->findOrFail($applicationId);

        $this->selectedApplicationId = $application->id;
        $this->processingStatus = $application->statut;
        $this->adminNotes = (string) ($application->notes_admin ?? '');
        $this->resetReplyForm();
    }

    public function processApplication(): void
    {
        $this->validate([
            'selectedApplicationId' => ['required', 'exists:candidatures_offres,id'],
            'processingStatus' => ['required', 'in:en_attente,en_revue,retenue,proposee_entreprise,validee_entreprise,rejetee,informations_complementaires'],
            'adminNotes' => ['nullable', 'string', 'max:4000'],
            'replyMessage' => ['nullable', 'string', 'max:4000'],
            'replyAttachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
        ]);

        $application = CandidatureOffre::query()
            ->with(['messages.sender', 'opportunite', 'user'])
            ->findOrFail($this->selectedApplicationId);
        $previousNotes = trim((string) ($application->notes_admin ?? ''));
        $normalizedNotes = trim($this->adminNotes);
        $normalizedReply = trim($this->replyMessage);
        $attachmentPath = $this->replyAttachment?->store('offer-applications/messages', 'local');

        $application->update([
            'statut' => $this->processingStatus,
            'notes_admin' => $normalizedNotes !== '' ? $normalizedNotes : null,
            'traite_par' => auth()->id(),
            'traite_le' => now(),
            'proposee_entreprise_le' => $this->processingStatus === 'proposee_entreprise' ? now() : $application->proposee_entreprise_le,
            'email_traitement_envoye_le' => now(),
        ]);

        if ($normalizedNotes !== '' && $normalizedNotes !== $previousNotes) {
            $application->messages()->create([
                'sender_id' => auth()->id(),
                'sender_role' => 'admin',
                'message' => $normalizedNotes,
            ]);
        }

        if ($normalizedReply !== '' || $attachmentPath !== null) {
            $application->messages()->create([
                'sender_id' => auth()->id(),
                'sender_role' => 'admin',
                'message' => $normalizedReply !== '' ? $normalizedReply : null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $this->replyAttachment?->getClientOriginalName(),
                'attachment_mime' => $this->replyAttachment?->getMimeType(),
            ]);
        }

        $application = $application->fresh(['opportunite.user']);

        Mail::to($application->email)->send(new OfferApplicationProcessedMail($application));

        if ($application->user) {
            $application->user->notify(new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.application_status.title'),
                'message' => __('admin.notifications.events.application_status.message', [
                    'offer' => $application->opportunite->titre,
                    'status' => $application->statusLabel(),
                ]),
                'action_url' => route('panel.user.applications', ['application' => $application->id]),
                'action_label' => __('admin.notifications.open'),
                'category' => 'application',
                'level' => in_array($this->processingStatus, ['retenue', 'validee_entreprise'], true) ? 'success' : ($this->processingStatus === 'rejetee' ? 'danger' : 'info'),
                'resource_type' => 'offer_application',
                'resource_id' => $application->id,
            ]));
        }

        if (
            $this->processingStatus === 'proposee_entreprise'
            && $application->opportunite?->user?->email
        ) {
            Mail::to($application->opportunite->user->email)->send(new CompanyProfileProposalMail($application));
            $application->opportunite->user->notify(new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.candidate_proposed.title'),
                'message' => __('admin.notifications.events.candidate_proposed.message', [
                    'name' => trim($application->prenom . ' ' . $application->nom),
                    'offer' => $application->opportunite->titre,
                ]),
                'action_url' => route('panel.company.applications'),
                'action_label' => __('admin.notifications.open'),
                'category' => 'company',
                'level' => 'info',
                'resource_type' => 'offer_application',
                'resource_id' => $application->id,
            ]));
        }

        session()->flash('panel_success', __('admin.flash.application_processed'));
        $this->resetReplyForm();
    }

    public function render(): View
    {
        $search = trim($this->search);

        $applications = CandidatureOffre::query()
            ->with(['messages.sender', 'opportunite', 'processedBy'])
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
            ? CandidatureOffre::query()->with(['messages.sender', 'opportunite', 'user', 'processedBy'])->find($this->selectedApplicationId)
            : $applications->first();

        if ($selectedApplication && $this->selectedApplicationId === null) {
            $this->selectedApplicationId = $selectedApplication->id;
            $this->processingStatus = $selectedApplication->statut;
            $this->adminNotes = (string) ($selectedApplication->notes_admin ?? '');
        }

        return view('livewire.panel.applications-manager', [
            'applications' => $applications,
            'selectedApplication' => $selectedApplication,
        ]);
    }

    protected function resetReplyForm(): void
    {
        $this->reset(['replyMessage', 'replyAttachment']);
        $this->resetValidation(['replyMessage', 'replyAttachment']);
    }
}
