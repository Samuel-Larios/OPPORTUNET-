<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $fillable = [
        'subject',
        'audience',
        'content_type',
        'content_id',
        'content_title',
        'content_url',
        'status',
        'recipients_count',
        'sent_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
