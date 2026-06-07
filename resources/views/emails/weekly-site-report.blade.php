@php
    $logoFile = public_path('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $logoSrc = isset($message) && file_exists($logoFile)
        ? $message->embed($logoFile)
        : asset('images/logo/imgi_24_cropped-cropped-Logo-OPM-1-300x213.png');
    $traffic = $report['traffic'];
    $activity = $report['activity'];
    $totals = $report['totals'];
@endphp

<x-email.layout
    :title="'Rapport hebdomadaire du site'"
    heading="Rapport hebdomadaire du site"
    eyebrow="Suivi hebdomadaire"
    :logo-src="$logoSrc"
>
    <p>
        Voici le rapport hebdomadaire des visites et de l’activité du site pour la période du
        <strong>{{ $report['period_start']->format('d/m/Y') }}</strong> au
        <strong>{{ $report['period_end']->format('d/m/Y') }}</strong>.
    </p>

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <p style="margin:0 0 10px;"><strong>Visites totales :</strong> {{ number_format($traffic['total_visits'], 0, ',', ' ') }}</p>
        <p style="margin:0 0 10px;"><strong>Visiteurs uniques :</strong> {{ number_format($traffic['unique_visitors'], 0, ',', ' ') }}</p>
        <p style="margin:0;"><strong>Visites depuis un compte connecté :</strong> {{ number_format($traffic['connected_visits'], 0, ',', ' ') }}</p>
    </div>

    <div style="margin:24px 0; padding:20px; background:#fff8ef; border:1px solid #f0dec4; border-radius:18px;">
        <p style="margin:0 0 12px;"><strong>Activité de la semaine</strong></p>
        <ul style="margin:0; padding-left:18px;">
            <li>Nouveaux utilisateurs : {{ number_format($activity['new_users'], 0, ',', ' ') }}</li>
            <li>Abonnements newsletter : {{ number_format($activity['newsletter_subscribers'], 0, ',', ' ') }}</li>
            <li>Contacts reçus : {{ number_format($activity['contacts'], 0, ',', ' ') }}</li>
            <li>Dépôts CV / services : {{ number_format($activity['cv_depots'], 0, ',', ' ') }}</li>
            <li>Candidatures à des offres : {{ number_format($activity['offer_applications'], 0, ',', ' ') }}</li>
            <li>Inscriptions formations : {{ number_format($activity['training_registrations'], 0, ',', ' ') }}</li>
            <li>Sujets de prière : {{ number_format($activity['prayer_requests'], 0, ',', ' ') }}</li>
            <li>Témoignages : {{ number_format($activity['testimonials'], 0, ',', ' ') }}</li>
            <li>Articles publiés : {{ number_format($activity['articles_published'], 0, ',', ' ') }}</li>
            <li>Offres publiées : {{ number_format($activity['offers_published'], 0, ',', ' ') }}</li>
            <li>Formations ajoutées : {{ number_format($activity['trainings_published'], 0, ',', ' ') }}</li>
            <li>Commentaires d’articles : {{ number_format($activity['article_comments'], 0, ',', ' ') }}</li>
        </ul>
    </div>

    @if (collect($traffic['top_pages'])->isNotEmpty())
        <div style="margin:24px 0; padding:20px; background:#f7f4ff; border:1px solid #ddd4ff; border-radius:18px;">
            <p style="margin:0 0 12px;"><strong>Pages les plus consultées</strong></p>
            <ul style="margin:0; padding-left:18px;">
                @foreach ($traffic['top_pages'] as $page)
                    <li>{{ $page['label'] }} : {{ number_format($page['count'], 0, ',', ' ') }} visites</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (collect($traffic['visits_by_day'])->isNotEmpty())
        <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
            <p style="margin:0 0 12px;"><strong>Visites par jour</strong></p>
            <ul style="margin:0; padding-left:18px;">
                @foreach ($traffic['visits_by_day'] as $day)
                    <li>{{ $day['date']->format('d/m') }} : {{ number_format($day['count'], 0, ',', ' ') }} visites</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="margin:24px 0; padding:20px; background:#f5fbfb; border:1px solid #dceceb; border-radius:18px;">
        <p style="margin:0 0 12px;"><strong>Totaux actuels de la plateforme</strong></p>
        <ul style="margin:0; padding-left:18px;">
            <li>Utilisateurs : {{ number_format($totals['users'], 0, ',', ' ') }}</li>
            <li>Offres : {{ number_format($totals['offers'], 0, ',', ' ') }}</li>
            <li>Articles : {{ number_format($totals['articles'], 0, ',', ' ') }}</li>
            <li>Formations : {{ number_format($totals['trainings'], 0, ',', ' ') }}</li>
            <li>Abonnés newsletter actifs : {{ number_format($totals['newsletter_subscribers'], 0, ',', ' ') }}</li>
        </ul>
    </div>

    <p>Ce rapport est généré automatiquement chaque fin de semaine pour l’équipe Opportunet Mondiale.</p>
</x-email.layout>
