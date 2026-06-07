@php
    $siteName = $siteName ?? 'Opportunet Mondiale';
    $siteSlogan = $siteSlogan ?? __('home.hero.badge');
    $siteEmail = $siteEmail ?? 'contact@opportunetmondiale.com';
    $siteHours = $siteHours ?? 'Lundi - Samedi 08:00 - 22:00';
    $siteAddress = $siteAddress ?? 'En face de la Mairie de Missérété, Ouémé, BJ';
    $siteWhatsapp = $siteWhatsapp ?? '+2290167229575';
    $siteWhatsappMessage = $siteWhatsappMessage ?? __('home.forms.whatsapp_default');
    $seoTitle = app()->getLocale() === 'fr'
        ? 'Articles sur emploi, foi et progression personnelle'
        : __('articles.meta.title');
    $hasActiveFilters = request()->filled('q') || request()->filled('category');
    $seoDescription = \App\Support\Seo::description(__('articles.meta.description') !== 'articles.meta.description'
        ? __('articles.meta.description')
        : __('articles.page.subtitle'));
    $seoKeywords = __('articles.meta.keywords') !== 'articles.meta.keywords' ? __('articles.meta.keywords') : null;
    $seoSchema = [
        \App\Support\Seo::breadcrumb([
            ['name' => $siteName, 'url' => \App\Support\Seo::localizedUrl(route('home'), app()->getLocale())],
            ['name' => __('articles.page.label'), 'url' => \App\Support\Seo::localizedUrl(route('articles.index'), app()->getLocale())],
        ]),
        \App\Support\Seo::schema('CollectionPage', [
            'name' => __('articles.meta.title'),
            'url' => \App\Support\Seo::localizedUrl(route('articles.index'), app()->getLocale()),
            'description' => $seoDescription,
            'inLanguage' => app()->getLocale(),
        ]),
    ];
@endphp

<x-layouts.app
    :title="$seoTitle"
    :description="$seoDescription"
    :keywords="$seoKeywords"
    :canonical="\App\Support\Seo::localizedUrl(route('articles.index'), app()->getLocale())"
    :robots="$hasActiveFilters ? 'noindex,follow' : 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1'"
    :schema-data="$seoSchema"
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
