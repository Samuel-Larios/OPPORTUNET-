<div class="panel-stack" wire:poll.30s>
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.services.title') }}</h2>
                <p>{{ __('admin.services.image_hint') }}</p>
            </div>

            <form wire:submit="saveService" class="panel-form-grid" enctype="multipart/form-data">
                <label class="panel-field">
                    <span>Titre FR</span>
                    <input type="text" wire:model="titreFr" />
                    @error('titreFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>Title EN</span>
                    <input type="text" wire:model="titreEn" />
                    @error('titreEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.services.category') }}</span>
                    <select wire:model="categorieId">
                        <option value="">{{ __('admin.services.category_placeholder') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nom }}</option>
                        @endforeach
                    </select>
                    @error('categorieId')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.services.type') }}</span>
                    <select wire:model="type">
                        @foreach ($serviceTypes as $serviceType)
                            <option value="{{ $serviceType }}">{{ __('admin.services.types.' . $serviceType) }}
                            </option>
                        @endforeach
                    </select>
                    @error('type')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Description courte FR</span>
                    <textarea wire:model="descriptionCourteFr" rows="3"></textarea>
                    @error('descriptionCourteFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Short description EN</span>
                    <textarea wire:model="descriptionCourteEn" rows="3"></textarea>
                    @error('descriptionCourteEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Description longue FR</span>
                    <textarea wire:model="descriptionLongueFr" rows="6"></textarea>
                    @error('descriptionLongueFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Long description EN</span>
                    <textarea wire:model="descriptionLongueEn" rows="6"></textarea>
                    @error('descriptionLongueEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.services.icon') }}</span>
                    <input type="text" wire:model="icone" placeholder="briefcase" />
                    @error('icone')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.services.order') }}</span>
                    <input type="number" min="0" wire:model="ordre" />
                    @error('ordre')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.services.price') }}</span>
                    <input type="number" min="0" step="0.01" wire:model="prix" />
                    @error('prix')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.services.currency') }}</span>
                    <input type="text" wire:model="devise" />
                    @error('devise')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>Durée FR</span>
                    <input type="text" wire:model="dureeFr" placeholder="48 h à 72 h" />
                    @error('dureeFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field">
                    <span>Duration EN</span>
                    <input type="text" wire:model="dureeEn" placeholder="48 to 72 hours" />
                    @error('dureeEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>Message WhatsApp FR</span>
                    <textarea wire:model="whatsappMessageFr" rows="3"></textarea>
                    @error('whatsappMessageFr')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>WhatsApp message EN</span>
                    <textarea wire:model="whatsappMessageEn" rows="3"></textarea>
                    @error('whatsappMessageEn')
                        <small>{{ $message }}</small>
                    @enderror
                </label>

                <div class="panel-field panel-field-span">
                    <span>{{ __('admin.services.image') }}</span>
                    <input type="file" wire:model="image" accept="image/*" />
                    <small>{{ __('admin.services.image_hint') }}</small>
                    @error('image')
                        <small>{{ $message }}</small>
                    @enderror
                </div>

                @if ($image || $currentImageUrl)
                    <div class="panel-field panel-field-span">
                        <span>{{ __('admin.services.current_image') }}</span>
                        <article class="panel-image-card panel-image-card-single">
                            @if ($image)
                                <img src="{{ $image->temporaryUrl() }}" alt="" class="panel-image-preview" />
                            @else
                                <img src="{{ $currentImageUrl }}" alt="" class="panel-image-preview" />
                            @endif

                            <button type="button" wire:click="removeImage" class="panel-secondary-btn panel-small-btn">
                                {{ __('admin.services.remove_image') }}
                            </button>
                        </article>
                    </div>
                @endif

                <div class="panel-check-grid">
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="actif" />
                        <span>{{ __('admin.users.active') }}</span>
                    </label>
                    <label class="panel-checkline">
                        <input type="checkbox" wire:model="enVedette" />
                        <span>{{ __('admin.services.featured') }}</span>
                    </label>
                </div>

                @include('livewire.panel.partials.schedule-fields')

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.services.save') }}</button>
                    <button type="button" wire:click="resetForm"
                        class="panel-secondary-btn">{{ __('admin.services.reset') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.services.list') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search"
                    placeholder="{{ __('admin.services.search') }}" />

                <select wire:model.live="typeFilter">
                    <option value="">{{ __('admin.services.all_types') }}</option>
                    @foreach ($serviceTypes as $serviceType)
                        <option value="{{ $serviceType }}">{{ __('admin.services.types.' . $serviceType) }}</option>
                    @endforeach
                </select>

                <select wire:model.live="activeFilter">
                    <option value="">{{ __('admin.services.all_statuses') }}</option>
                    <option value="1">{{ __('admin.users.active') }}</option>
                    <option value="0">{{ __('admin.users.inactive') }}</option>
                </select>

                <select wire:model.live="categoryFilter">
                    <option value="">{{ __('admin.services.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}">{{ $category->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.services.service') }}</th>
                            <th>{{ __('admin.services.category') }}</th>
                            <th>{{ __('admin.services.type') }}</th>
                            <th>{{ __('admin.services.price') }}</th>
                            <th>{{ __('admin.services.status') }}</th>
                            <th>{{ __('admin.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td>
                                    <strong>{{ $service->titre }}</strong>
                                    <span>{{ $service->duree ?: '-' }}</span>
                                </td>
                                <td>{{ $service->category?->nom ?: '-' }}</td>
                                <td>{{ __('admin.services.types.' . $service->type) }}</td>
                                <td>
                                    @if ($service->prix)
                                        {{ number_format((float) $service->prix, 0, ',', ' ') }}
                                        {{ $service->devise }}
                                    @else
                                        {{ __('admin.services.on_request') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="panel-badge{{ $service->actif ? ' is-success' : ' is-muted' }}">
                                        {{ $service->actif ? __('admin.users.active') : __('admin.users.inactive') }}
                                    </span>
                                    @if ($service->auto_publish && $service->scheduled_for)
                                        <div style="margin-top: 6px;">
                                            <span class="panel-badge">
                                                {{ app()->getLocale() === 'fr' ? 'Programmé le ' : 'Scheduled on ' }}{{ $service->scheduled_for->format('d/m/Y H:i') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="panel-inline-actions">
                                    <button type="button" wire:click="editService({{ $service->id }})"
                                        class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.services.edit') }}
                                    </button>
                                    <button type="button" wire:click="deleteService({{ $service->id }})"
                                        class="panel-secondary-btn panel-small-btn">
                                        {{ __('admin.services.delete') }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($services->hasPages())
                <div class="panel-pagination">
                    {{ $services->links() }}
                </div>
            @endif
        </article>
    </section>
</div>
