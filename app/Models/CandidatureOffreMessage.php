<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CandidatureOffreMessage extends Model
{
    protected $table = 'candidature_offre_messages';

    protected $fillable = [
        'candidature_offre_id',
        'sender_id',
        'sender_role',
        'message',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(CandidatureOffre::class, 'candidature_offre_id');
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
            ? (string) __('admin.application_messages.admin_sender')
            : (string) __('admin.application_messages.user_sender');
    }
}
