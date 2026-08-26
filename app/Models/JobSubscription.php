<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSubscription extends Model
{
    protected $fillable = ['user_id', 'weeks', 'amount', 'currency', 'starts_at', 'expires_at', 'payment_id'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'expires_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(JobSubscriptionPayment::class, 'payment_id');
    }
}
