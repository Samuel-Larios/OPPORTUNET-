<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.testimonials.list_title') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.testimonials.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.testimonials.all_statuses') }}</option>
                    @foreach (__('admin.testimonials.statuses') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="typeFilter">
                    <option value="">{{ __('admin.testimonials.all_types') }}</option>
                    @foreach (__('admin.testimonials.types') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="featuredFilter">
                    <option value="">{{ __('admin.testimonials.all_featured_states') }}</option>
                    <option value="1">{{ __('admin.testimonials.featured_only') }}</option>
                    <option value="0">{{ __('admin.testimonials.not_featured_only') }}</option>
                </select>
            </div>

            <div class="panel-list panel-list-spaced">
                @forelse ($testimonials as $testimonial)
                    <button type="button" wire:click="selectTestimonial({{ $testimonial->id }})" class="panel-application-item{{ $selectedTestimonial && $selectedTestimonial->id === $testimonial->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $testimonial->prenom }}{{ $testimonial->nom ? ' ' . $testimonial->nom : '' }}</strong>
                            <span>{{ $testimonial->typeLabel() }}</span>
                            <span>{{ $testimonial->email ?: '-' }}</span>
                        </div>
                        <span class="panel-badge{{ $testimonial->en_vedette ? '' : ' is-muted' }}">{{ $testimonial->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.testimonials.empty') }}</p>
                @endforelse
            </div>

            @if ($testimonials->hasPages())
                <div class="panel-pagination">
                    {{ $testimonials->links() }}
                </div>
            @endif
        </article>

        <article class="panel-card">
            @if ($selectedTestimonial)
                <div class="panel-card-head">
                    <h2>{{ __('admin.testimonials.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.testimonials.labels.author') }}</span>
                            <strong>{{ $selectedTestimonial->prenom }}{{ $selectedTestimonial->nom ? ' ' . $selectedTestimonial->nom : '' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.testimonials.labels.email') }}</span>
                            <strong>{{ $selectedTestimonial->email ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.testimonials.labels.type') }}</span>
                            <strong>{{ $selectedTestimonial->typeLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.testimonials.labels.rating') }}</span>
                            <strong>{{ $selectedTestimonial->note ? $selectedTestimonial->note . '/5' : '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.testimonials.labels.country') }}</span>
                            <strong>{{ $selectedTestimonial->pays ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.testimonials.labels.profession') }}</span>
                            <strong>{{ $selectedTestimonial->profession ?: '-' }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.testimonials.labels.content') }}</strong>
                        <div class="panel-detail-grid">
                            <span>{{ __('admin.testimonials.labels.submitted') }}: {{ $selectedTestimonial->created_at->format('d/m/Y H:i') }}</span>
                            <span>{{ __('admin.testimonials.labels.from_account') }}: {{ $selectedTestimonial->user?->fullName() ?: '-' }}</span>
                        </div>
                        <p style="margin-top: 12px;">{{ $selectedTestimonial->contenu }}</p>
                    </div>

                    <form wire:submit="updateTestimonial" class="panel-form-grid">
                        <label class="panel-field">
                            <span>{{ __('admin.testimonials.labels.status') }}</span>
                            <select wire:model="processingStatus">
                                @foreach (__('admin.testimonials.statuses') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('processingStatus') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.testimonials.labels.order') }}</span>
                            <input type="number" min="0" wire:model="ordre" />
                            @error('ordre') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-checkline">
                            <input type="checkbox" wire:model="enVedette" />
                            <span>{{ __('admin.testimonials.labels.featured') }}</span>
                        </label>

                        <div class="panel-action-row panel-field-span">
                            <button type="submit" class="panel-primary-btn">{{ __('admin.testimonials.process') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.testimonials.empty') }}</p>
            @endif
        </article>
    </section>
</div>
