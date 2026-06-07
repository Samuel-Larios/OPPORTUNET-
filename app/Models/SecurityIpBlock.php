<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityIpBlock extends Model
{
    protected $fillable = [
        'ip_address',
        'reason',
        'blocked_until',
        'is_manual',
        'incidents_count',
        'notes',
        'created_by',
        'last_triggered_at',
    ];

    protected function casts(): array
    {
        return [
            'blocked_until' => 'datetime',
            'is_manual' => 'boolean',
            'last_triggered_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->is_manual || $this->blocked_until === null || $this->blocked_until->isFuture();
    }
}
