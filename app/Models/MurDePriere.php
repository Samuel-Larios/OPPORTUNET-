<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MurDePriere extends Model
{
    protected $table = 'mur_de_prieres';

    protected $fillable = [
        'user_id',
        'prenom',
        'pays',
        'email',
        'sujet',
        'type',
        'anonyme',
        'priants',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'anonyme' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function soutiens(): HasMany
    {
        return $this->hasMany(PriereSoutien::class, 'priere_id');
    }

    public function statusLabel(): string
    {
        return (string) __('admin.prayers.statuses.' . $this->statut);
    }

    public function statusDescription(): string
    {
        return (string) __('admin.prayers.status_help.' . $this->statut);
    }

    public function typeLabel(): string
    {
        return (string) __('admin.prayers.types.' . $this->type);
    }

    public function isPubliclyVisible(): bool
    {
        return $this->statut === 'approuve' && $this->type === 'priere';
    }

    public function visibilityLabel(): string
    {
        return (string) ($this->isPubliclyVisible()
            ? __('admin.prayers.visibility.public')
            : __('admin.prayers.visibility.private'));
    }

    public function publicAuthorName(): string
    {
        if ($this->anonyme) {
            return (string) __('contact_prayer.wall.anonymous');
        }

        return (string) $this->prenom;
    }
}
