<?php

namespace App\Http\Controllers;

use App\Models\CandidatureOffre;
use App\Models\Opportunite;
use App\Notifications\PlatformDatabaseNotification;
use App\Support\NotificationRecipients;
use App\Support\SubmissionGuard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class OfferApplicationController extends Controller
{
    public function entry(Request $request, Opportunite $opportunite): RedirectResponse
    {
        abort_unless($opportunite->statut === 'publie', 404);

        if (! $request->user()) {
            return redirect()->guest(route('login'));
        }

        if (! $request->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if (! $request->user()->hasRole('user')) {
            return redirect()->route($request->user()->dashboardRouteName());
        }

        return redirect()->to(route('offers.show', $opportunite->slug) . '#application-form');
    }

    public function store(Request $request, Opportunite $opportunite): RedirectResponse
    {
        abort_unless($opportunite->statut === 'publie', 404);

        $user = $request->user();

        SubmissionGuard::ensureSafeRequest($request, [
            'pays',
            'message',
        ]);

        $validated = $request->validate([
            'telephone' => ['nullable', 'string', 'max:20'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'pays' => ['nullable', 'string', 'max:80'],
            'message' => ['nullable', 'string', 'max:2000'],
            'lettre_motivation' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'diplomes' => ['required', 'array', 'min:1', 'max:5'],
            'diplomes.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'attestations' => ['required', 'array', 'min:1', 'max:5'],
            'attestations.*' => ['file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
        ], [
            'lettre_motivation.required' => __('offers.application.validation.letter_required'),
            'diplomes.required' => __('offers.application.validation.diplomas_required'),
            'attestations.required' => __('offers.application.validation.certificates_required'),
        ]);

        if (CandidatureOffre::query()->where('user_id', $user->id)->where('opportunite_id', $opportunite->id)->exists()) {
            return redirect()
                ->to(route('offers.show', $opportunite->slug) . '#application-form')
                ->withErrors(['application' => __('offers.application.validation.already_applied')]);
        }

        $letterPath = $validated['lettre_motivation']->store('offer-applications/letters', 'local');
        $diplomaPaths = collect($request->file('diplomes', []))
            ->map(fn ($file) => $file->store('offer-applications/diplomas', 'local'))
            ->all();
        $certificatePaths = collect($request->file('attestations', []))
            ->map(fn ($file) => $file->store('offer-applications/certificates', 'local'))
            ->all();

        $application = CandidatureOffre::query()->create([
            'user_id' => $user->id,
            'opportunite_id' => $opportunite->id,
            'prenom' => $user->prenom,
            'nom' => $user->nom,
            'email' => $user->email,
            'telephone' => $validated['telephone'] ?: ($user->telephone ?: null),
            'whatsapp' => $validated['whatsapp'] ?: ($user->whatsapp ?: null),
            'pays' => $validated['pays'] ?: ($user->pays ?: null),
            'lettre_motivation' => $letterPath,
            'diplome_fichiers' => $diplomaPaths,
            'attestation_fichiers' => $certificatePaths,
            'message' => $validated['message'] ?: null,
            'statut' => 'en_attente',
        ]);

        Notification::send(
            NotificationRecipients::admins(),
            new PlatformDatabaseNotification([
                'title' => __('admin.notifications.events.offer_application.title'),
                'message' => __('admin.notifications.events.offer_application.message', [
                    'name' => $user->fullName(),
                    'offer' => $opportunite->titre,
                ]),
                'action_url' => route('panel.admin.applications', ['application' => $application->id]),
                'action_label' => __('admin.notifications.open'),
                'category' => 'application',
                'level' => 'info',
                'resource_type' => 'offer_application',
                'resource_id' => $application->id,
            ])
        );

        if (($validated['telephone'] ?? '') !== '' || ($validated['whatsapp'] ?? '') !== '' || ($validated['pays'] ?? '') !== '') {
            $user->update([
                'telephone' => $validated['telephone'] ?: $user->telephone,
                'whatsapp' => $validated['whatsapp'] ?: $user->whatsapp,
                'pays' => $validated['pays'] ?: $user->pays,
            ]);
        }

        return redirect()
            ->to(route('offers.show', $opportunite->slug) . '#application-form')
            ->with('offer_application_success', __('offers.application.success'));
    }
}
