<div class="panel-stack">
    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_testimonials.list_title') }}</h2>
                <p>{{ __('admin.user_testimonials.intro') }}</p>
            </div>

            <div class="panel-list">
                @forelse ($testimonials as $testimonial)
                    <button type="button" wire:click="selectTestimonial({{ $testimonial->id }})" class="panel-application-item{{ $selectedTestimonial && $selectedTestimonial->id === $testimonial->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $testimonial->typeLabel() }}</strong>
                            <span>{{ $testimonial->created_at->format('d/m/Y') }}</span>
                            <span>{{ $testimonial->note ? $testimonial->note . '/5' : '-' }}</span>
                        </div>
                        <span class="panel-badge{{ $testimonial->en_vedette ? '' : ' is-muted' }}">{{ $testimonial->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.user_testimonials.empty') }}</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            @if ($selectedTestimonial)
                <div class="panel-card-head">
                    <h2>{{ __('admin.user_testimonials.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.user_testimonials.labels.status') }}</span>
                            <strong>{{ $selectedTestimonial->statusLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_testimonials.labels.type') }}</span>
                            <strong>{{ $selectedTestimonial->typeLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_testimonials.labels.rating') }}</span>
                            <strong>{{ $selectedTestimonial->note ? $selectedTestimonial->note . '/5' : '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_testimonials.labels.submitted') }}</span>
                            <strong>{{ $selectedTestimonial->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_testimonials.labels.content') }}</strong>
                        <p style="margin-top: 12px;">{{ $selectedTestimonial->contenu }}</p>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_testimonials.labels.visibility') }}</strong>
                        <p>
                            {{ $selectedTestimonial->en_vedette
                                ? __('admin.user_testimonials.featured_visible')
                                : __('admin.user_testimonials.standard_visibility') }}
                        </p>
                    </div>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.user_testimonials.empty') }}</p>
            @endif
        </article>
    </section>
</div>
