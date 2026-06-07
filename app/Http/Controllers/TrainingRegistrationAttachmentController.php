<?php

namespace App\Http\Controllers;

use App\Models\FormationRegistrationMessage;
use App\Models\InscriptionFormation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TrainingRegistrationAttachmentController extends Controller
{
    public function downloadMessageAttachment(Request $request, InscriptionFormation $registration, FormationRegistrationMessage $message): StreamedResponse
    {
        $this->ensureAccess($request, $registration);

        abort_unless($message->inscription_formation_id === $registration->id, 404);
        abort_unless(is_string($message->attachment_path) && $message->attachment_path !== '', 404);
        $disk = $this->resolveDisk($message->attachment_path);
        abort_unless($disk !== null, 404);

        return Storage::disk($disk)->download(
            $message->attachment_path,
            $message->attachment_name ?: basename($message->attachment_path)
        );
    }

    protected function ensureAccess(Request $request, InscriptionFormation $registration): void
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($request->routeIs('panel.admin.training-registrations.*')) {
            abort_unless($user->isAdmin(), 403);

            return;
        }

        abort_unless($registration->belongsToUser($user), 403);
    }

    protected function resolveDisk(string $path): ?string
    {
        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                return $disk;
            }
        }

        return null;
    }
}
