<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Http\Request;

class NotificationRedirectController extends Controller
{
    public function __invoke(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, 403);

        abort_unless(
            $notification->notifiable_type === $user::class
            && (int) $notification->notifiable_id === (int) $user->id,
            403
        );

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $actionUrl = $notification->data['action_url'] ?? null;

        if (is_string($actionUrl) && $actionUrl !== '') {
            return redirect()->to($actionUrl);
        }

        return redirect()->route('panel.notifications');
    }
}
