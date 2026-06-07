<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

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

        $actionUrl = $this->resolveActionUrl($notification);

        if (is_string($actionUrl) && $actionUrl !== '') {
            return redirect()->to($actionUrl);
        }

        return redirect()->route('panel.notifications');
    }

    protected function resolveActionUrl(DatabaseNotification $notification): ?string
    {
        $actionUrl = $notification->data['action_url'] ?? null;
        $resourceType = $notification->data['resource_type'] ?? null;
        $resourceId = $notification->data['resource_id'] ?? null;

        if (! is_string($actionUrl) || $actionUrl === '' || ! is_numeric($resourceId)) {
            return is_string($actionUrl) && $actionUrl !== '' ? $actionUrl : null;
        }

        $resourceId = (int) $resourceId;
        $parsed = parse_url($actionUrl);
        $basePath = $parsed['path'] ?? '';
        parse_str($parsed['query'] ?? '', $query);

        $query = match ($resourceType) {
            'contact' => $basePath === route('panel.admin.contacts', [], false)
                ? [...$query, 'contact' => $resourceId]
                : $query,
            'cv_depot' => [...$query, 'cv' => $resourceId],
            'training_registration' => [...$query, 'registration' => $resourceId],
            'offer_application' => [...$query, 'application' => $resourceId],
            'offer' => [...$query, 'offer' => $resourceId],
            default => $query,
        };

        $rebuiltQuery = http_build_query($query);

        return $rebuiltQuery !== '' ? $basePath . '?' . $rebuiltQuery : $actionUrl;
    }
}
