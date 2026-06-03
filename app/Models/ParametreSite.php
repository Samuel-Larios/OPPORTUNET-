<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ParametreSite extends Model
{
    use Bilingual;

    protected $table = 'parametres_site';

    protected $fillable = [
        'cle',
        'valeur',
        'valeur_fr',
        'valeur_en',
        'type',
        'groupe',
        'label',
        'description',
        'public',
    ];

    protected array $bilingual = [
        'valeur',
    ];

    protected function casts(): array
    {
        return [
            'public' => 'boolean',
        ];
    }

    public static function configuredEmailRecipients(string $key = 'site_email'): Collection
    {
        $setting = static::query()->where('cle', $key)->first();

        if (! $setting) {
            return collect();
        }

        return collect([
            $setting->getRawOriginal('valeur_fr'),
            $setting->getRawOriginal('valeur_en'),
            $setting->valeur,
        ])->filter(fn ($value) => is_string($value) && $value !== '');
    }
}
