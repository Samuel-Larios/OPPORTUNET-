<?php

namespace App\Livewire\Panel;

use App\Models\Temoignage;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TestimonialsManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(except: '')]
    public string $typeFilter = '';

    #[Url(except: '')]
    public string $featuredFilter = '';

    public ?int $selectedTestimonialId = null;
    public string $processingStatus = 'en_attente';
    public bool $enVedette = false;
    public string $ordre = '0';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingFeaturedFilter(): void
    {
        $this->resetPage();
    }

    public function selectTestimonial(int $testimonialId): void
    {
        $testimonial = Temoignage::query()->with('user')->findOrFail($testimonialId);

        $this->selectedTestimonialId = $testimonial->id;
        $this->processingStatus = $testimonial->statut;
        $this->enVedette = (bool) $testimonial->en_vedette;
        $this->ordre = (string) ($testimonial->ordre ?? 0);
    }

    public function updateTestimonial(): void
    {
        $validated = $this->validate([
            'selectedTestimonialId' => ['required', 'exists:temoignages,id'],
            'processingStatus' => ['required', 'in:en_attente,approuve,rejete'],
            'enVedette' => ['boolean'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ]);

        $testimonial = Temoignage::query()->findOrFail($validated['selectedTestimonialId']);

        $testimonial->update([
            'statut' => $validated['processingStatus'],
            'en_vedette' => $validated['enVedette'],
            'ordre' => $validated['ordre'] !== '' ? (int) $validated['ordre'] : 0,
        ]);

        session()->flash('panel_success', __('admin.flash.testimonial_updated'));
    }

    public function render(): View
    {
        $search = trim($this->search);

        $testimonials = Temoignage::query()
            ->with('user')
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('prenom', 'like', $term)
                        ->orWhere('nom', 'like', $term)
                        ->orWhere('email', 'like', $term)
                        ->orWhere('profession', 'like', $term)
                        ->orWhere('contenu', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->featuredFilter !== '', fn ($query) => $query->where('en_vedette', $this->featuredFilter === '1'))
            ->orderByRaw("CASE WHEN statut = 'en_attente' THEN 0 WHEN statut = 'approuve' THEN 1 ELSE 2 END")
            ->orderByDesc('en_vedette')
            ->orderBy('ordre')
            ->latest('updated_at')
            ->paginate(10);

        $selectedTestimonial = $this->selectedTestimonialId
            ? Temoignage::query()->with('user')->find($this->selectedTestimonialId)
            : $testimonials->first();

        if ($selectedTestimonial && $this->selectedTestimonialId === null) {
            $this->selectedTestimonialId = $selectedTestimonial->id;
            $this->processingStatus = $selectedTestimonial->statut;
            $this->enVedette = (bool) $selectedTestimonial->en_vedette;
            $this->ordre = (string) ($selectedTestimonial->ordre ?? 0);
        }

        return view('livewire.panel.testimonials-manager', [
            'testimonials' => $testimonials,
            'selectedTestimonial' => $selectedTestimonial,
        ]);
    }
}
