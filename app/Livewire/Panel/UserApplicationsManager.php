<?php

namespace App\Livewire\Panel;

use App\Models\CandidatureOffre;
use App\Notifications\PlatformDatabaseNotification;
use App\Support\NotificationRecipients;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class UserApplicationsManager extends Component
{
    use WithFileUploads;

    #[Url(as: 'application', except: null)]
    public ?int $selectedApplicationId = null;
    public string $replyMessage = '';
    public ?TemporaryUploadedFile $replyAttachment = null;

    public function selectApplication(int $applicationId): void
    {
        $this->selectedApplicationId = $applicationId;
        $this->resetReplyForm();
    }

    public function sendReply(): void
    {
        $this->validate([
            'selectedApplicationId' => ['required', 'exists:candidatures_offres,id'],
            'replyMessage' => ['nullable', 'string', 'max:4000'],
            'replyAttachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
        ]);

        if (trim($this->replyMessage) === '' && ! $this->replyAttachment) {
            $this->addError('replyMessage', __('admin.application_messages.validation.required'));

            return;
        }

        $application = CandidatureOffre::query()
            ->visibleToUser(auth()->user())
            ->findOrFail($this->selectedApplicationId);

        $attachmentPath = $this->replyAttachment?->store('offer-applications/messages', 'local');

        $application->messages()->create([
            'sender_id' => auth()->id(),
            'sender_role' => 'user',
            'message' => trim($this->replyMessage) !== '' ? trim($this->replyMessage) : null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $this->replyAttachment?->getClientOriginalName(),
            'attachment_mime' => $this->replyAttachment?->getMimeType(),
        ]);

        $application->update([
            'statut' => $application->statut === 'informations_complementaires' ? 'en_revue' : $application->statut,
            'traite_le' => now(),
        ]);

        Notification::send(
            NotificationRecipients::admins(),
            new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.application_reply.title'),
                'message' => __('admin.notifications.events.application_reply.message', [
                    'name' => trim($application->prenom . ' ' . $application->nom),
                    'offer' => $application->opportunite?->titre ?? '-',
                ]),
                'action_url' => route('panel.admin.applications', ['application' => $application->id]),
                'action_label' => __('admin.notifications.open'),
                'category' => 'application',
                'level' => 'info',
                'resource_type' => 'offer_application',
                'resource_id' => $application->id,
            ])
        );

        session()->flash('panel_success', __('admin.flash.application_message_sent'));
        $this->resetReplyForm();
    }

    public function render(): View
    {
        $applications = CandidatureOffre::query()
            ->with(['messages.sender', 'opportunite', 'processedBy'])
            ->visibleToUser(auth()->user())
            ->latest('updated_at')
            ->get();

        $selectedApplication = $this->selectedApplicationId
            ? $applications->firstWhere('id', $this->selectedApplicationId)
            : $applications->first();

        if ($selectedApplication && $this->selectedApplicationId === null) {
            $this->selectedApplicationId = $selectedApplication->id;
        }

        return view('livewire.panel.user-applications-manager', [
            'applications' => $applications,
            'selectedApplication' => $selectedApplication,
        ]);
    }

    protected function resetReplyForm(): void
    {
        $this->reset(['replyAttachment', 'replyMessage']);
        $this->resetValidation();
    }
}
