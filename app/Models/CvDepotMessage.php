<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvDepotMessage extends Model
{
    protected $table = 'cv_depot_messages';

    protected $fillable = [
        'cv_depot_id',
        'sender_id',
        'sender_role',
        'message',
        'attachment_path',
        'attachment_name',
        'attachment_mime',
    ];

    protected $touches = ['cvDepot'];

    public function cvDepot(): BelongsTo
    {
        return $this->belongsTo(CvDepot::class);
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
        if ($this->isAdmin()) {
            return (string) __('admin.cv_depots.messages.admin_sender');
        }

        return $this->sender?->fullName() ?: (string) __('admin.cv_depots.messages.user_sender');
    }
}
