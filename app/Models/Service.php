<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Service extends Model
{
    use Bilingual;

    protected $table = 'services';

    protected $fillable = [
        'categorie_id',
        'titre',
        'titre_fr',
        'titre_en',
        'slug',
        'description_courte',
        'description_courte_fr',
        'description_courte_en',
        'description_longue',
        'description_longue_fr',
        'description_longue_en',
        'icone',
        'image',
        'type',
        'prix',
        'devise',
        'duree',
        'duree_fr',
        'duree_en',
        'whatsapp_message',
        'whatsapp_message_fr',
        'whatsapp_message_en',
        'actif',
        'en_vedette',
        'ordre',
    ];

    protected array $bilingual = [
        'titre',
        'description_courte',
        'description_longue',
        'duree',
        'whatsapp_message',
    ];

    protected function casts(): array
    {
        return [
            'actif' => 'boolean',
            'en_vedette' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function demandesAccompagnement(): HasMany
    {
        return $this->hasMany(DemandeAccompagnement::class);
    }

    public function publicImageUrl(): ?string
    {
        if (! $this->image) {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $this->image)) {
            return $this->image;
        }

        if (str_starts_with($this->image, 'images/') || str_starts_with($this->image, 'storage/')) {
            return asset($this->image);
        }

        return asset('storage/' . ltrim(Str::replace('\\', '/', (string) $this->image), '/'));
    }
}
