<?php

namespace App\Http\Controllers;

use App\Models\CandidatureOffre;
use App\Models\CandidatureOffreMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminOfferApplicationAttachmentController extends Controller
{
    public function __invoke(Request $request, CandidatureOffre $candidature, string $type, ?int $index = null): StreamedResponse
    {
        $this->ensureAccess($request, $candidature);

        $path = match ($type) {
            'lettre' => $candidature->lettre_motivation,
            'diplome' => $candidature->diplome_fichiers[$index ?? 0] ?? null,
            'attestation' => $candidature->attestation_fichiers[$index ?? 0] ?? null,
            default => null,
        };

        abort_unless(is_string($path) && $path !== '', 404);
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }

    public function downloadMessageAttachment(Request $request, CandidatureOffre $candidature, CandidatureOffreMessage $message): StreamedResponse
    {
        $this->ensureAccess($request, $candidature);

        abort_unless($message->candidature_offre_id === $candidature->id, 404);
        abort_unless(is_string($message->attachment_path) && $message->attachment_path !== '', 404);
        abort_unless(Storage::disk('local')->exists($message->attachment_path), 404);

        return Storage::disk('local')->download(
            $message->attachment_path,
            $message->attachment_name ?: basename($message->attachment_path)
        );
    }

    protected function ensureAccess(Request $request, CandidatureOffre $candidature): void
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($request->routeIs('panel.admin.applications.*')) {
            abort_unless($user->isAdmin(), 403);

            return;
        }

        if ($request->routeIs('panel.company.applications.*')) {
            abort_unless(
                $user->isCompany()
                && (int) $candidature->opportunite?->user_id === (int) $user->id,
                403
            );

            return;
        }

        abort_unless($candidature->belongsToUser($user), 403);
    }
}
