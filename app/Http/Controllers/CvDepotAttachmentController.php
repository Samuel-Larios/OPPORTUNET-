<?php

namespace App\Http\Controllers;

use App\Models\CvDepot;
use App\Models\CvDepotMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvDepotAttachmentController extends Controller
{
    public function downloadCv(Request $request, CvDepot $cvDepot): StreamedResponse
    {
        $this->ensureAccess($request, $cvDepot);

        abort_unless(is_string($cvDepot->cv_fichier) && $cvDepot->cv_fichier !== '', 404);
        abort_unless(Storage::disk('public')->exists($cvDepot->cv_fichier), 404);

        return Storage::disk('public')->download($cvDepot->cv_fichier, basename($cvDepot->cv_fichier));
    }

    public function downloadMessageAttachment(Request $request, CvDepot $cvDepot, CvDepotMessage $message): StreamedResponse
    {
        $this->ensureAccess($request, $cvDepot);
        abort_unless($message->cv_depot_id === $cvDepot->id, 404);
        abort_unless(is_string($message->attachment_path) && $message->attachment_path !== '', 404);
        abort_unless(Storage::disk('public')->exists($message->attachment_path), 404);

        return Storage::disk('public')->download(
            $message->attachment_path,
            $message->attachment_name ?: basename($message->attachment_path)
        );
    }

    protected function ensureAccess(Request $request, CvDepot $cvDepot): void
    {
        $user = $request->user();
        abort_unless($user, 403);

        if ($request->routeIs('panel.admin.cv-depots.*')) {
            abort_unless($user->isAdmin(), 403);

            return;
        }

        abort_unless((int) $cvDepot->user_id === (int) $user->id, 403);
    }
}
