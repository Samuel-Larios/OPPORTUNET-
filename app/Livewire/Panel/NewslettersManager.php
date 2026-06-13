<?php

namespace App\Livewire\Panel;

use App\Models\Newsletter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class NewslettersManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    public ?int $editingNewsletterId = null;

    public string $subject = '';
    public string $contentType = '';
    public string $contentTitle = '';
    public string $audience = 'platform_users_and_subscribers';
    public string $status = 'draft';
    public bool $scheduleEnabled = false;
    public string $scheduleAt = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function editNewsletter(int $newsletterId): void
    {
        $newsletter = Newsletter::query()->findOrFail($newsletterId);

        $this->editingNewsletterId = $newsletter->id;
        $this->subject = (string) $newsletter->subject;
        $this->contentType = (string) ($newsletter->content_type ?? '');
        $this->contentTitle = (string) $newsletter->content_title;
        $this->audience = (string) $newsletter->audience;
        $this->status = (string) $newsletter->status;
        $this->scheduleEnabled = (bool) $newsletter->auto_publish && $newsletter->scheduled_for?->isFuture();
        $this->scheduleAt = $this->scheduleEnabled ? $newsletter->scheduled_for?->format('Y-m-d\TH:i') ?? '' : '';
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingNewsletterId',
            'subject',
            'contentType',
            'contentTitle',
            'audience',
            'scheduleAt',
        ]);

        $this->status = 'draft';
        $this->scheduleEnabled = false;
        $this->resetValidation();
    }

    public function saveNewsletter(): void
    {
        $validated = $this->validate([
            'subject' => ['required', 'string', 'max:255'],
            'contentType' => ['nullable', 'string', 'max:120'],
            'contentTitle' => ['required', 'string', 'max:255'],
            'audience' => ['required', 'string', 'max:80'],
            'status' => ['required', 'string', 'in:draft,scheduled,sent,failed'],
            'scheduleEnabled' => ['boolean'],
            'scheduleAt' => ['nullable', 'date_format:Y-m-d\TH:i', 'required_if:scheduleEnabled,true', 'after:now'],
        ]);

        $scheduledFor = $validated['scheduleEnabled']
            ? Carbon::parse($validated['scheduleAt'])
            : null;

        Newsletter::query()->updateOrCreate(
            ['id' => $this->editingNewsletterId],
            [
                'subject' => $validated['subject'],
                'content_type' => $validated['contentType'] ?: null,
                'content_title' => $validated['contentTitle'],
                'audience' => $validated['audience'],
                'status' => $scheduledFor ? 'scheduled' : $validated['status'],
                'auto_publish' => $scheduledFor !== null,
                'scheduled_for' => $scheduledFor,
                'published_at' => $scheduledFor === null && $validated['status'] === 'sent' ? now() : null,
            ]
        );

        session()->flash('panel_success', __('admin.flash.newsletter_saved'));
        $this->resetForm();
    }

    public function deleteNewsletter(int $newsletterId): void
    {
        Newsletter::query()->findOrFail($newsletterId)->delete();

        if ($this->editingNewsletterId === $newsletterId) {
            $this->resetForm();
        }

        session()->flash('panel_success', __('admin.flash.newsletter_deleted'));
    }

    public function poll(): void
    {
        $this->refreshScheduledPublications();
    }

    public function refreshScheduledPublications(): void
    {
        $now = now();

        Newsletter::query()
            ->where('auto_publish', true)
            ->whereNotNull('scheduled_for')
            ->where('scheduled_for', '<=', $now)
            ->each(function (Newsletter $newsletter) use ($now): void {
                $newsletter->forceFill([
                    'status' => 'sent',
                    'sent_at' => $newsletter->scheduled_for,
                    'auto_publish' => false,
                    'scheduled_for' => null,
                    'published_at' => $newsletter->published_at ?: $now,
                ])->save();
            });
    }

    public function render(): View
    {
        $search = trim($this->search);

        $newsletters = Newsletter::query()
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('subject', 'like', $term)
                        ->orWhere('content_title', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn($query) => $query->where('status', $this->statusFilter))
            ->orderByRaw("CASE WHEN status = 'draft' THEN 0 WHEN status = 'scheduled' THEN 1 WHEN status = 'sent' THEN 2 ELSE 3 END")
            ->latest('updated_at')
            ->paginate(10);

        $selectedNewsletter = $this->editingNewsletterId
            ? Newsletter::query()->find($this->editingNewsletterId)
            : $newsletters->first();

        if ($selectedNewsletter && $this->editingNewsletterId === null) {
            $this->editingNewsletterId = $selectedNewsletter->id;
            $this->subject = (string) $selectedNewsletter->subject;
            $this->contentType = (string) ($selectedNewsletter->content_type ?? '');
            $this->contentTitle = (string) $selectedNewsletter->content_title;
            $this->audience = (string) $selectedNewsletter->audience;
            $this->status = (string) $selectedNewsletter->status;
        }

        return view('livewire.panel.newsletters-manager', [
            'newsletters' => $newsletters,
            'selectedNewsletter' => $selectedNewsletter,
        ]);
    }
}
