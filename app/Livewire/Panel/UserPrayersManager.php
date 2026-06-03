<?php

namespace App\Livewire\Panel;

use App\Models\MurDePriere;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UserPrayersManager extends Component
{
    public ?int $selectedPrayerId = null;

    public function selectPrayer(int $prayerId): void
    {
        $this->selectedPrayerId = $prayerId;
    }

    public function render(): View
    {
        $prayers = MurDePriere::query()
            ->where('user_id', auth()->id())
            ->latest('updated_at')
            ->get();

        $selectedPrayer = $this->selectedPrayerId
            ? $prayers->firstWhere('id', $this->selectedPrayerId)
            : $prayers->first();

        if ($selectedPrayer && $this->selectedPrayerId === null) {
            $this->selectedPrayerId = $selectedPrayer->id;
        }

        return view('livewire.panel.user-prayers-manager', [
            'prayers' => $prayers,
            'selectedPrayer' => $selectedPrayer,
        ]);
    }
}
