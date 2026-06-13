<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Formation extends Model
{
    use Bilingual;
    use SoftDeletes;

    protected $table = 'formations';

    protected $fillable = [
        'categorie_id',
        'formateur_id',
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
        'image_couverture',
        'mode',
        'lieu',
        'lieu_fr',
        'lieu_en',
        'lien_en_ligne',
        'prix',
        'devise',
        'gratuit',
        'duree_heures',
        'nb_seances',
        'date_debut',
        'date_fin',
        'heure_debut',
        'fuseau_horaire',
        'places_max',
        'places_restantes',
        'niveau',
        'niveau_fr',
        'niveau_en',
        'prerequis',
        'prerequis_fr',
        'prerequis_en',
        'objectifs',
        'objectifs_fr',
        'objectifs_en',
        'programme',
        'programme_fr',
        'programme_en',
        'certificat',
        'certificat_fr',
        'certificat_en',
        'statut',
        'inscriptions_ouvertes',
        'en_vedette',
        'vues',
        'whatsapp_message',
        'whatsapp_message_fr',
        'whatsapp_message_en',
        'auto_publish',
        'scheduled_for',
        'published_at',
        'scheduled_status',
    ];

    protected array $bilingual = [
        'titre',
        'description_courte',
        'description_longue',
        'lieu',
        'niveau',
        'prerequis',
        'objectifs',
        'programme',
        'certificat',
        'whatsapp_message',
    ];

    protected function casts(): array
    {
        return [
            'gratuit' => 'boolean',
            'inscriptions_ouvertes' => 'boolean',
            'en_vedette' => 'boolean',
            'date_debut' => 'date',
            'date_fin' => 'date',
            'auto_publish' => 'boolean',
            'scheduled_for' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }

    public function formateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'formateur_id');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(InscriptionFormation::class);
    }

    public function isPubliclyVisible(): bool
    {
        return in_array($this->statut, ['ouverte', 'complete', 'terminee'], true);
    }

    public function isRegistrationOpen(): bool
    {
        if (! $this->isPubliclyVisible() || ! $this->inscriptions_ouvertes) {
            return false;
        }

        if ($this->statut !== 'ouverte') {
            return false;
        }

        if ($this->date_debut && now()->startOfDay()->gte($this->date_debut->startOfDay())) {
            return false;
        }

        if ($this->date_fin && now()->startOfDay()->gt($this->date_fin->startOfDay())) {
            return false;
        }

        if ($this->places_restantes !== null && $this->places_restantes <= 0) {
            return false;
        }

        return true;
    }

    public function availabilityState(): string
    {
        if ($this->statut === 'annulee') {
            return 'annulee';
        }

        if ($this->statut === 'terminee' || ($this->date_fin && now()->startOfDay()->gt($this->date_fin->startOfDay()))) {
            return 'terminee';
        }

        if ($this->statut === 'complete' || ($this->places_restantes !== null && $this->places_restantes <= 0)) {
            return 'complete';
        }

        if ($this->date_debut && now()->startOfDay()->gte($this->date_debut->startOfDay())) {
            return 'demarree';
        }

        return $this->inscriptions_ouvertes && $this->statut === 'ouverte'
            ? 'ouverte'
            : 'fermee';
    }

    public function publicCoverUrl(): ?string
    {
        $path = (string) ($this->image_couverture ?? '');

        if ($path === '') {
            return null;
        }

        if (preg_match('/^https?:\/\//i', $path) || str_starts_with($path, '/')) {
            return $path;
        }

        $normalizedPath = Str::startsWith($path, 'storage/')
            ? Str::after($path, 'storage/')
            : $path;

        return Storage::disk('public')->url($normalizedPath);
    }
}
