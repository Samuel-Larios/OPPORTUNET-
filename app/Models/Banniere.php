<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banniere extends Model
{
    use Bilingual;

    protected $table = 'bannieres';

    protected $fillable = [
        'titre',
        'titre_fr',
        'titre_en',
        'sous_titre',
        'sous_titre_fr',
        'sous_titre_en',
        'image',
        'image_mobile',
        'bouton1_texte',
        'bouton1_texte_fr',
        'bouton1_texte_en',
        'bouton1_lien',
        'bouton1_style',
        'bouton2_texte',
        'bouton2_texte_fr',
        'bouton2_texte_en',
        'bouton2_lien',
        'bouton2_style',
        'actif',
        'ordre',
    ];

    protected array $bilingual = [
        'titre',
        'sous_titre',
        'bouton1_texte',
        'bouton2_texte',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }
}
