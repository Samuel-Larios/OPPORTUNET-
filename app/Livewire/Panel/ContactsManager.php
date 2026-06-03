<?php

namespace App\Livewire\Panel;

use App\Mail\ContactAdminReplyMail;
use App\Models\Contact;
use App\Models\ContactReply;
use App\Notifications\PlatformDatabaseNotification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ContactsManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(except: '')]
    public string $priorityFilter = '';

    #[Url(except: '')]
    public string $subjectFilter = '';

    public ?int $selectedContactId = null;
    public string $processingStatus = 'non_lu';
    public string $processingPriority = 'normale';
    public string $adminNotes = '';
    public string $reminderAt = '';
    public string $reminderNote = '';
    public string $replyMessage = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingPriorityFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSubjectFilter(): void
    {
        $this->resetPage();
    }

    public function selectContact(int $contactId): void
    {
        $contact = Contact::query()->with($this->contactRelations())->findOrFail($contactId);

        $this->selectedContactId = $contact->id;
        $this->processingStatus = (string) $contact->statut;
        $this->processingPriority = (string) $contact->priorite;
        $this->adminNotes = (string) ($contact->notes_admin ?? '');
        $this->reminderAt = $contact->rappel_le?->format('Y-m-d\TH:i') ?? '';
        $this->reminderNote = (string) ($contact->rappel_note ?? '');
        $this->replyMessage = '';
    }

    public function updateContact(): void
    {
        $validated = $this->validate([
            'selectedContactId' => ['required', 'exists:contacts,id'],
            'processingStatus' => ['required', 'in:non_lu,lu,en_cours,repondu,archive'],
            'processingPriority' => ['required', 'in:normale,urgente'],
            'adminNotes' => ['nullable', 'string', 'max:4000'],
            'reminderAt' => ['nullable', 'date'],
            'reminderNote' => ['nullable', 'string', 'max:200'],
        ]);

        $contact = Contact::query()->findOrFail($validated['selectedContactId']);

        $repliedAt = $contact->repondu_le;

        if ($validated['processingStatus'] === 'repondu') {
            $repliedAt = $contact->repondu_le ?? now();
        } elseif ($validated['processingStatus'] !== 'archive') {
            $repliedAt = null;
        }

        $contact->update([
            'statut' => $validated['processingStatus'],
            'priorite' => $validated['processingPriority'],
            'notes_admin' => $validated['adminNotes'] !== '' ? $validated['adminNotes'] : null,
            'rappel_le' => $validated['reminderAt'] !== '' ? $validated['reminderAt'] : null,
            'rappel_note' => $validated['reminderNote'] !== '' ? $validated['reminderNote'] : null,
            'traite_par' => auth()->id(),
            'repondu_le' => $repliedAt,
        ]);

        session()->flash('panel_success', __('admin.flash.contact_updated'));
    }

    public function sendReply(): void
    {
        if (! $this->repliesEnabled()) {
            $this->addError('replyMessage', __('admin.contacts.messages.migration_required'));

            return;
        }

        $validated = $this->validate([
            'selectedContactId' => ['required', 'exists:contacts,id'],
            'processingStatus' => ['required', 'in:non_lu,lu,en_cours,repondu,archive'],
            'processingPriority' => ['required', 'in:normale,urgente'],
            'adminNotes' => ['nullable', 'string', 'max:4000'],
            'reminderAt' => ['nullable', 'date'],
            'reminderNote' => ['nullable', 'string', 'max:200'],
            'replyMessage' => ['required', 'string', 'max:4000'],
        ]);

        $reply = null;

        DB::transaction(function () use ($validated, &$reply): void {
            $contact = Contact::query()->findOrFail($validated['selectedContactId']);

            $contact->update([
                'statut' => 'repondu',
                'priorite' => $validated['processingPriority'],
                'reponse_admin' => $validated['replyMessage'],
                'notes_admin' => $validated['adminNotes'] !== '' ? $validated['adminNotes'] : null,
                'rappel_le' => $validated['reminderAt'] !== '' ? $validated['reminderAt'] : null,
                'rappel_note' => $validated['reminderNote'] !== '' ? $validated['reminderNote'] : null,
                'traite_par' => auth()->id(),
                'repondu_le' => now(),
            ]);

            $reply = $contact->replies()->create([
                'user_id' => auth()->id(),
                'message' => $validated['replyMessage'],
                'sent_at' => now(),
            ]);

            Mail::to($contact->email)->send(
                new ContactAdminReplyMail(
                    $contact->fresh(['user', 'processedBy']),
                    $reply->fresh(['sender'])
                )
            );

            if ($contact->user) {
                $contact->user->notify(new PlatformDatabaseNotification([
                    'title' => __('admin.notifications.events.contact_reply.title'),
                    'message' => __('admin.notifications.events.contact_reply.message', [
                        'subject' => $contact->subjectLabel(),
                    ]),
                    'action_url' => route('panel.notifications'),
                    'action_label' => __('admin.notifications.open'),
                    'category' => 'contact',
                    'level' => 'success',
                    'resource_type' => 'contact',
                    'resource_id' => $contact->id,
                ]));
            }
        });

        session()->flash('panel_success', __('admin.flash.contact_reply_sent'));

        $this->replyMessage = '';
        $this->selectContact($validated['selectedContactId']);
    }

    public function deleteReply(int $replyId): void
    {
        if (! $this->repliesEnabled() || $this->selectedContactId === null) {
            return;
        }

        $reply = ContactReply::query()
            ->where('contact_id', $this->selectedContactId)
            ->findOrFail($replyId);

        $contact = Contact::query()->findOrFail($this->selectedContactId);
        $reply->delete();

        $latestReply = $contact->replies()->first();

        $contact->update([
            'reponse_admin' => $latestReply?->message,
            'repondu_le' => $latestReply?->sent_at,
            'statut' => $latestReply ? $contact->statut : ($contact->statut === 'repondu' ? 'en_cours' : $contact->statut),
        ]);

        session()->flash('panel_success', __('admin.flash.contact_reply_deleted'));
        $this->selectContact($this->selectedContactId);
    }

    public function render(): View
    {
        $search = trim($this->search);

        $contacts = Contact::query()
            ->with($this->contactRelations())
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('prenom', 'like', $term)
                        ->orWhere('nom', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('telephone', 'like', $term)
                        ->orWhere('whatsapp', 'like', $term)
                        ->orWhere('pays', 'like', $term)
                        ->orWhere('message', 'like', $term)
                        ->orWhere('sujet_personnalise', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->when($this->priorityFilter !== '', fn ($query) => $query->where('priorite', $this->priorityFilter))
            ->when($this->subjectFilter !== '', fn ($query) => $query->where('sujet', $this->subjectFilter))
            ->orderByRaw("CASE WHEN statut = 'non_lu' THEN 0 WHEN statut = 'en_cours' THEN 1 WHEN statut = 'lu' THEN 2 WHEN statut = 'repondu' THEN 3 ELSE 4 END")
            ->orderByRaw("CASE WHEN rappel_le IS NOT NULL AND rappel_le <= NOW() THEN 0 WHEN rappel_le IS NOT NULL THEN 1 ELSE 2 END")
            ->latest('updated_at')
            ->paginate(10);

        $selectedContact = $this->selectedContactId
            ? Contact::query()->with($this->contactRelations())->find($this->selectedContactId)
            : $contacts->first();

        if ($selectedContact && $this->selectedContactId === null) {
            $this->selectedContactId = $selectedContact->id;
            $this->processingStatus = (string) $selectedContact->statut;
            $this->processingPriority = (string) $selectedContact->priorite;
            $this->adminNotes = (string) ($selectedContact->notes_admin ?? '');
            $this->reminderAt = $selectedContact->rappel_le?->format('Y-m-d\TH:i') ?? '';
            $this->reminderNote = (string) ($selectedContact->rappel_note ?? '');
        }

        return view('livewire.panel.contacts-manager', [
            'contacts' => $contacts,
            'selectedContact' => $selectedContact,
            'replyTimeline' => $selectedContact && $this->repliesEnabled()
                ? $selectedContact->replies
                : collect(),
            'repliesEnabled' => $this->repliesEnabled(),
        ]);
    }

    protected function repliesEnabled(): bool
    {
        return Schema::hasTable('contact_replies');
    }

    protected function contactRelations(): array
    {
        $relations = ['user', 'processedBy'];

        if ($this->repliesEnabled()) {
            $relations[] = 'replies.sender';
        }

        return $relations;
    }
}
