<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Verset extends Model
{
    use Bilingual;

    protected $table = 'versets';

    protected $fillable = [
        'reference',
        'reference_fr',
        'reference_en',
        'texte',
        'texte_fr',
        'texte_en',
        'version',
        'version_fr',
        'version_en',
        'actif',
        'afficher_accueil',
        'ordre',
    ];

    protected array $bilingual = [
        'reference',
        'texte',
        'version',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'afficher_accueil' => 'boolean',
        ];
    }
}
