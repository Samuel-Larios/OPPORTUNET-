<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InscriptionFormation extends Model
{
    protected $table = 'inscriptions_formations';

    protected $fillable = [
        'formation_id',
        'user_id',
        'prenom',
        'nom',
        'email',
        'telephone',
        'whatsapp',
        'pays',
        'profession',
        'niveau_etude',
        'motivation',
        'mode_paiement',
        'reference_paiement',
        'montant_paye',
        'statut_paiement',
        'statut',
        'est_suspendue',
        'suspendue_le',
        'motif_suspension',
        'certificat_delivre',
        'certificat_fichier',
        'confirme_le',
        'notes_admin',
        'traite_par',
        'traite_le',
    ];

    protected function casts(): array
    {
        return [
            'certificat_delivre' => 'boolean',
            'est_suspendue' => 'boolean',
            'confirme_le' => 'datetime',
            'suspendue_le' => 'datetime',
            'traite_le' => 'datetime',
        ];
    }

    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
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
        return $this->hasMany(FormationRegistrationMessage::class, 'inscription_formation_id');
    }

    public function statusLabel(): string
    {
        return (string) __('admin.training_registrations.statuses.' . $this->statut);
    }

    public function paymentStatusLabel(): string
    {
        return (string) __('admin.training_registrations.payment_statuses.' . $this->statut_paiement);
    }

    public function paymentModeLabel(): string
    {
        return (string) __('admin.training_registrations.payment_modes.' . $this->mode_paiement);
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
}
