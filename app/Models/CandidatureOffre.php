<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidatureOffre extends Model
{
    protected $table = 'candidatures_offres';

    protected $fillable = [
        'user_id',
        'opportunite_id',
        'prenom',
        'nom',
        'email',
        'telephone',
        'whatsapp',
        'pays',
        'lettre_motivation',
        'diplome_fichiers',
        'attestation_fichiers',
        'message',
        'statut',
        'notes_admin',
        'traite_par',
        'traite_le',
        'proposee_entreprise_le',
        'validee_par_entreprise',
        'validee_entreprise_le',
        'note_entreprise',
        'email_traitement_envoye_le',
    ];

    protected function casts(): array
    {
        return [
            'diplome_fichiers' => 'array',
            'attestation_fichiers' => 'array',
            'traite_le' => 'datetime',
            'proposee_entreprise_le' => 'datetime',
            'validee_entreprise_le' => 'datetime',
            'email_traitement_envoye_le' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function opportunite(): BelongsTo
    {
        return $this->belongsTo(Opportunite::class, 'opportunite_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'traite_par');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(CandidatureOffreMessage::class, 'candidature_offre_id')->latest('created_at');
    }

    public function companyValidatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validee_par_entreprise');
    }

    public function scopeVisibleToUser(Builder $query, User $user): Builder
    {
        return $query->where(function (Builder $nested) use ($user) {
            $nested->where('user_id', $user->id);

            if ($user->email !== null && $user->email !== '') {
                $nested->orWhere('email', $user->email);
            }
        });
    }

    public function belongsToUser(User $user): bool
    {
        if ((int) $this->user_id === (int) $user->id) {
            return true;
        }

        return $this->email !== null
            && $user->email !== null
            && strcasecmp($this->email, $user->email) === 0;
    }

    public function statusLabel(): string
    {
        return (string) __('admin.applications.statuses.' . $this->statut);
    }
}
