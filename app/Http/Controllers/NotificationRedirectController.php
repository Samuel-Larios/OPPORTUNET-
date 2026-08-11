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

        $actionUrl = $this->resolveActionUrl($notification, $user);

        if (is_string($actionUrl) && $actionUrl !== '') {
            return redirect()->to($actionUrl);
        }

        return redirect()->route('panel.notifications');
    }

    protected function resolveActionUrl(DatabaseNotification $notification, mixed $user): ?string
    {
        $actionUrl = $notification->data['action_url'] ?? null;
        $resourceType = $notification->data['resource_type'] ?? null;
        $resourceId = $notification->data['resource_id'] ?? null;

        // Si on n’a pas l’info pour reconstruire proprement l’URL, on fallback.
        if (! is_numeric($resourceId)) {
            return is_string($actionUrl) && $actionUrl !== '' ? $actionUrl : null;
        }

        $resourceId = (int) $resourceId;

        // Ne jamais utiliser une URL absolue stockée dans action_url (peut contenir localhost en prod).
        // On reconstruit à partir de routes internes + paramètres, ce qui garantit le bon APP_URL.
        return match ($resourceType) {
            'contact' => route('panel.admin.contacts', ['contact' => $resourceId]),
            'cv_depot' => route('panel.admin.cv-depots', ['cv' => $resourceId]),
            'training_registration' => route('panel.admin.training-registrations', ['registration' => $resourceId]),
            'offer_application' => $user->canManageOffers()
                ? route('panel.admin.applications', ['application' => $resourceId])
                : route('panel.user.applications', ['application' => $resourceId]),
            'offer' => route('panel.editor.offers', ['offer' => $resourceId]),
            default => (is_string($actionUrl) && $actionUrl !== '' ? $actionUrl : null),
        };
    }
}
