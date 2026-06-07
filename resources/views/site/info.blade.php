@php
    $seoDescription = \App\Support\Seo::description($description ?? $intro ?? $title);
@endphp

<x-layouts.app
    :title="$title"
    :description="$seoDescription"
    :canonical="\App\Support\Seo::localizedUrl(url()->current(), app()->getLocale())"
    :show-hero="false"
>
    <main class="articles-page">
        <section class="offers-detail-content">
            <div class="container">
                <div class="article-detail-layout">
                    <article class="article-detail-main reveal">
                        <div class="home-section-head">
                            <span class="section-label">Opportunet Mondiale</span>
                            <h1 class="section-title">{{ $title }}</h1>
                            <p class="section-sub">{{ $intro }}</p>
                        </div>

                        @foreach ($sections as $section)
                            <div class="offers-detail-section">
                                <h2>{{ $section['title'] }}</h2>
                                <p>{{ $section['body'] }}</p>
                            </div>
                        @endforeach
                    </article>

                    <aside class="article-detail-aside reveal reveal-delay-1">
                        <article class="offers-detail-panel">
                            <h2>Navigation</h2>
                            <ul class="offers-detail-summary">
                                <li>
                                    <span>Accueil</span>
                                    <strong><a href="{{ \App\Support\Seo::localizedUrl(route('home'), app()->getLocale()) }}">Opportunet Mondiale</a></strong>
                                </li>
                                <li>
                                    <span>Contact</span>
                                    <strong><a href="{{ \App\Support\Seo::localizedUrl(route('contact.prayer.index'), app()->getLocale()) }}">Contact et prière</a></strong>
                                </li>
                                <li>
                                    <span>Opportunités</span>
                                    <strong><a href="{{ \App\Support\Seo::localizedUrl(route('offers.index'), app()->getLocale()) }}">Offres & opportunités</a></strong>
                                </li>
                            </ul>
                        </article>
                    </aside>
                </div>
            </div>
        </section>
    </main>
</x-layouts.app>
