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
        'auto_publish',
        'scheduled_for',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'auto_publish' => 'boolean',
            'scheduled_for' => 'datetime',
            'published_at' => 'datetime',
            'sent_at' => 'datetime',
            'recipients_count' => 'integer',
            'meta' => 'array',
        ];
    }
}
