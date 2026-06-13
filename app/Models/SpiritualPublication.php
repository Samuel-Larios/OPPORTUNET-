<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SpiritualPublication extends Model
{
    use Bilingual;

    protected $table = 'spiritual_publications';

    protected $fillable = [
        'type',
        'titre',
        'titre_fr',
        'titre_en',
        'slug',
        'extrait',
        'extrait_fr',
        'extrait_en',
        'contenu',
        'contenu_fr',
        'contenu_en',
        'reference',
        'reference_fr',
        'reference_en',
        'auteur',
        'auteur_fr',
        'auteur_en',
        'actif',
        'afficher_accueil',
        'auto_publish',
        'scheduled_for',
        'published_at',
        'ordre',
    ];

    protected array $bilingual = [
        'titre',
        'extrait',
        'contenu',
        'reference',
        'auteur',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'afficher_accueil' => 'boolean',
            'auto_publish' => 'boolean',
            'scheduled_for' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('actif', true);
    }
}
