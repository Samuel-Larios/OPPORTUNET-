<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Opportunite extends Model
{
    use Bilingual;
    use SoftDeletes;

    protected $table = 'opportunites';

    protected $fillable = [
        'categorie_id',
        'user_id',
        'titre',
        'titre_fr',
        'titre_en',
        'slug',
        'organisation',
        'logo_organisation',
        'type',
        'contrat',
        'lieu',
        'pays',
        'teletravail',
        'description',
        'description_fr',
        'description_en',
        'profil_recherche',
        'profil_recherche_fr',
        'profil_recherche_en',
        'avantages',
        'avantages_fr',
        'avantages_en',
        'lien_candidature',
        'email_candidature',
        'salaire_min',
        'salaire_max',
        'devise_salaire',
        'date_expiration',
        'date_publication',
        'statut',
        'valide_par',
        'valide_le',
        'notes_validation_admin',
        'en_vedette',
        'urgent',
        'vues',
        'candidatures',
        'source',
        'auto_publish',
        'scheduled_for',
        'published_at',
        'scheduled_status',
    ];

    protected array $bilingual = [
        'titre',
        'description',
        'profil_recherche',
        'avantages',
    ];

    protected function casts(): array
    {
        return [
            'teletravail' => 'boolean',
            'en_vedette' => 'boolean',
            'urgent' => 'boolean',
            'date_expiration' => 'date',
            'date_publication' => 'date',
            'valide_le' => 'datetime',
            'auto_publish' => 'boolean',
            'scheduled_for' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'valide_par');
    }

    public function candidatures(): HasMany
    {
        return $this->hasMany(CandidatureOffre::class, 'opportunite_id');
    }

    public function statusLabel(): string
    {
        return (string) __('admin.offers.statuses.' . $this->statut);
    }
}
