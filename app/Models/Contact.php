<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'user_id',
        'prenom',
        'nom',
        'email',
        'telephone',
        'whatsapp',
        'pays',
        'sujet',
        'sujet_personnalise',
        'message',
        'priorite',
        'statut',
        'reponse_admin',
        'notes_admin',
        'traite_par',
        'repondu_le',
        'rappel_le',
        'rappel_note',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'repondu_le' => 'datetime',
            'rappel_le' => 'datetime',
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

    public function replies(): HasMany
    {
        return $this->hasMany(ContactReply::class)->orderByDesc('sent_at')->orderByDesc('id');
    }

    public function statusLabel(): string
    {
        return (string) __('admin.contacts.statuses.' . $this->statut);
    }

    public function priorityLabel(): string
    {
        return (string) __('admin.contacts.priorities.' . $this->priorite);
    }

    public function subjectLabel(): string
    {
        return (string) __('home.forms.contact.subjects.' . $this->sujet);
    }

    public function fullName(): string
    {
        return trim($this->prenom . ' ' . ($this->nom ?? ''));
    }

    public function reminderIsDue(): bool
    {
        return $this->rappel_le !== null && $this->rappel_le->isPast();
    }
}
