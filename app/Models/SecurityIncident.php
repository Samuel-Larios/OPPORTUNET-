<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityIncident extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'ip_address',
        'country_code',
        'type',
        'severity',
        'reason',
        'route_name',
        'path',
        'method',
        'user_agent',
        'metadata',
        'count_for_auto_block',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'count_for_auto_block' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
