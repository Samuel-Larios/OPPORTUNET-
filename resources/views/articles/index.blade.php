@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+229XXXXXXXXX';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
@endphp

<x-layouts.app
    :title="__('articles.meta.title')"
    :site-name="$siteName"
    :site-slogan="$siteSlogan"
    :site-email="$siteEmail"
    :site-hours="$siteHours"
    :site-address="$siteAddress"
    :site-whatsapp="$siteWhatsapp"
    :site-whatsapp-message="$siteWhatsappMessage"
    :show-hero="false"
>
    <main class="articles-page">
        <livewire:articles-index />
    </main>
</x-layouts.app>
