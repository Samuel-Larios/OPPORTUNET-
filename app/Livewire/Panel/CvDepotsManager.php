<?php

namespace App\Livewire\Panel;

use App\Models\CvDepot;
use App\Support\SubmissionGuard;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class CvDepotsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(as: 'cv', except: null)]
    public ?int $selectedCvDepotId = null;
    public string $processingStatus = 'nouveau';
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

    public function selectCvDepot(int $cvDepotId): void
    {
        $cvDepot = CvDepot::query()
            ->with(['messages.sender', 'user', 'processedBy'])
            ->findOrFail($cvDepotId);

        $this->selectedCvDepotId = $cvDepot->id;
        $this->processingStatus = $cvDepot->statut;
        $this->adminNotes = (string) ($cvDepot->notes_admin ?? '');
        $this->resetReplyFields();
    }

    public function updateCvDepot(): void
    {
        $this->validate([
            'selectedCvDepotId' => ['required', 'exists:cv_depots,id'],
            'processingStatus' => ['required', 'in:nouveau,en_traitement,traite,archive'],
            'adminNotes' => ['nullable', 'string', 'max:4000'],
            'replyMessage' => ['nullable', 'string', 'max:4000'],
            'replyAttachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
        ]);

        $cvDepot = CvDepot::query()->findOrFail($this->selectedCvDepotId);

        SubmissionGuard::ensureSafePayload([
            'adminNotes' => $this->adminNotes,
            'replyMessage' => $this->replyMessage,
        ], [
            'adminNotes',
            'replyMessage',
        ]);

        $cvDepot->update([
            'statut' => $this->processingStatus,
            'notes_admin' => $this->adminNotes !== '' ? $this->adminNotes : null,
            'traite_par' => auth()->id(),
            'traite_le' => now(),
        ]);

        if ($this->replyMessage !== '' || $this->replyAttachment) {
            $attachmentPath = $this->replyAttachment?->store('cv-depots/messages', 'local');

            $cvDepot->messages()->create([
                'sender_id' => auth()->id(),
                'sender_role' => 'admin',
                'message' => $this->replyMessage !== '' ? $this->replyMessage : null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $this->replyAttachment?->getClientOriginalName(),
                'attachment_mime' => $this->replyAttachment?->getMimeType(),
            ]);
        }

        session()->flash('panel_success', __('admin.flash.cv_depot_updated'));
        $this->resetReplyFields();
    }

    public function render(): View
    {
        $search = trim($this->search);

        $cvDepots = CvDepot::query()
            ->with(['messages', 'processedBy'])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('prenom', 'like', $term)
                        ->orWhere('nom', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('titre_poste', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->latest('updated_at')
            ->paginate(10);

        $selectedCvDepot = $this->selectedCvDepotId
            ? CvDepot::query()
                ->with(['messages.sender', 'user', 'processedBy'])
                ->find($this->selectedCvDepotId)
            : $cvDepots->first();

        if ($selectedCvDepot && $this->selectedCvDepotId === null) {
            $this->selectedCvDepotId = $selectedCvDepot->id;
            $this->processingStatus = $selectedCvDepot->statut;
            $this->adminNotes = (string) ($selectedCvDepot->notes_admin ?? '');
        }

        return view('livewire.panel.cv-depots-manager', [
            'cvDepots' => $cvDepots,
            'selectedCvDepot' => $selectedCvDepot,
        ]);
    }

    protected function resetReplyFields(): void
    {
        $this->reset(['replyMessage', 'replyAttachment']);
        $this->resetValidation();
    }
}
