<?php

use App\Http\Controllers\AdminOfferApplicationAttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CvDepotAttachmentController;
use App\Http\Controllers\NotificationRedirectController;
use App\Http\Controllers\OfferApplicationController;
use App\Http\Controllers\TrainingRegistrationAttachmentController;
use App\Mail\ContactConfirmationMail;
use App\Mail\ContactReceivedMail;
use App\Mail\TrainingRegistrationReceivedMail;
use App\Notifications\PlatformDatabaseNotification;
use App\Support\NotificationRecipients;
use App\Support\SubmissionGuard;
use App\Models\Banniere;
use App\Models\BlogArticle;
use App\Models\BlogCommentaire;
use App\Models\CandidatureOffre;
use App\Models\Contact;
use App\Models\CvDepot;
use App\Models\Formation;
use App\Models\InscriptionFormation;
use App\Models\MurDePriere;
use App\Models\NewsletterSubscriber;
use App\Models\Opportunite;
use App\Models\ParametreSite;
use App\Models\PriereSoutien;
use App\Models\Service;
use App\Models\SpiritualPublication;
use App\Models\Temoignage;
use App\Models\User;
use App\Models\Verset;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

$siteSettings = function () {
    return ParametreSite::query()
        ->whereIn('cle', [
            'site_nom',
            'site_slogan',
            'site_email',
            'site_horaires',
            'site_adresse',
            'whatsapp_numero',
            'whatsapp_message_defaut',
            'support_kkiapay_url',
            'support_bank_name',
            'support_bank_account_name',
            'support_bank_account_number',
            'support_bank_iban',
            'support_bank_swift',
            'facebook_url',
            'instagram_url',
            'linkedin_url',
            'youtube_url',
            'tiktok_url',
        ])
        ->get()
        ->keyBy('cle');
};

$resolveRedirectTarget = function (Request $request, string $fallbackRoute, string $fallbackFragment = '') {
    $redirectTo = $request->input('redirect_to');

    if (is_string($redirectTo) && $redirectTo !== '') {
        $parts = parse_url($redirectTo);
        $host = $parts['host'] ?? null;
        $path = $parts['path'] ?? null;

        if (($host === null || $host === $request->getHost()) && is_string($path) && str_starts_with($path, '/')) {
            $target = $path;

            if (! empty($parts['query'])) {
                $target .= '?' . $parts['query'];
            }

            if (! empty($parts['fragment'])) {
                $target .= '#' . $parts['fragment'];
            }

            return $target;
        }
    }

    $target = route($fallbackRoute);

    return $fallbackFragment !== '' ? $target . '#' . $fallbackFragment : $target;
};

$resolvePrayerWhatsappGroupUrl = function (): ?string {
    $rawUrl = trim((string) (ParametreSite::query()->where('cle', 'whatsapp_groupe_priere_url')->value('valeur') ?? ''));

    if ($rawUrl === '') {
        return null;
    }

    $normalizedUrl = preg_match('#^https?://#i', $rawUrl) ? $rawUrl : 'https://' . ltrim($rawUrl, '/');

    return filter_var($normalizedUrl, FILTER_VALIDATE_URL) ? $normalizedUrl : null;
};

$siteInfoPage = function (string $key) {
    $page = __("site.{$key}");
    abort_unless(is_array($page), 404);

    return view('site.info', [
        'title' => $page['title'] ?? 'Opportunet Mondiale',
        'description' => $page['description'] ?? null,
        'intro' => $page['intro'] ?? '',
        'sections' => $page['sections'] ?? [],
    ]);
};

Route::get('/robots.txt', function (Request $request) {
    $baseUrl = rtrim(
        config('app.url') && config('app.url') !== 'http://localhost'
            ? (string) config('app.url')
            : $request->root(),
        '/'
    );

    return response(
        "User-agent: *\n"
        . "Allow: /\n"
        . "Disallow: /admin\n"
        . "Disallow: /espace-administration\n"
        . "Disallow: /connexion\n"
        . "Disallow: /inscription\n"
        . "Disallow: /inscription-entreprise\n"
        . "Disallow: /inscription-utilisateur\n"
        . "Disallow: /verification-email\n"
        . "Disallow: /mon-espace\n"
        . "Disallow: /espace-entreprise\n"
        . "Sitemap: {$baseUrl}/sitemap.xml\n",
        200,
        ['Content-Type' => 'text/plain; charset=UTF-8']
    );
})->name('seo.robots');

Route::get('/sitemap.xml', function () {
    $locales = ['fr', 'en'];
    $entries = collect();

    $staticUrls = [
        route('home'),
        route('offers.index'),
        route('cv.services.index'),
        route('trainings.index'),
        route('articles.index'),
        route('contact.prayer.index'),
        route('community.prayers.index'),
        route('community.testimonials.index'),
        route('spiritual.verses.index'),
        route('spiritual.thoughts.index'),
        route('spiritual.exhortations.index'),
        route('spiritual.daily-prayers.index'),
        route('spiritual.conversion.index'),
    ];

    foreach ($locales as $locale) {
        foreach ($staticUrls as $url) {
            $entries->push([
                'loc' => Seo::localizedUrl($url, $locale),
                'lastmod' => null,
                'alternates' => Seo::alternateLocaleUrls($url),
            ]);
        }
    }

    foreach (Opportunite::query()->where('statut', 'publie')->get(['slug', 'updated_at']) as $opportunity) {
        foreach ($locales as $locale) {
            $entries->push([
                'loc' => Seo::localizedUrl(route('offers.show', $opportunity->slug), $locale),
                'lastmod' => optional($opportunity->updated_at)->toAtomString(),
                'alternates' => Seo::alternateLocaleUrls(route('offers.show', $opportunity->slug)),
            ]);
        }
    }

    foreach (BlogArticle::query()->where('statut', 'publie')->get(['slug', 'updated_at']) as $article) {
        foreach ($locales as $locale) {
            $entries->push([
                'loc' => Seo::localizedUrl(route('articles.show', $article->slug), $locale),
                'lastmod' => optional($article->updated_at)->toAtomString(),
                'alternates' => Seo::alternateLocaleUrls(route('articles.show', $article->slug)),
            ]);
        }
    }

    foreach (Formation::query()->whereIn('statut', ['ouverte', 'complete', 'terminee'])->get(['id', 'updated_at']) as $training) {
        foreach ($locales as $locale) {
            $entries->push([
                'loc' => Seo::localizedUrl(route('trainings.index'), $locale, ['formation' => $training->id]),
                'lastmod' => optional($training->updated_at)->toAtomString(),
                'alternates' => Seo::alternateLocaleUrls(route('trainings.index'), ['formation' => $training->id]),
            ]);
        }
    }

    return response()->view('seo.sitemap', [
        'entries' => $entries->unique('loc')->values(),
    ], 200, [
        'Content-Type' => 'application/xml; charset=UTF-8',
    ]);
})->name('seo.sitemap');

Route::get('/locale/{locale}', function (Request $request, string $locale) {
    abort_unless(in_array($locale, ['fr', 'en'], true), 404);

    $request->session()->put('locale', $locale);

    return redirect()->back();
})->name('locale.switch');

Route::get('/a-propos', fn () => $siteInfoPage('about'))->name('site.about');
Route::get('/centre-aide', fn () => $siteInfoPage('help'))->name('site.help');
Route::get('/documentation', fn () => $siteInfoPage('documentation'))->name('site.documentation');
Route::get('/securite', fn () => $siteInfoPage('security'))->name('site.security');
Route::get('/politique-confidentialite', fn () => $siteInfoPage('privacy'))->name('site.privacy');
Route::get('/conditions-utilisation', fn () => $siteInfoPage('terms'))->name('site.terms');
Route::get('/politique-cookies', fn () => $siteInfoPage('cookies'))->name('site.cookies');

Route::get('/admin', function (Request $request) {
    $user = $request->user();

    if (! $user) {
        abort(404);
    }

    if (! $user->isAdmin()) {
        return redirect()->route($user->dashboardRouteName());
    }

    return redirect()->route('panel.admin.dashboard');
})->name('panel.admin.entry');

Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'createLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:auth-login')->name('login.store');
    Route::get('/inscription', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/inscription', [AuthController::class, 'register'])->middleware('throttle:auth-register')->name('register.store');
    Route::get('/inscription-entreprise', [AuthController::class, 'createRegister'])->name('register.company');
    Route::post('/inscription-entreprise', [AuthController::class, 'register'])->middleware('throttle:auth-register')->name('register.company.store');
    Route::get('/inscription-utilisateur', [AuthController::class, 'createUserRegister'])->name('register.user');
    Route::post('/inscription-utilisateur', [AuthController::class, 'register'])->middleware('throttle:auth-register')->name('register.user.store');
});

Route::get('/offres-opportunites/{opportunite:slug}/postuler', [OfferApplicationController::class, 'entry'])
    ->name('offers.apply.entry');

Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    Route::get('/verification-email', [AuthController::class, 'createVerifyEmail'])->name('verification.notice');
    Route::get('/verification-email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/verification-email/renvoyer', [AuthController::class, 'sendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('/offres-opportunites/{opportunite:slug}/candidatures', [OfferApplicationController::class, 'store'])
        ->middleware(['role:user', 'throttle:user-form'])
        ->name('offers.apply.store');

    Route::get('/tableau-de-bord', function () {
        return redirect()->route(auth()->user()->dashboardRouteName());
    })->name('dashboard');

    Route::view('/notifications', 'panel.shared.notifications')
        ->name('panel.notifications');
    Route::get('/notifications/{notification}/open', NotificationRedirectController::class)
        ->name('panel.notifications.open');

    Route::view('/espace-administration', 'panel.admin.dashboard')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.dashboard');

    Route::view('/admin/offres', 'panel.editor.offers')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.offers');

    Route::view('/admin/articles', 'panel.editor.articles')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.articles');

    Route::view('/admin/articles/commentaires', 'panel.admin.article-comments')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.article-comments');

    Route::view('/admin/services', 'panel.editor.services')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.services');

    Route::view('/admin/formations', 'panel.editor.trainings')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.trainings');

    Route::view('/admin/categories', 'panel.editor.categories')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.categories');

    Route::view('/admin/versets', 'panel.editor.verses')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.verses');
    Route::view('/admin/pensees-du-jour', 'panel.editor.thoughts')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.thoughts');
    Route::view('/admin/exhortations', 'panel.editor.exhortations')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.exhortations');
    Route::view('/admin/prieres-du-jour', 'panel.editor.daily-prayers')
        ->middleware('role:super_admin,admin,editeur')
        ->name('panel.editor.daily-prayers');

    Route::view('/admin/utilisateurs', 'panel.admin.users')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.users');

    Route::view('/admin/candidatures', 'panel.admin.applications')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.applications');
    Route::get('/admin/candidatures/{candidature}/fichiers/{type}/{index?}', AdminOfferApplicationAttachmentController::class)
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.applications.download');
    Route::get('/admin/candidatures/{candidature}/messages/{message}/attachment', [AdminOfferApplicationAttachmentController::class, 'downloadMessageAttachment'])
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.applications.download-message');

    Route::view('/admin/contacts', 'panel.admin.contacts')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.contacts');

    Route::view('/admin/cv-depots', 'panel.admin.cv-depots')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.cv-depots');
    Route::get('/admin/cv-depots/{cvDepot}/cv', [CvDepotAttachmentController::class, 'downloadCv'])
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.cv-depots.download-cv');
    Route::get('/admin/cv-depots/{cvDepot}/messages/{message}/attachment', [CvDepotAttachmentController::class, 'downloadMessageAttachment'])
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.cv-depots.download-message');

    Route::view('/admin/formations/inscriptions', 'panel.admin.training-registrations')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.training-registrations');
    Route::get('/admin/formations/inscriptions/{registration}/messages/{message}/attachment', [TrainingRegistrationAttachmentController::class, 'downloadMessageAttachment'])
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.training-registrations.download-message');

    Route::view('/admin/temoignages', 'panel.admin.testimonials')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.testimonials');

    Route::view('/admin/prieres', 'panel.admin.prayers')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.prayers');

    Route::view('/admin/parametres', 'panel.admin.settings')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.settings');
    Route::view('/admin/securite', 'panel.admin.security')
        ->middleware('role:super_admin,admin')
        ->name('panel.admin.security');

    Route::view('/espace-entreprise', 'panel.company.dashboard')
        ->middleware('role:entreprise')
        ->name('panel.company.dashboard');

    Route::view('/espace-entreprise/offres', 'panel.company.offers')
        ->middleware('role:entreprise')
        ->name('panel.company.offers');

    Route::view('/espace-entreprise/candidatures', 'panel.company.applications')
        ->middleware('role:entreprise')
        ->name('panel.company.applications');

    Route::get('/espace-entreprise/candidatures/{candidature}/fichiers/{type}/{index?}', AdminOfferApplicationAttachmentController::class)
        ->middleware('role:entreprise')
        ->name('panel.company.applications.download');

    Route::view('/mon-espace', 'panel.user.dashboard')
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.dashboard');

    Route::view('/mon-espace/demandes', 'panel.user.requests')
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.requests');

    Route::view('/mon-espace/cv', 'panel.user.cv-depots')
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.cv-depots');
    Route::get('/mon-espace/cv/{cvDepot}/cv', [CvDepotAttachmentController::class, 'downloadCv'])
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.cv-depots.download-cv');
    Route::get('/mon-espace/cv/{cvDepot}/messages/{message}/attachment', [CvDepotAttachmentController::class, 'downloadMessageAttachment'])
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.cv-depots.download-message');

    Route::view('/mon-espace/formations', 'panel.user.trainings')
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.trainings');
    Route::get('/mon-espace/formations/{registration}/messages/{message}/attachment', [TrainingRegistrationAttachmentController::class, 'downloadMessageAttachment'])
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.trainings.download-message');

    Route::view('/mon-espace/candidatures', 'panel.user.applications')
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.applications');
    Route::get('/mon-espace/candidatures/{candidature}/fichiers/{type}/{index?}', AdminOfferApplicationAttachmentController::class)
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.applications.download');
    Route::get('/mon-espace/candidatures/{candidature}/messages/{message}/attachment', [AdminOfferApplicationAttachmentController::class, 'downloadMessageAttachment'])
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.applications.download-message');

    Route::view('/mon-espace/temoignages', 'panel.user.testimonials')
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.testimonials');

    Route::view('/mon-espace/prieres', 'panel.user.prayers')
        ->middleware('role:user,coach,moderateur')
        ->name('panel.user.prayers');

    Route::post('/temoignages', function (Request $request) {
        $user = $request->user();
        abort_unless($user, 403);

        $data = $request->validate([
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['nullable', 'string', 'max:80'],
            'pays' => ['nullable', 'string', 'max:80'],
            'profession' => ['nullable', 'string', 'max:120'],
            'type' => ['required', Rule::in(['emploi_trouve', 'formation_suivie', 'service_cv', 'coaching', 'general'])],
            'note' => ['nullable', 'integer', 'between:1,5'],
            'contenu' => ['required', 'string', 'max:4000'],
        ]);

        SubmissionGuard::ensureSafeRequest($request, [
            'prenom',
            'nom',
            'pays',
            'profession',
            'contenu',
        ]);

        Temoignage::query()->create([
            ...$data,
            'user_id' => $user->id,
            'email' => $user->email,
            'photo' => $user->photo,
            'statut' => 'en_attente',
            'en_vedette' => false,
            'ordre' => 0,
        ]);

        return redirect()
            ->to(route('home') . '#testimonial-form')
            ->with('testimonial_success', __('home.forms.testimonial.success'));
    })->middleware('throttle:user-form')->name('testimonials.store');
});

Route::post('/contact-rapide', function (Request $request) use ($resolveRedirectTarget) {
    $user = $request->user();

    $data = $request->validate([
        'prenom' => ['required', 'string', 'max:80'],
        'nom' => ['nullable', 'string', 'max:80'],
        'email' => ['required', 'email', 'max:191'],
        'telephone' => ['nullable', 'string', 'max:20'],
        'whatsapp' => ['nullable', 'string', 'max:20'],
        'pays' => ['nullable', 'string', 'max:80'],
        'sujet' => ['required', 'in:information,service,formation,offre,partenariat,technique,autre'],
        'sujet_personnalise' => ['nullable', 'string', 'max:200', 'required_if:sujet,autre'],
        'message' => ['required', 'string', 'max:2000'],
    ]);

    SubmissionGuard::ensureSafeRequest($request, [
        'prenom',
        'nom',
        'pays',
        'sujet_personnalise',
        'message',
    ], true);

    $contact = Contact::query()->create([
        ...$data,
        'user_id' => $user?->id,
        'sujet_personnalise' => $data['sujet'] === 'autre' ? ($data['sujet_personnalise'] ?? null) : null,
        'priorite' => 'normale',
        'statut' => 'non_lu',
        'ip_address' => $request->ip(),
    ]);

    $contactEmail = ParametreSite::query()->where('cle', 'site_email')->value('valeur') ?: 'contact@opportunetmondiale.com';

    rescue(fn () => Mail::to($contactEmail)->send(new ContactReceivedMail($contact)));
    rescue(fn () => Mail::to($contact->email)->send(new ContactConfirmationMail($contact)));
    Notification::send(
        NotificationRecipients::admins(),
        new PlatformDatabaseNotification([
            'title' => __('admin.notifications.events.contact_received.title'),
            'message' => __('admin.notifications.events.contact_received.message', ['name' => $contact->fullName()]),
            'action_url' => route('panel.admin.contacts', ['contact' => $contact->id]),
            'action_label' => __('admin.notifications.open'),
            'category' => 'contact',
            'level' => $contact->priorite === 'urgente' ? 'warning' : 'info',
            'resource_type' => 'contact',
            'resource_id' => $contact->id,
        ])
    );

    return redirect()->to($resolveRedirectTarget($request, 'home', 'home-contact'))->with('contact_success', __('home.forms.contact.success'));
})->middleware('throttle:public-form')->name('contact.quick');

Route::post('/mur-de-priere', function (Request $request) use ($resolveRedirectTarget) {
    $user = $request->user();

    $data = $request->validate([
        'prenom' => ['required', 'string', 'max:80'],
        'email' => ['nullable', 'email', 'max:191'],
        'pays' => ['nullable', 'string', 'max:80'],
        'sujet' => ['required', 'string', 'max:2000'],
        'anonyme' => ['nullable', 'boolean'],
    ]);

    SubmissionGuard::ensureSafeRequest($request, [
        'prenom',
        'pays',
        'sujet',
    ], true);

    MurDePriere::query()->create([
        'user_id' => $user?->id,
        'prenom' => $data['prenom'],
        'email' => $user?->email ?? ($data['email'] ?? null),
        'pays' => $data['pays'] ?? null,
        'sujet' => $data['sujet'],
        'type' => 'priere',
        'anonyme' => (bool) ($data['anonyme'] ?? false),
        'priants' => 0,
        'statut' => 'en_attente',
    ]);

    return redirect()->to($resolveRedirectTarget($request, 'home', 'home-prayer'))->with('prayer_success', __('home.forms.prayer.success'));
})->middleware('throttle:public-form')->name('prayer.store');

Route::post('/mur-de-priere/{prayer}/soutien', function (Request $request, MurDePriere $prayer) use ($resolveRedirectTarget, $resolvePrayerWhatsappGroupUrl) {
    abort_unless($prayer->statut === 'approuve' && $prayer->type === 'priere', 404);

    $user = $request->user();
    $ipAddress = $request->ip();
    $whatsappGroupUrl = $resolvePrayerWhatsappGroupUrl();

    $existingSupport = PriereSoutien::query()
        ->where('priere_id', $prayer->id)
        ->when(
            $user,
            fn ($query) => $query->where('user_id', $user->id),
            fn ($query) => $query->whereNull('user_id')->where('ip_address', $ipAddress)
        )
        ->first();

    if ($existingSupport) {
        if ($whatsappGroupUrl) {
            return redirect()->away($whatsappGroupUrl);
        }

        return redirect()
            ->to($resolveRedirectTarget($request, 'contact.prayer.index', 'prayer-wall'))
            ->with('prayer_support_info', __('contact_prayer.wall.support_group_missing'));
    }

    PriereSoutien::query()->create([
        'priere_id' => $prayer->id,
        'user_id' => $user?->id,
        'ip_address' => $ipAddress,
    ]);

    $prayer->increment('priants');

    if ($whatsappGroupUrl) {
        return redirect()->away($whatsappGroupUrl);
    }

    return redirect()
        ->to($resolveRedirectTarget($request, 'contact.prayer.index', 'prayer-wall'))
        ->with('prayer_support_success', __('contact_prayer.wall.support_group_missing'));
})->middleware('throttle:public-action')->name('prayer.support');

Route::post('/newsletter/abonnement', function (Request $request) use ($resolveRedirectTarget) {
    $data = $request->validate([
        'prenom' => ['nullable', 'string', 'max:80'],
        'email' => ['required', 'email', 'max:191'],
    ]);

    SubmissionGuard::ensureSafeRequest($request, ['prenom'], true);

    NewsletterSubscriber::query()->updateOrCreate(
        ['email' => strtolower($data['email'])],
        [
            'prenom' => $data['prenom'] ?? null,
            'langue' => app()->getLocale(),
            'source' => 'website',
            'is_active' => true,
            'subscribed_at' => now(),
        ]
    );

    return redirect()
        ->to($resolveRedirectTarget($request, 'home'))
        ->with('newsletter_success', __('home.forms.newsletter.success'));
})->middleware('throttle:public-form')->name('newsletter.subscribe');

Route::post('/depot-cv-services', function (Request $request) {
    $data = $request->validate([
        'prenom' => ['required', 'string', 'max:80'],
        'nom' => ['required', 'string', 'max:80'],
        'email' => ['required', 'email', 'max:191'],
        'telephone' => ['nullable', 'string', 'max:20'],
        'whatsapp' => ['nullable', 'string', 'max:20'],
        'pays' => ['nullable', 'string', 'max:80'],
        'ville' => ['nullable', 'string', 'max:80'],
        'date_naissance' => ['nullable', 'date'],
        'genre' => ['nullable', 'in:homme,femme,non_precise'],
        'titre_poste' => ['nullable', 'string', 'max:150'],
        'niveau_etude' => ['nullable', 'string', 'max:80'],
        'domaine_etude' => ['nullable', 'string', 'max:120'],
        'competences' => ['nullable', 'string', 'max:3000'],
        'langues' => ['nullable', 'string', 'max:2000'],
        'annees_experience' => ['nullable', 'integer', 'min:0', 'max:60'],
        'objectif_professionnel' => ['nullable', 'string', 'max:2000'],
        'secteurs_interet' => ['nullable', 'string', 'max:2000'],
        'type_contrat_recherche' => ['required', 'in:cdi,cdd,stage,freelance,tous'],
        'teletravail_souhaite' => ['nullable', 'boolean'],
        'linkedin_url' => ['nullable', 'url', 'max:300'],
        'portfolio_url' => ['nullable', 'url', 'max:300'],
        'message' => ['nullable', 'string', 'max:2000'],
        'demande_redaction_cv' => ['nullable', 'boolean'],
        'demande_coaching' => ['nullable', 'boolean'],
        'demande_orientation' => ['nullable', 'boolean'],
        'cv_fichier' => ['required', 'file', 'mimes:pdf', 'max:5120'],
    ]);

    SubmissionGuard::ensureSafeRequest($request, [
        'prenom',
        'nom',
        'pays',
        'ville',
        'titre_poste',
        'niveau_etude',
        'domaine_etude',
        'competences',
        'langues',
        'objectif_professionnel',
        'secteurs_interet',
        'message',
    ]);

    $cvPath = $request->file('cv_fichier')->store('cv-depots', 'local');

    $cvDepot = CvDepot::query()->create([
        'user_id' => auth()->id(),
        ...collect($data)->except('cv_fichier')->toArray(),
        'cv_fichier' => $cvPath,
        'teletravail_souhaite' => (bool) ($data['teletravail_souhaite'] ?? false),
        'demande_redaction_cv' => (bool) ($data['demande_redaction_cv'] ?? false),
        'demande_coaching' => (bool) ($data['demande_coaching'] ?? false),
        'demande_orientation' => (bool) ($data['demande_orientation'] ?? false),
        'statut' => 'nouveau',
    ]);

    if (! empty($data['message'])) {
        $cvDepot->messages()->create([
            'sender_id' => auth()->id(),
            'sender_role' => 'user',
            'message' => $data['message'],
        ]);
    }

    Notification::send(
        NotificationRecipients::admins(),
        new PlatformDatabaseNotification([
            'title' => __('admin.notifications.events.cv_depot_received.title'),
            'message' => __('admin.notifications.events.cv_depot_received.message', [
                'name' => trim($cvDepot->prenom . ' ' . $cvDepot->nom),
            ]),
            'action_url' => route('panel.admin.cv-depots', ['cv' => $cvDepot->id]),
            'action_label' => __('admin.notifications.open'),
            'category' => 'application',
            'level' => 'info',
            'resource_type' => 'cv_depot',
            'resource_id' => $cvDepot->id,
        ])
    );

    return redirect()->to(route('cv.services.index') . '#cv-form')->with('cv_success', __('cv_services.form.success'));
})->middleware(['auth', 'verified', 'throttle:user-form'])->name('cv.services.store');

Route::post('/formations/inscription', function (Request $request) {
    $user = $request->user();
    abort_unless($user, 403);

    $data = $request->validate([
        'formation_id' => ['required', 'exists:formations,id'],
        'prenom' => ['required', 'string', 'max:80'],
        'nom' => ['required', 'string', 'max:80'],
        'email' => ['required', 'email', 'max:191', Rule::in([$user->email])],
        'telephone' => ['nullable', 'string', 'max:20'],
        'whatsapp' => ['nullable', 'string', 'max:20'],
        'pays' => ['nullable', 'string', 'max:80'],
        'profession' => ['nullable', 'string', 'max:120'],
        'niveau_etude' => ['nullable', 'string', 'max:80'],
        'motivation' => ['nullable', 'string', 'max:2000'],
    ]);

    SubmissionGuard::ensureSafeRequest($request, [
        'prenom',
        'nom',
        'pays',
        'profession',
        'niveau_etude',
        'motivation',
    ]);

    $formation = Formation::query()->findOrFail((int) $data['formation_id']);

    if (! $formation->isRegistrationOpen()) {
        return redirect()
            ->to(route('trainings.index', ['formation' => $formation->id]) . '#training-registration')
            ->withErrors(['formation_id' => __('trainings.form.closed')])
            ->withInput();
    }

    $existingRegistration = InscriptionFormation::query()
        ->visibleToUser($user)
        ->where('formation_id', $formation->id)
        ->first();

    if ($existingRegistration) {
        if ($existingRegistration->user_id === null) {
            $existingRegistration->update([
                'user_id' => $user->id,
            ]);
        }

        return redirect()
            ->to(route('trainings.index', ['formation' => $formation->id]) . '#training-registration')
            ->withErrors(['formation_id' => __('trainings.form.already_registered')])
            ->withInput();
    }

    $registration = InscriptionFormation::query()->create([
        ...$data,
        'user_id' => $user->id,
        'email' => $user->email,
        'mode_paiement' => $formation->gratuit ? 'gratuit' : 'en_attente',
        'statut_paiement' => $formation->gratuit ? 'paye' : 'en_attente',
        'statut' => 'en_attente',
        'certificat_delivre' => false,
    ]);

    if (! empty($data['motivation'])) {
        $registration->messages()->create([
            'sender_id' => $user->id,
            'sender_role' => 'user',
            'message' => $data['motivation'],
        ]);
    }

    $adminRecipients = ParametreSite::configuredEmailRecipients()->merge(
        User::query()
            ->whereHas('role', fn ($query) => $query->whereIn('nom', ['super_admin', 'admin']))
            ->pluck('email')
    )
        ->filter(fn ($email) => is_string($email) && $email !== '')
        ->unique()
        ->values();

    foreach ($adminRecipients as $recipient) {
        Mail::to($recipient)->send(new TrainingRegistrationReceivedMail($registration->fresh(['formation'])));
    }

    Notification::send(
        NotificationRecipients::admins(),
        new PlatformDatabaseNotification([
            'title' => __('admin.notifications.events.training_registration.title'),
            'message' => __('admin.notifications.events.training_registration.message', [
                'name' => trim($registration->prenom . ' ' . $registration->nom),
                'training' => $formation->titre,
            ]),
            'action_url' => route('panel.admin.training-registrations', ['registration' => $registration->id]),
            'action_label' => __('admin.notifications.open'),
            'category' => 'training',
            'level' => 'info',
            'resource_type' => 'training_registration',
            'resource_id' => $registration->id,
        ])
    );

    return redirect()->to(route('trainings.index', ['formation' => $formation->id]) . '#training-registration')->with(
        'training_success',
        __('trainings.form.success', ['formation' => $formation->titre])
    );
})->middleware(['auth', 'verified', 'throttle:user-form'])->name('trainings.register');

Route::get('/offres-opportunites', function () use ($siteSettings) {
    $settings = $siteSettings();
    $services = Service::query()
        ->with('category')
        ->where('actif', true)
        ->orderByDesc('en_vedette')
        ->orderBy('ordre')
        ->get();

    $publishedOpportunities = Opportunite::query()->where('statut', 'publie');
    $recentBeninOpportunities = (clone $publishedOpportunities)
        ->where('pays', 'Benin')
        ->orderByDesc('en_vedette')
        ->orderByDesc('date_publication')
        ->take(4)
        ->get(['id', 'slug', 'titre', 'organisation', 'lieu', 'pays', 'contrat']);

    return view('offers.index', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'services' => $services,
        'beninOpportunityCount' => (clone $publishedOpportunities)->where('pays', 'Benin')->count(),
        'remoteOpportunityCount' => (clone $publishedOpportunities)->where('teletravail', true)->count(),
        'recentBeninOpportunities' => $recentBeninOpportunities,
    ]);
})->name('offers.index');

Route::get('/offres-opportunites/{opportunite:slug}', function (Opportunite $opportunite) use ($siteSettings) {
    abort_unless($opportunite->statut === 'publie', 404);

    $settings = $siteSettings();
    $opportunite->increment('vues');
    $opportunite->refresh();
    $currentApplication = auth()->check()
        ? CandidatureOffre::query()
            ->where('user_id', auth()->id())
            ->where('opportunite_id', $opportunite->id)
            ->first()
        : null;

    $relatedOpportunities = Opportunite::query()
        ->where('statut', 'publie')
        ->whereKeyNot($opportunite->id)
        ->when(
            $opportunite->categorie_id,
            fn ($query) => $query->where('categorie_id', $opportunite->categorie_id),
            fn ($query) => $query->where('type', $opportunite->type)
        )
        ->orderByDesc('en_vedette')
        ->orderByDesc('urgent')
        ->orderByDesc('date_publication')
        ->take(3)
        ->get();

    return view('offers.show', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'opportunity' => $opportunite,
        'currentApplication' => $currentApplication,
        'relatedOpportunities' => $relatedOpportunities,
    ]);
})->name('offers.show');

Route::get('/depot-cv-services', function () use ($siteSettings) {
    $settings = $siteSettings();

    $services = Service::query()
        ->where('actif', true)
        ->orderByDesc('en_vedette')
        ->orderBy('ordre')
        ->take(4)
        ->get();

    return view('cv-services.index', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'services' => $services,
    ]);
})->name('cv.services.index');

Route::get('/formations', function (Request $request) use ($siteSettings) {
    $settings = $siteSettings();

    $trainingsQuery = Formation::query()
        ->whereIn('statut', ['ouverte', 'complete', 'terminee'])
        ->orderByDesc('en_vedette')
        ->orderBy('date_debut')
        ->orderBy('id');

    $trainings = (clone $trainingsQuery)
        ->paginate(9)
        ->withQueryString();

    $trainingOptions = (clone $trainingsQuery)->get();

    $selectedTraining = $request->filled('formation')
        ? (clone $trainingsQuery)->whereKey((int) $request->query('formation'))->first()
        : $trainings->getCollection()->first();

    if ($selectedTraining) {
        $selectedTraining->increment('vues');
        $selectedTraining->refresh();
    }

    $currentTrainingRegistration = auth()->check() && $selectedTraining
        ? InscriptionFormation::query()
            ->visibleToUser(auth()->user())
            ->where('formation_id', $selectedTraining->id)
            ->first()
        : null;

    return view('trainings.index', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'trainings' => $trainings,
        'trainingOptions' => $trainingOptions,
        'selectedTraining' => $selectedTraining,
        'currentTrainingRegistration' => $currentTrainingRegistration,
    ]);
})->name('trainings.index');

Route::get('/articles', function () use ($siteSettings) {
    $settings = $siteSettings();

    return view('articles.index', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
    ]);
})->name('articles.index');

Route::get('/articles/{article:slug}', function (BlogArticle $article) use ($siteSettings) {
    abort_unless($article->statut === 'publie', 404);

    $settings = $siteSettings();
    $article->increment('vues');
    $article->refresh();

    $approvedComments = $article->commentaires()
        ->with('user')
        ->where('statut', 'approuve')
        ->orderBy('created_at')
        ->get();

    $replyComment = null;

    if (request()->filled('reply')) {
        $replyComment = $approvedComments->firstWhere('id', (int) request()->query('reply'));
    }

    $relatedArticles = BlogArticle::query()
        ->with(['category', 'featuredImage'])
        ->where('statut', 'publie')
        ->whereKeyNot($article->id)
        ->when(
            $article->categorie_id,
            fn ($query) => $query->where('categorie_id', $article->categorie_id)
        )
        ->orderByDesc('en_vedette')
        ->orderByDesc('publie_le')
        ->take(3)
        ->get();

    return view('articles.show', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'article' => $article->load(['category', 'images', 'featuredImage']),
        'approvedComments' => $approvedComments,
        'replyComment' => $replyComment,
        'relatedArticles' => $relatedArticles,
    ]);
})->name('articles.show');

Route::post('/articles/{article:slug}/commentaires', function (Request $request, BlogArticle $article) {
    abort_unless($article->statut === 'publie', 404);
    abort_unless($article->commentaires_actifs, 403);

    $user = $request->user();

    if (! $user) {
        return redirect()->route('login', [
            'redirect_to' => route('articles.show', $article->slug) . '#article-comments',
        ]);
    }

    if (! $user->hasVerifiedEmail()) {
        return redirect()->route('verification.notice');
    }

    $data = $request->validate([
        'parent_id' => [
            'nullable',
            'integer',
            function (string $attribute, mixed $value, \Closure $fail) use ($article) {
                if ($value === null || $value === '') {
                    return;
                }

                $parentComment = BlogCommentaire::query()
                    ->where('article_id', $article->id)
                    ->where('statut', 'approuve')
                    ->find($value);

                if (! $parentComment) {
                    $fail(__('articles.comments.validation.parent'));
                }
            },
        ],
        'contenu' => ['required', 'string', 'max:4000'],
    ]);

    SubmissionGuard::ensureSafeRequest($request, ['contenu']);

    $parentId = $data['parent_id'] ?? null;

    BlogCommentaire::query()->create([
        'article_id' => $article->id,
        'user_id' => $user->id,
        'parent_id' => $parentId !== null && $parentId !== '' ? (int) $parentId : null,
        'auteur_nom' => $user->fullName(),
        'auteur_email' => $user->email,
        'contenu' => $data['contenu'],
        'ip_address' => $request->ip(),
        'statut' => 'en_attente',
    ]);

    return redirect()
        ->to(route('articles.show', $article->slug) . '#article-comments')
        ->with('article_comment_success', __('articles.comments.success'));
})->middleware('throttle:user-form')->name('articles.comments.store');

Route::get('/contact-priere', function () use ($siteSettings) {
    $settings = $siteSettings();
    $user = auth()->user();
    $ipAddress = request()->ip();

    $displayVerses = Verset::query()
        ->where('actif', true)
        ->where('afficher_accueil', true)
        ->orderBy('ordre')
        ->latest('updated_at')
        ->take(2)
        ->get();

    $featuredVerse = $displayVerses->first();
    $featuredDailyPrayer = SpiritualPublication::query()
        ->ofType('priere_jour')
        ->visible()
        ->where('afficher_accueil', true)
        ->orderBy('ordre')
        ->latest('published_at')
        ->first();

    $prayerEncouragement = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'encouragement')
        ->latest()
        ->first();

    $prayerRequests = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'priere')
        ->latest()
        ->take(6)
        ->get();

    $approvedPrayerTotal = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'priere')
        ->count();

    $approvedPrayerSupportTotal = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'priere')
        ->sum('priants');

    $supportedPrayerIds = PriereSoutien::query()
        ->whereIn('priere_id', $prayerRequests->pluck('id'))
        ->when(
            $user,
            fn ($query) => $query->where('user_id', $user->id),
            fn ($query) => $query->whereNull('user_id')->where('ip_address', $ipAddress)
        )
        ->pluck('priere_id')
        ->map(fn ($id) => (int) $id)
        ->all();

    return view('contact-prayer.index', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'featuredVerse' => $featuredVerse,
        'featuredDailyPrayer' => $featuredDailyPrayer,
        'prayerEncouragement' => $prayerEncouragement,
        'prayerRequests' => $prayerRequests,
        'approvedPrayerTotal' => $approvedPrayerTotal,
        'approvedPrayerSupportTotal' => (int) $approvedPrayerSupportTotal,
        'supportedPrayerIds' => $supportedPrayerIds,
    ]);
})->name('contact.prayer.index');

Route::get('/mur-de-priere/communaute', function () use ($siteSettings) {
    $settings = $siteSettings();
    $user = auth()->user();
    $ipAddress = request()->ip();

    $prayerRequests = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'priere')
        ->latest()
        ->paginate(12);

    $approvedPrayerTotal = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'priere')
        ->count();

    $approvedPrayerSupportTotal = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'priere')
        ->sum('priants');

    $supportedPrayerIds = PriereSoutien::query()
        ->whereIn('priere_id', $prayerRequests->getCollection()->pluck('id'))
        ->when(
            $user,
            fn ($query) => $query->where('user_id', $user->id),
            fn ($query) => $query->whereNull('user_id')->where('ip_address', $ipAddress)
        )
        ->pluck('priere_id')
        ->map(fn ($id) => (int) $id)
        ->all();

    return view('community.prayers', [
        'title' => __('community.prayers.meta.title'),
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'prayerRequests' => $prayerRequests,
        'approvedPrayerTotal' => $approvedPrayerTotal,
        'approvedPrayerSupportTotal' => (int) $approvedPrayerSupportTotal,
        'supportedPrayerIds' => $supportedPrayerIds,
    ]);
})->name('community.prayers.index');

Route::get('/temoignages/communaute', function () use ($siteSettings) {
    $settings = $siteSettings();

    $testimonials = Temoignage::query()
        ->where('statut', 'approuve')
        ->orderByDesc('en_vedette')
        ->orderBy('ordre')
        ->latest()
        ->paginate(9);

    $approvedTestimonialTotal = Temoignage::query()
        ->where('statut', 'approuve')
        ->count();

    $featuredTestimonialTotal = Temoignage::query()
        ->where('statut', 'approuve')
        ->where('en_vedette', true)
        ->count();

    return view('community.testimonials', [
        'title' => __('community.testimonials.meta.title'),
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'testimonials' => $testimonials,
        'approvedTestimonialTotal' => $approvedTestimonialTotal,
        'featuredTestimonialTotal' => $featuredTestimonialTotal,
    ]);
})->name('community.testimonials.index');

Route::get('/versets-bibliques', function () use ($siteSettings) {
    $settings = $siteSettings();

    return view('spiritual.verses', [
        'title' => app()->getLocale() === 'fr' ? 'Versets bibliques' : 'Bible verses',
        'description' => app()->getLocale() === 'fr'
            ? 'Une sélection de versets bibliques à lire, méditer et partager.'
            : 'A selection of active Bible verses to read, reflect on, and share.',
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'verses' => Verset::query()
            ->where('actif', true)
            ->orderByDesc('afficher_accueil')
            ->orderBy('ordre')
            ->latest('updated_at')
            ->get(),
    ]);
})->name('spiritual.verses.index');

Route::get('/versets-bibliques/{verse}', function (Verset $verse) use ($siteSettings) {
    abort_unless($verse->actif, 404);

    $settings = $siteSettings();
    $detailUrl = Seo::localizedUrl(route('spiritual.verses.show', $verse), app()->getLocale());

    return view('spiritual.show', [
        'kicker' => app()->getLocale() === 'fr' ? 'Verset biblique' : 'Bible verse',
        'title' => $verse->reference,
        'description' => Seo::description($verse->texte, 170),
        'canonical' => $detailUrl,
        'backUrl' => Seo::localizedUrl(route('spiritual.verses.index'), app()->getLocale()),
        'backLabel' => app()->getLocale() === 'fr' ? 'Retour aux versets' : 'Back to verses',
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'reference' => $verse->reference,
        'excerpt' => null,
        'content' => $verse->texte,
        'metaLabel' => app()->getLocale() === 'fr' ? 'Version' : 'Version',
        'metaValue' => $verse->version,
        'author' => null,
        'shareUrl' => $detailUrl,
        'shareTitle' => $verse->reference,
        'shareText' => $verse->texte . ' - ' . $verse->version,
    ]);
})->whereNumber('verse')->name('spiritual.verses.show');

Route::get('/pensees-du-jour', function () use ($siteSettings) {
    $settings = $siteSettings();

    return view('spiritual.index', [
        'kicker' => app()->getLocale() === 'fr' ? 'Pensée du jour' : 'Thought of the day',
        'title' => app()->getLocale() === 'fr' ? 'Pensées du jour' : 'Thoughts of the day',
        'description' => app()->getLocale() === 'fr'
            ? 'Des méditations courtes et claires pour nourrir votre foi au quotidien.'
            : 'Short and clear meditations to nourish your faith each day.',
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'detailRouteName' => 'spiritual.thoughts.show',
        'items' => SpiritualPublication::query()->ofType('pensee')->visible()->orderByDesc('afficher_accueil')->orderBy('ordre')->latest('published_at')->get(),
    ]);
})->name('spiritual.thoughts.index');

Route::get('/pensees-du-jour/{publication:slug}', function (SpiritualPublication $publication) use ($siteSettings) {
    abort_unless($publication->actif && $publication->type === 'pensee', 404);

    $settings = $siteSettings();
    $detailUrl = Seo::localizedUrl(route('spiritual.thoughts.show', $publication->slug), app()->getLocale());

    return view('spiritual.show', [
        'kicker' => app()->getLocale() === 'fr' ? 'Pensée du jour' : 'Thought of the day',
        'title' => $publication->titre,
        'description' => Seo::description($publication->extrait ?: $publication->contenu, 170),
        'canonical' => $detailUrl,
        'backUrl' => Seo::localizedUrl(route('spiritual.thoughts.index'), app()->getLocale()),
        'backLabel' => app()->getLocale() === 'fr' ? 'Retour aux pensées du jour' : 'Back to thoughts of the day',
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'reference' => $publication->reference,
        'excerpt' => $publication->extrait,
        'content' => $publication->contenu,
        'metaLabel' => app()->getLocale() === 'fr' ? 'Auteur' : 'Author',
        'metaValue' => $publication->auteur,
        'author' => $publication->auteur,
        'shareUrl' => $detailUrl,
        'shareTitle' => $publication->titre,
        'shareText' => $publication->extrait ?: Seo::description($publication->contenu, 180),
    ]);
})->name('spiritual.thoughts.show');

Route::get('/exhortations', function () use ($siteSettings) {
    $settings = $siteSettings();

    return view('spiritual.index', [
        'kicker' => app()->getLocale() === 'fr' ? 'Exhortation' : 'Exhortation',
        'title' => app()->getLocale() === 'fr' ? 'Exhortations' : 'Exhortations',
        'description' => app()->getLocale() === 'fr'
            ? "Des exhortations pour persévérer dans la foi et l'espérance."
            : 'Encouragements to persevere in faith and endurance.',
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'detailRouteName' => 'spiritual.exhortations.show',
        'items' => SpiritualPublication::query()->ofType('exhortation')->visible()->orderByDesc('afficher_accueil')->orderBy('ordre')->latest('published_at')->get(),
    ]);
})->name('spiritual.exhortations.index');

Route::get('/exhortations/{publication:slug}', function (SpiritualPublication $publication) use ($siteSettings) {
    abort_unless($publication->actif && $publication->type === 'exhortation', 404);

    $settings = $siteSettings();
    $detailUrl = Seo::localizedUrl(route('spiritual.exhortations.show', $publication->slug), app()->getLocale());

    return view('spiritual.show', [
        'kicker' => app()->getLocale() === 'fr' ? 'Exhortation' : 'Exhortation',
        'title' => $publication->titre,
        'description' => Seo::description($publication->extrait ?: $publication->contenu, 170),
        'canonical' => $detailUrl,
        'backUrl' => Seo::localizedUrl(route('spiritual.exhortations.index'), app()->getLocale()),
        'backLabel' => app()->getLocale() === 'fr' ? 'Retour aux exhortations' : 'Back to exhortations',
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'reference' => $publication->reference,
        'excerpt' => $publication->extrait,
        'content' => $publication->contenu,
        'metaLabel' => app()->getLocale() === 'fr' ? 'Auteur' : 'Author',
        'metaValue' => $publication->auteur,
        'author' => $publication->auteur,
        'shareUrl' => $detailUrl,
        'shareTitle' => $publication->titre,
        'shareText' => $publication->extrait ?: Seo::description($publication->contenu, 180),
    ]);
})->name('spiritual.exhortations.show');

Route::get('/prieres-du-jour', function () use ($siteSettings) {
    $settings = $siteSettings();

    return view('spiritual.index', [
        'kicker' => app()->getLocale() === 'fr' ? 'Prière du jour' : 'Prayer of the day',
        'title' => app()->getLocale() === 'fr' ? 'Prières du jour' : 'Daily prayers',
        'description' => app()->getLocale() === 'fr'
            ? 'Des prières écrites pour vous aider à prier avec foi et simplicité.'
            : 'Written prayers to help you pray with faith and simplicity.',
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'items' => SpiritualPublication::query()->ofType('priere_jour')->visible()->orderByDesc('afficher_accueil')->orderBy('ordre')->latest('published_at')->get(),
    ]);
})->name('spiritual.daily-prayers.index');

Route::get('/se-convertir', function () use ($siteSettings) {
    $settings = $siteSettings();

    return view('spiritual.conversion', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
    ]);
})->name('spiritual.conversion.index');

Route::get('/', function () use ($siteSettings) {
    $settings = $siteSettings();

    $banner = Banniere::query()
        ->where('actif', true)
        ->orderBy('ordre')
        ->first();

    $services = Service::query()
        ->where('actif', true)
        ->whereIn('type', ['redaction_cv', 'coaching', 'orientation', 'accompagnement'])
        ->orderByDesc('en_vedette')
        ->orderBy('ordre')
        ->get()
        ->keyBy('type');

    $featuredFormation = Formation::query()
        ->whereIn('statut', ['ouverte', 'complete', 'terminee'])
        ->orderByDesc('en_vedette')
        ->orderBy('date_debut')
        ->first();

    $latestOpportunities = Opportunite::query()
        ->where('statut', 'publie')
        ->orderByDesc('en_vedette')
        ->orderByDesc('date_publication')
        ->take(3)
        ->get();

    $latestArticles = BlogArticle::query()
        ->with(['category', 'featuredImage'])
        ->where('statut', 'publie')
        ->orderByDesc('publie_le')
        ->take(3)
        ->get();

    $testimonials = Temoignage::query()
        ->where('statut', 'approuve')
        ->orderByDesc('en_vedette')
        ->orderBy('ordre')
        ->latest()
        ->take(3)
        ->get();

    $prayerEncouragement = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'encouragement')
        ->latest()
        ->first();

    $approvedPrayerRequests = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'priere')
        ->latest()
        ->take(8)
        ->get();

    $prayerTestimonies = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'temoignage_reponse')
        ->latest()
        ->take(4)
        ->get();

    $prayerRequestCount = MurDePriere::query()
        ->where('statut', 'approuve')
        ->where('type', 'priere')
        ->count();

    $displayVerses = Verset::query()
        ->where('actif', true)
        ->orderByDesc('afficher_accueil')
        ->orderBy('ordre')
        ->latest('updated_at')
        ->take(3)
        ->get();
    $homeThoughts = SpiritualPublication::query()
        ->ofType('pensee')
        ->visible()
        ->orderByDesc('afficher_accueil')
        ->orderBy('ordre')
        ->latest('updated_at')
        ->take(3)
        ->get();
    $homeExhortations = SpiritualPublication::query()
        ->ofType('exhortation')
        ->visible()
        ->orderByDesc('afficher_accueil')
        ->orderBy('ordre')
        ->latest('updated_at')
        ->take(3)
        ->get();
    $homeDailyPrayers = SpiritualPublication::query()
        ->ofType('priere_jour')
        ->visible()
        ->orderByDesc('afficher_accueil')
        ->orderBy('ordre')
        ->latest('updated_at')
        ->take(3)
        ->get();

    $featuredVerse = $displayVerses->first();
    $featuredThought = $homeThoughts->first();
    $featuredExhortation = $homeExhortations->first();
    $featuredDailyPrayer = $homeDailyPrayers->first();

    return view('welcome', [
        'siteName' => $settings->get('site_nom')?->valeur ?? 'Opportunet Mondiale',
        'siteSlogan' => $settings->get('site_slogan')?->valeur ?? __('home.hero.badge'),
        'siteEmail' => $settings->get('site_email')?->valeur ?? 'contact@opportunetmondiale.com',
        'siteHours' => $settings->get('site_horaires')?->valeur ?? 'Lundi - Samedi 08:00 - 22:00',
        'siteAddress' => $settings->get('site_adresse')?->valeur ?? "En face de la Mairie de Miss\u{00E9}r\u{00E9}t\u{00E9}, Ou\u{00E9}m\u{00E9}, BJ",
        'siteWhatsapp' => $settings->get('whatsapp_numero')?->valeur ?? '+2290167229575',
        'siteWhatsappMessage' => $settings->get('whatsapp_message_defaut')?->valeur ?? __('home.forms.whatsapp_default'),
        'supportKkiapayUrl' => $settings->get('support_kkiapay_url')?->valeur ?? '',
        'supportBankName' => $settings->get('support_bank_name')?->valeur ?? '',
        'supportBankAccountName' => $settings->get('support_bank_account_name')?->valeur ?? '',
        'supportBankAccountNumber' => $settings->get('support_bank_account_number')?->valeur ?? '',
        'supportBankIban' => $settings->get('support_bank_iban')?->valeur ?? '',
        'supportBankSwift' => $settings->get('support_bank_swift')?->valeur ?? '',
        'socialLinks' => [
            'facebook' => $settings->get('facebook_url')?->valeur,
            'instagram' => $settings->get('instagram_url')?->valeur,
            'linkedin' => $settings->get('linkedin_url')?->valeur,
            'youtube' => $settings->get('youtube_url')?->valeur,
            'tiktok' => $settings->get('tiktok_url')?->valeur,
        ],
        'banner' => $banner,
        'services' => $services,
        'featuredFormation' => $featuredFormation,
        'latestOpportunities' => $latestOpportunities,
        'latestArticles' => $latestArticles,
        'testimonials' => $testimonials,
        'prayerEncouragement' => $prayerEncouragement,
        'approvedPrayerRequests' => $approvedPrayerRequests,
        'prayerTestimonies' => $prayerTestimonies,
        'prayerRequestCount' => $prayerRequestCount,
        'featuredVerse' => $featuredVerse,
        'featuredThought' => $featuredThought,
        'featuredExhortation' => $featuredExhortation,
        'featuredDailyPrayer' => $featuredDailyPrayer,
        'headerVerses' => $displayVerses,
        'welcomeVerses' => $displayVerses,
        'welcomeThoughts' => $homeThoughts,
        'welcomeExhortations' => $homeExhortations,
        'welcomeDailyPrayers' => $homeDailyPrayers,
    ]);
})->name('home');
