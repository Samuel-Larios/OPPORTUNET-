<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PriereSoutien extends Model
{
    protected $table = 'prieres_soutiens';

    protected $fillable = [
        'priere_id',
        'user_id',
        'ip_address',
    ];

    public function priere(): BelongsTo
    {
        return $this->belongsTo(MurDePriere::class, 'priere_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
