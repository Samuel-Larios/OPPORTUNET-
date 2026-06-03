<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Temoignage extends Model
{
    use Bilingual;

    protected $table = 'temoignages';

    protected $fillable = [
        'user_id',
        'prenom',
        'nom',
        'email',
        'photo',
        'pays',
        'profession',
        'contenu',
        'contenu_fr',
        'contenu_en',
        'type',
        'note',
        'statut',
        'en_vedette',
        'ordre',
    ];

    protected array $bilingual = [
        'contenu',
    ];

    protected function casts(): array
    {
        return [
            'en_vedette' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function statusLabel(): string
    {
        return (string) __('admin.testimonials.statuses.' . $this->statut);
    }

    public function typeLabel(): string
    {
        return (string) __('admin.testimonials.types.' . $this->type);
    }
}
