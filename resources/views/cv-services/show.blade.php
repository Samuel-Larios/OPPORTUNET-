@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $whatsappBase = 'https://wa.me/' . preg_replace('/\D+/', '', $siteWhatsapp ?? '');
    $whatsappMessage = $service->whatsapp_message ?: __('cv_services.whatsapp.default_service', ['service' => $service->titre]);
    $whatsappHref = $whatsappBase . '?text=' . urlencode($whatsappMessage);
    $description = $service->description_longue ?: $service->description_courte;
    $canonical = \App\Support\Seo::localizedUrl(route('cv.services.show', $service->slug), app()->getLocale());
@endphp

<x-layouts.app :title="$service->titre . ' | ' . $siteName" :description="\App\Support\Seo::description($service->description_courte)" :canonical="$canonical" :image="$service->publicImageUrl()"
    :site-name="$siteName" :site-slogan="$siteSlogan" :site-email="$siteEmail" :site-hours="$siteHours" :site-address="$siteAddress" :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage" :show-hero="false">
    <main class="cv-services-page">
        <section class="cv-services-hero">
            <div class="container">
                <div class="cv-services-hero-shell reveal">
                    <div class="cv-services-hero-copy">
                        <span class="section-label">{{ __('cv_services.services.label') }}</span>
                        <h1 class="section-title">{{ $service->titre }}</h1>
                        <p class="section-sub">{{ $service->description_courte }}</p>
                    </div>
                    <div class="cv-services-hero-actions">
                        <a href="{{ $whatsappHref }}" class="solid-submit" target="_blank" rel="noopener">{{ __('cv_services.services.whatsapp_cta') }}</a>
                        <a href="{{ route('cv.services.index') }}" class="ghost-submit">{{ __('cv_services.services.back_cta') }}</a>
                    </div>
                </div>
            </div>
        </section>

        <section class="service-detail-section">
            <div class="container">
                <article class="service-detail-card {{ $service->publicImageUrl() ? '' : 'service-detail-card--without-image' }} reveal">
                    @if ($service->publicImageUrl())
                        <img src="{{ $service->publicImageUrl() }}" alt="{{ $service->titre }}" class="service-detail-image" />
                    @endif
                    <div>
                        <div class="service-detail-meta">
                            <span>{{ $service->duree ?: __('cv_services.services.meta_default') }}</span>
                            <span>{{ $service->prix ? number_format((float) $service->prix, 0, ',', ' ') . ' ' . $service->devise : __('cv_services.services.on_request') }}</span>
                        </div>
                        <div class="service-detail-content">{!! nl2br(e($description)) !!}</div>
                    </div>
                </article>
            </div>
        </section>
    </main>
</x-layouts.app>
