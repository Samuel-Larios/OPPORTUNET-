<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'prenom',
        'nom',
        'name',
        'email',
        'telephone',
        'pays',
        'ville',
        'photo',
        'bio',
        'genre',
        'date_naissance',
        'profession',
        'niveau_etude',
        'whatsapp',
        'password',
        'actif',
        'newsletter',
        'langue',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_naissance' => 'date',
            'derniere_connexion' => 'datetime',
            'password' => 'hashed',
            'actif' => 'boolean',
            'newsletter' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function opportunites(): HasMany
    {
        return $this->hasMany(Opportunite::class);
    }

    public function candidaturesOffres(): HasMany
    {
        return $this->hasMany(CandidatureOffre::class);
    }

    public function candidatureOffreMessages(): HasMany
    {
        return $this->hasMany(CandidatureOffreMessage::class, 'sender_id');
    }

    public function demandesAccompagnement(): HasMany
    {
        return $this->hasMany(DemandeAccompagnement::class);
    }

    public function cvDepots(): HasMany
    {
        return $this->hasMany(CvDepot::class);
    }

    public function cvDepotMessages(): HasMany
    {
        return $this->hasMany(CvDepotMessage::class, 'sender_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function blogCommentaires(): HasMany
    {
        return $this->hasMany(BlogCommentaire::class);
    }

    public function inscriptionsFormations(): HasMany
    {
        return $this->hasMany(InscriptionFormation::class);
    }

    public function formationRegistrationMessages(): HasMany
    {
        return $this->hasMany(FormationRegistrationMessage::class, 'sender_id');
    }

    public function prieres(): HasMany
    {
        return $this->hasMany(MurDePriere::class);
    }

    public function prieresSoutiens(): HasMany
    {
        return $this->hasMany(PriereSoutien::class);
    }

    public function temoignages(): HasMany
    {
        return $this->hasMany(Temoignage::class);
    }

    public function fullName(): string
    {
        $name = trim(($this->prenom ?? '') . ' ' . ($this->nom ?? ''));

        return $name !== '' ? $name : (string) $this->name;
    }

    public function roleName(): string
    {
        return (string) ($this->role?->nom ?? 'user');
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->roleName(), $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('super_admin', 'admin');
    }

    public function isCompany(): bool
    {
        return $this->hasRole('entreprise');
    }

    public function canManageOffers(): bool
    {
        return $this->hasRole('super_admin', 'admin', 'editeur');
    }

    public function dashboardRouteName(): string
    {
        if ($this->isAdmin()) {
            return 'panel.admin.dashboard';
        }

        if ($this->isCompany()) {
            return 'panel.company.dashboard';
        }

        if ($this->canManageOffers()) {
            return 'panel.editor.offers';
        }

        return 'panel.user.dashboard';
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(
            (new VerifyEmailNotification())->locale($this->langue ?: app()->getLocale())
        );
    }
}
