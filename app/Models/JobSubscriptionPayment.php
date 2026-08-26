<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobSubscriptionPayment extends Model
{
    protected $fillable = ['reference', 'user_id', 'customer_name', 'customer_email', 'customer_phone', 'customer_country', 'weeks', 'amount', 'currency', 'provider', 'provider_transaction_id', 'provider_reference', 'payment_method', 'provider_status', 'status_checked_at', 'status', 'checkout_url', 'paid_at', 'provider_payload'];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime', 'status_checked_at' => 'datetime', 'provider_payload' => 'array'];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(JobSubscription::class, 'payment_id');
    }
}
