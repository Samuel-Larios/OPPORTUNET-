<?php

namespace App\Livewire\Panel;

use App\Models\CvDepot;
use App\Notifications\PlatformDatabaseNotification;
use App\Support\NotificationRecipients;
use App\Support\SubmissionGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class UserCvDepotsManager extends Component
{
    use WithFileUploads;

    public ?int $selectedCvDepotId = null;
    public string $replyMessage = '';
    public ?TemporaryUploadedFile $replyAttachment = null;

    public function selectCvDepot(int $cvDepotId): void
    {
        $this->selectedCvDepotId = $cvDepotId;
        $this->resetReplyForm();
    }

    public function sendReply(): void
    {
        $this->validate([
            'selectedCvDepotId' => ['required', 'exists:cv_depots,id'],
            'replyMessage' => ['nullable', 'string', 'max:4000'],
            'replyAttachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
        ]);

        if ($this->replyMessage === '' && ! $this->replyAttachment) {
            $this->addError('replyMessage', __('admin.cv_depots.messages.validation.required'));

            return;
        }

        $cvDepot = CvDepot::query()
            ->where('user_id', auth()->id())
            ->findOrFail($this->selectedCvDepotId);

        SubmissionGuard::ensureSafePayload([
            'replyMessage' => $this->replyMessage,
        ], ['replyMessage']);

        $attachmentPath = $this->replyAttachment?->store('cv-depots/messages', 'local');

        $cvDepot->messages()->create([
            'sender_id' => auth()->id(),
            'sender_role' => 'user',
            'message' => $this->replyMessage !== '' ? $this->replyMessage : null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $this->replyAttachment?->getClientOriginalName(),
            'attachment_mime' => $this->replyAttachment?->getMimeType(),
        ]);

        Notification::send(
            NotificationRecipients::admins(),
            new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.cv_depot_reply.title'),
                'message' => __('admin.notifications.events.cv_depot_reply.message', [
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

        session()->flash('panel_success', __('admin.flash.cv_message_sent'));
        $this->resetReplyForm();
    }

    public function render(): View
    {
        $cvDepots = CvDepot::query()
            ->with(['messages.sender', 'processedBy'])
            ->where('user_id', auth()->id())
            ->latest('updated_at')
            ->get();

        $selectedCvDepot = $this->selectedCvDepotId
            ? $cvDepots->firstWhere('id', $this->selectedCvDepotId)
            : $cvDepots->first();

        if ($selectedCvDepot && $this->selectedCvDepotId === null) {
            $this->selectedCvDepotId = $selectedCvDepot->id;
        }

        return view('livewire.panel.user-cv-depots-manager', [
            'cvDepots' => $cvDepots,
            'selectedCvDepot' => $selectedCvDepot,
        ]);
    }

    protected function resetReplyForm(): void
    {
        $this->reset(['replyMessage', 'replyAttachment']);
        $this->resetValidation();
    }
}
