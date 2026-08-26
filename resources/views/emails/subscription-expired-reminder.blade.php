<x-email.layout title="Renouvelez votre abonnement" heading="Votre accès premium a expiré" eyebrow="Abonnement offres premium">
    <p>Bonjour {{ $subscription->user->fullName() }},</p>

    <p>
        Votre accès aux offres premium a expiré le
        <strong>{{ $subscription->expires_at->locale('fr')->translatedFormat('d F Y à H:i') }}</strong>.
    </p>

    <p>Renouvelez votre abonnement pour consulter toutes les offres premium et y postuler à nouveau.</p>

    <p style="margin:28px 0;">
        <a href="{{ route('subscriptions.index') }}" style="display:inline-block; padding:14px 22px; border-radius:999px; background:#1a7a6e; color:#ffffff; font-weight:700; text-decoration:none;">Renouveler mon abonnement</a>
    </p>

    <p>Opportunet Mondiale</p>
</x-email.layout>
