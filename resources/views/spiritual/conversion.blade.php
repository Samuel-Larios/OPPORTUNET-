@php
    $isFrench = app()->getLocale() === 'fr';
    $content = $isFrench
        ? [
            'kicker' => 'Se convertir',
            'title' => 'Faire un pas vers Jésus-Christ',
            'subtitle' => "Comprendre l'Évangile, accueillir Jésus-Christ et commencer une vie nouvelle avec Dieu.",
            'lead_title' => 'Que signifie se convertir ?',
            'lead_text' => 'Se convertir, c’est se tourner vers Dieu avec foi, reconnaître Jésus-Christ comme Sauveur et commencer une vie nouvelle conduite par sa parole.',
            'steps' => [
                'Reconnaissez votre besoin de salut et votre désir de revenir à Dieu.',
                'Croyez que Jésus-Christ est mort et ressuscité pour vous sauver.',
                'Priez avec sincérité et confiez votre vie au Seigneur.',
                'Cherchez un accompagnement, lisez la Bible et rapprochez-vous d’une communauté chrétienne sérieuse.',
            ],
            'prayer_title' => 'Une prière simple',
            'prayer_text' => 'Seigneur Jésus, je viens à toi avec foi. Pardonne mes péchés, change mon cœur et conduis ma vie. Je crois en toi et je veux te suivre. Amen.',
            'cta_primary' => 'Demander un accompagnement',
            'cta_secondary' => 'Nous écrire sur WhatsApp',
        ]
        : [
            'kicker' => 'Convert',
            'title' => 'Take a step toward Jesus Christ',
            'subtitle' => 'Understand the Gospel, receive Jesus Christ, and begin a new life with God.',
            'lead_title' => 'What does conversion mean?',
            'lead_text' => 'Conversion means turning to God in faith, recognizing Jesus Christ as Savior, and beginning a new life shaped by His word.',
            'steps' => [
                'Recognize your need for salvation and your desire to return to God.',
                'Believe that Jesus Christ died and rose again to save you.',
                'Pray sincerely and entrust your life to the Lord.',
                'Seek guidance, read the Bible, and stay connected to a serious Christian community.',
            ],
            'prayer_title' => 'A simple prayer',
            'prayer_text' => 'Lord Jesus, I come to you in faith. Forgive my sins, change my heart, and lead my life. I believe in you and I want to follow you. Amen.',
            'cta_primary' => 'Request guidance',
            'cta_secondary' => 'Message us on WhatsApp',
        ];
@endphp

<x-layouts.app
    :title="$content['title']"
    :description="$content['subtitle']"
    :canonical="\App\Support\Seo::localizedUrl(url()->current(), app()->getLocale())"
    :page-banner-title="$content['title']"
    :site-name="$siteName"
    :site-slogan="$siteSlogan"
    :site-email="$siteEmail"
    :site-hours="$siteHours"
    :site-address="$siteAddress"
    :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage"
    :show-hero="false"
>
    <main class="home-strip">
        <section class="home-contact">
            <div class="container">
                <div class="contact-shell reveal visible">
                    <div class="contact-copy">
                        <span class="section-label">{{ $content['kicker'] }}</span>
                        <h1 class="section-title">{{ $content['title'] }}</h1>
                        <p class="section-sub">{{ $content['subtitle'] }}</p>

                        <div class="contact-cards">
                            @foreach ($content['steps'] as $step)
                                <article class="contact-info-card">
                                    <span>{{ $content['kicker'] }}</span>
                                    <strong>{{ $step }}</strong>
                                </article>
                            @endforeach
                        </div>
                    </div>

                    <div class="contact-form-wrap">
                        <div class="contact-form-card" style="display: grid; gap: 18px;">
                            <div>
                                <span class="section-label">{{ $content['lead_title'] }}</span>
                                <p style="margin-top: 12px;">{{ $content['lead_text'] }}</p>
                            </div>

                            <div>
                                <span class="section-label">{{ $content['prayer_title'] }}</span>
                                <p style="margin-top: 12px; line-height: 1.9;">{{ $content['prayer_text'] }}</p>
                            </div>

                            <div class="contact-form-actions">
                                <a href="{{ route('contact.prayer.index') }}" class="solid-submit">{{ $content['cta_primary'] }}</a>
                                <a href="{{ 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp) . '?text=' . urlencode($content['title']) }}" class="ghost-submit" target="_blank" rel="noopener">{{ $content['cta_secondary'] }}</a>
                            </div>

                            <x-share-buttons :url="\App\Support\Seo::localizedUrl(url()->current(), app()->getLocale())" :title="$content['title']" />
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
