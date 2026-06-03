<?php

namespace App\Livewire\Panel;

use App\Models\Temoignage;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UserTestimonialsManager extends Component
{
    public ?int $selectedTestimonialId = null;

    public function selectTestimonial(int $testimonialId): void
    {
        $this->selectedTestimonialId = $testimonialId;
    }

    public function render(): View
    {
        $testimonials = Temoignage::query()
            ->where('user_id', auth()->id())
            ->latest('updated_at')
            ->get();

        $selectedTestimonial = $this->selectedTestimonialId
            ? $testimonials->firstWhere('id', $this->selectedTestimonialId)
            : $testimonials->first();

        if ($selectedTestimonial && $this->selectedTestimonialId === null) {
            $this->selectedTestimonialId = $selectedTestimonial->id;
        }

        return view('livewire.panel.user-testimonials-manager', [
            'testimonials' => $testimonials,
            'selectedTestimonial' => $selectedTestimonial,
        ]);
    }
}
