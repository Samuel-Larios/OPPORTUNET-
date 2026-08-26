<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'reference', 'amount', 'currency', 'donor_name', 'donor_email', 'donor_phone',
        'status', 'provider_status', 'provider_transaction_id', 'provider_reference',
        'checkout_url', 'provider_payload', 'paid_at', 'status_checked_at',
    ];

    protected function casts(): array
    {
        return [
            'provider_payload' => 'array',
            'paid_at' => 'datetime',
            'status_checked_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'reference';
    }
}
