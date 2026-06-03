<?php

namespace App\Livewire\Panel;

use App\Models\InscriptionFormation;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class UserTrainingRegistrationsManager extends Component
{
    use WithFileUploads;

    public ?int $selectedRegistrationId = null;
    public string $replyMessage = '';
    public ?TemporaryUploadedFile $replyAttachment = null;

    public function selectRegistration(int $registrationId): void
    {
        $this->selectedRegistrationId = $registrationId;
        $this->resetReplyForm();
    }

    public function sendReply(): void
    {
        $this->validate([
            'selectedRegistrationId' => ['required', 'exists:inscriptions_formations,id'],
            'replyMessage' => ['nullable', 'string', 'max:4000'],
            'replyAttachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
        ]);

        if ($this->replyMessage === '' && ! $this->replyAttachment) {
            $this->addError('replyMessage', __('admin.training_registrations.messages.validation.required'));

            return;
        }

        $registration = InscriptionFormation::query()
            ->visibleToUser(auth()->user())
            ->findOrFail($this->selectedRegistrationId);

        $attachmentPath = $this->replyAttachment?->store('formations/messages', 'public');

        $registration->messages()->create([
            'sender_id' => auth()->id(),
            'sender_role' => 'user',
            'message' => $this->replyMessage !== '' ? $this->replyMessage : null,
            'attachment_path' => $attachmentPath,
            'attachment_name' => $this->replyAttachment?->getClientOriginalName(),
            'attachment_mime' => $this->replyAttachment?->getMimeType(),
        ]);

        $registration->update([
            'traite_le' => now(),
        ]);

        session()->flash('panel_success', __('admin.flash.training_message_sent'));
        $this->resetReplyForm();
    }

    public function render(): View
    {
        $registrations = InscriptionFormation::query()
            ->with(['formation', 'messages.sender', 'processedBy'])
            ->visibleToUser(auth()->user())
            ->latest('updated_at')
            ->get();

        $selectedRegistration = $this->selectedRegistrationId
            ? $registrations->firstWhere('id', $this->selectedRegistrationId)
            : $registrations->first();

        if ($selectedRegistration && $this->selectedRegistrationId === null) {
            $this->selectedRegistrationId = $selectedRegistration->id;
        }

        return view('livewire.panel.user-training-registrations-manager', [
            'registrations' => $registrations,
            'selectedRegistration' => $selectedRegistration,
        ]);
    }

    protected function resetReplyForm(): void
    {
        $this->reset(['replyMessage', 'replyAttachment']);
        $this->resetValidation();
    }
}
