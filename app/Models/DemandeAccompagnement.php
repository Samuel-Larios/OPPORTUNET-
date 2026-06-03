<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandeAccompagnement extends Model
{
    protected $table = 'demandes_accompagnement';

    protected $fillable = [
        'user_id',
        'service_id',
        'prenom',
        'nom',
        'email',
        'telephone',
        'whatsapp',
        'pays',
        'besoin',
        'objectif',
        'budget',
        'mode_contact_prefere',
        'disponibilite',
        'statut',
        'notes_admin',
        'montant_facture',
        'devise',
        'coach_assigne',
        'suivi_le',
    ];

    protected function casts(): array
    {
        return [
            'suivi_le' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function coach(): BelongsTo
    {
        return $this->belongsTo(User::class, 'coach_assigne');
    }
}
