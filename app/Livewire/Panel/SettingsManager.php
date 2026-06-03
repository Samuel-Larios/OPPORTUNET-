<?php

namespace App\Livewire\Panel;

use App\Models\ParametreSite;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class SettingsManager extends Component
{
    public array $settings = [];

    public function mount(): void
    {
        $this->settings = ParametreSite::query()
            ->orderBy('groupe')
            ->orderBy('label')
            ->get()
            ->mapWithKeys(fn ($setting) => [
                $setting->id => [
                    'id' => $setting->id,
                    'cle' => $setting->cle,
                    'label' => $setting->label ?: $setting->cle,
                    'groupe' => $setting->groupe,
                    'type' => $setting->type,
                    'public' => (bool) $setting->public,
                    'valeur_fr' => (string) ($setting->getRawOriginal('valeur_fr') ?? $setting->valeur),
                    'valeur_en' => (string) ($setting->getRawOriginal('valeur_en') ?? $setting->valeur),
                ],
            ])
            ->toArray();
    }

    public function save(): void
    {
        foreach ($this->settings as $settingId => $values) {
            ParametreSite::query()
                ->whereKey($settingId)
                ->update([
                    'valeur' => $values['valeur_fr'],
                    'valeur_fr' => $values['valeur_fr'],
                    'valeur_en' => $values['valeur_en'],
                ]);
        }

        session()->flash('panel_success', __('admin.flash.settings_saved'));
    }

    public function render(): View
    {
        $groupedSettings = collect($this->settings)->groupBy('groupe');

        return view('livewire.panel.settings-manager', [
            'groupedSettings' => $groupedSettings,
        ]);
    }
}
