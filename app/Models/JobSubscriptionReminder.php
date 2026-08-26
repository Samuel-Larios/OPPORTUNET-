<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobSubscriptionReminder extends Model
{
    protected $fillable = ['job_subscription_id', 'user_id', 'type', 'sent_at'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(JobSubscription::class, 'job_subscription_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
