<?php

namespace App\Livewire\Panel;

use App\Models\ParametreSite;
use App\Support\SecuritySettings;
use App\Support\SubmissionGuard;
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
        $this->resetValidation();

        foreach ($this->settings as $settingId => $values) {
            $frenchValue = (string) $values['valeur_fr'];
            $englishValue = (string) $values['valeur_en'];

            if (str_ends_with((string) $values['cle'], '_url')) {
                try {
                    $frenchValue = SubmissionGuard::normalizeExternalUrl($frenchValue) ?? '';
                    $englishValue = SubmissionGuard::normalizeExternalUrl($englishValue) ?? '';
                } catch (\Illuminate\Validation\ValidationException $exception) {
                    $this->addError('settings.' . $settingId . '.valeur_fr', $exception->errors()['url'][0] ?? __('security.validation.invalid_external_url'));

                    return;
                }
            }

            ParametreSite::query()
                ->whereKey($settingId)
                ->update([
                    'valeur' => $frenchValue,
                    'valeur_fr' => $frenchValue,
                    'valeur_en' => $englishValue,
                ]);
        }

        SecuritySettings::flush();

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
