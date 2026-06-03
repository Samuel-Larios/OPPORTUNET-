<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationRecipients
{
    public static function admins(): Collection
    {
        return User::query()
            ->whereHas('role', fn ($query) => $query->whereIn('nom', ['super_admin', 'admin']))
            ->get();
    }

    public static function offerManagers(): Collection
    {
        return User::query()
            ->whereHas('role', fn ($query) => $query->whereIn('nom', ['super_admin', 'admin', 'editeur']))
            ->get();
    }
}
