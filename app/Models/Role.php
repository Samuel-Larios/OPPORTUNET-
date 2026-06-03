<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'nom',
        'libelle',
        'description',
        'permissions',
        'actif',
    ];

    protected function casts(): array
    {
        return [
            'permissions' => 'array',
            'actif' => 'boolean',
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
