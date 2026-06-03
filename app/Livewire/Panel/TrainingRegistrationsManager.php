<?php

namespace App\Livewire\Panel;

use App\Models\Formation;
use App\Models\InscriptionFormation;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class TrainingRegistrationsManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(except: '')]
    public string $paymentFilter = '';

    #[Url(except: '')]
    public string $formationFilter = '';

    public ?int $selectedRegistrationId = null;
    public string $processingStatus = 'en_attente';
    public string $paymentStatus = 'en_attente';
    public string $paymentMode = 'en_attente';
    public string $paymentReference = '';
    public string $amountPaid = '';
    public bool $isSuspended = false;
    public string $suspensionReason = '';
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

    public function updatingPaymentFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFormationFilter(): void
    {
        $this->resetPage();
    }

    public function selectRegistration(int $registrationId): void
    {
        $registration = InscriptionFormation::query()
            ->with(['formation', 'messages.sender', 'processedBy', 'user'])
            ->findOrFail($registrationId);

        $this->selectedRegistrationId = $registration->id;
        $this->processingStatus = $registration->statut;
        $this->paymentStatus = $registration->statut_paiement;
        $this->paymentMode = $registration->mode_paiement;
        $this->paymentReference = (string) ($registration->reference_paiement ?? '');
        $this->amountPaid = $registration->montant_paye !== null ? (string) $registration->montant_paye : '';
        $this->isSuspended = (bool) $registration->est_suspendue;
        $this->suspensionReason = (string) ($registration->motif_suspension ?? '');
        $this->adminNotes = (string) ($registration->notes_admin ?? '');
        $this->resetReplyFields();
    }

    public function updateRegistration(): void
    {
        $this->validate([
            'selectedRegistrationId' => ['required', 'exists:inscriptions_formations,id'],
            'processingStatus' => ['required', 'in:en_attente,confirme,annule,liste_attente'],
            'paymentStatus' => ['required', 'in:non_paye,en_attente,paye,rembourse'],
            'paymentMode' => ['required', 'in:mobile_money,virement,especes,gratuit,en_attente'],
            'paymentReference' => ['nullable', 'string', 'max:100'],
            'amountPaid' => ['nullable', 'numeric', 'min:0'],
            'isSuspended' => ['boolean'],
            'suspensionReason' => ['nullable', 'string', 'max:4000'],
            'adminNotes' => ['nullable', 'string', 'max:4000'],
            'replyMessage' => ['nullable', 'string', 'max:4000'],
            'replyAttachment' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
        ]);

        $registration = InscriptionFormation::query()
            ->with('formation')
            ->findOrFail($this->selectedRegistrationId);

        $occupiedBefore = $registration->statut === 'confirme' && ! $registration->est_suspendue;
        $occupiedAfter = $this->processingStatus === 'confirme' && ! $this->isSuspended;

        if (! $occupiedBefore && $occupiedAfter && $registration->formation?->places_restantes !== null && $registration->formation->places_restantes <= 0) {
            $this->addError('processingStatus', __('admin.training_registrations.validation.no_places_left'));

            return;
        }

        $registration->update([
            'statut' => $this->processingStatus,
            'mode_paiement' => $this->paymentMode,
            'statut_paiement' => $this->paymentStatus,
            'reference_paiement' => $this->paymentReference !== '' ? $this->paymentReference : null,
            'montant_paye' => $this->amountPaid !== '' ? $this->amountPaid : null,
            'est_suspendue' => $this->isSuspended,
            'suspendue_le' => $this->isSuspended ? ($registration->suspendue_le ?? now()) : null,
            'motif_suspension' => $this->suspensionReason !== '' ? $this->suspensionReason : null,
            'notes_admin' => $this->adminNotes !== '' ? $this->adminNotes : null,
            'traite_par' => auth()->id(),
            'traite_le' => now(),
            'confirme_le' => $this->processingStatus === 'confirme' && ! $this->isSuspended
                ? ($registration->confirme_le ?? now())
                : null,
        ]);

        $formation = $registration->formation;

        if ($formation && $formation->places_restantes !== null) {
            if (! $occupiedBefore && $occupiedAfter) {
                $formation->update([
                    'places_restantes' => max(0, $formation->places_restantes - 1),
                ]);
            }

            if ($occupiedBefore && ! $occupiedAfter && $formation->places_max !== null) {
                $formation->update([
                    'places_restantes' => min($formation->places_max, $formation->places_restantes + 1),
                ]);
            }
        }

        if ($this->replyMessage !== '' || $this->replyAttachment) {
            $attachmentPath = $this->replyAttachment?->store('formations/messages', 'public');

            $registration->messages()->create([
                'sender_id' => auth()->id(),
                'sender_role' => 'admin',
                'message' => $this->replyMessage !== '' ? $this->replyMessage : null,
                'attachment_path' => $attachmentPath,
                'attachment_name' => $this->replyAttachment?->getClientOriginalName(),
                'attachment_mime' => $this->replyAttachment?->getMimeType(),
            ]);
        }

        session()->flash('panel_success', __('admin.flash.training_registration_updated'));
        $this->resetReplyFields();
    }

    public function render(): View
    {
        $search = trim($this->search);

        $registrations = InscriptionFormation::query()
            ->with(['formation', 'messages', 'processedBy'])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('prenom', 'like', $term)
                        ->orWhere('nom', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('profession', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->when($this->paymentFilter !== '', fn ($query) => $query->where('statut_paiement', $this->paymentFilter))
            ->when($this->formationFilter !== '', fn ($query) => $query->where('formation_id', (int) $this->formationFilter))
            ->latest('updated_at')
            ->paginate(10);

        $selectedRegistration = $this->selectedRegistrationId
            ? InscriptionFormation::query()
                ->with(['formation', 'messages.sender', 'processedBy', 'user'])
                ->find($this->selectedRegistrationId)
            : $registrations->first();

        if ($selectedRegistration && $this->selectedRegistrationId === null) {
            $this->selectedRegistrationId = $selectedRegistration->id;
            $this->processingStatus = $selectedRegistration->statut;
            $this->paymentStatus = $selectedRegistration->statut_paiement;
            $this->paymentMode = $selectedRegistration->mode_paiement;
            $this->paymentReference = (string) ($selectedRegistration->reference_paiement ?? '');
            $this->amountPaid = $selectedRegistration->montant_paye !== null ? (string) $selectedRegistration->montant_paye : '';
            $this->isSuspended = (bool) $selectedRegistration->est_suspendue;
            $this->suspensionReason = (string) ($selectedRegistration->motif_suspension ?? '');
            $this->adminNotes = (string) ($selectedRegistration->notes_admin ?? '');
        }

        return view('livewire.panel.training-registrations-manager', [
            'registrations' => $registrations,
            'selectedRegistration' => $selectedRegistration,
            'formations' => Formation::query()->whereIn('statut', ['ouverte', 'complete', 'terminee'])->orderBy('date_debut')->get(),
        ]);
    }

    protected function resetReplyFields(): void
    {
        $this->reset(['replyMessage', 'replyAttachment']);
        $this->resetValidation();
    }
}
