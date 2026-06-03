<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormationRegistrationMessage extends Model
{
    protected $table = 'inscription_formation_messages';

    protected $fillable = [
        'inscription_formation_id',
        'sender_id',
        'sender_role',
        'message',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(InscriptionFormation::class, 'inscription_formation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function isAdmin(): bool
    {
        return $this->sender_role === 'admin';
    }

    public function senderLabel(): string
    {
        return $this->isAdmin()
            ? (string) __('admin.training_registrations.messages.admin_sender')
            : (string) __('admin.training_registrations.messages.user_sender');
    }
}
