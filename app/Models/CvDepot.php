<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CvDepot extends Model
{
    use SoftDeletes;

    protected $table = 'cv_depots';

    protected $fillable = [
        'user_id',
        'prenom',
        'nom',
        'email',
        'telephone',
        'whatsapp',
        'pays',
        'ville',
        'date_naissance',
        'genre',
        'titre_poste',
        'niveau_etude',
        'domaine_etude',
        'competences',
        'langues',
        'annees_experience',
        'objectif_professionnel',
        'secteurs_interet',
        'type_contrat_recherche',
        'teletravail_souhaite',
        'cv_fichier',
        'linkedin_url',
        'portfolio_url',
        'message',
        'demande_redaction_cv',
        'demande_coaching',
        'demande_orientation',
        'statut',
        'notes_admin',
        'traite_par',
        'traite_le',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
            'teletravail_souhaite' => 'boolean',
            'demande_redaction_cv' => 'boolean',
            'demande_coaching' => 'boolean',
            'demande_orientation' => 'boolean',
            'traite_le' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CvDepotMessage::class)->latest('created_at');
    }

    public function statusLabel(): string
    {
        return (string) __('admin.cv_depots.statuses.' . $this->statut);
    }
}
