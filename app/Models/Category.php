<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use Bilingual;

    protected $table = 'categories';

    protected $fillable = [
        'type',
        'nom',
        'nom_fr',
        'nom_en',
        'slug',
        'icone',
        'couleur',
        'description',
        'description_fr',
        'description_en',
        'actif',
        'ordre',
    ];

    protected array $bilingual = [
        'nom',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
        ];
    }
}
