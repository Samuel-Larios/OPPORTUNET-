@php
    $contactModes = [
        'whatsapp' => 'WhatsApp',
        'email' => 'Email',
        'telephone' => 'Telephone',
        'presentiel' => 'Presentiel',
    ];
    $availabilities = [
        'matin' => 'Matin',
        'apres_midi' => 'Apres-midi',
        'soir' => 'Soir',
        'flexible' => 'Flexible',
    ];
@endphp

<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.requests.title') }}</h2>
                <p>{{ __('admin.requests.intro') }}</p>
            </div>

            <form wire:submit="save" class="panel-form-grid">
                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.requests.service') }}</span>
                    <select wire:model="serviceId">
                        <option value="">{{ __('admin.requests.service_placeholder') }}</option>
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">{{ $service->titre }}</option>
                        @endforeach
                    </select>
                    @error('serviceId') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.requests.phone') }}</span>
                    <input type="text" wire:model="telephone" />
                    @error('telephone') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.requests.whatsapp') }}</span>
                    <input type="text" wire:model="whatsapp" />
                    @error('whatsapp') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.requests.country') }}</span>
                    <input type="text" wire:model="pays" />
                    @error('pays') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.requests.need') }}</span>
                    <textarea wire:model="besoin" rows="4"></textarea>
                    @error('besoin') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.requests.goal') }}</span>
                    <textarea wire:model="objectif" rows="3"></textarea>
                    @error('objectif') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.requests.budget') }}</span>
                    <input type="text" wire:model="budget" />
                    @error('budget') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.requests.contact_mode') }}</span>
                    <select wire:model="modeContactPrefere">
                        @foreach ($contactModes as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('modeContactPrefere') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.requests.availability') }}</span>
                    <select wire:model="disponibilite">
                        @foreach ($availabilities as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('disponibilite') <small>{{ $message }}</small> @enderror
                </label>

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.requests.submit') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.requests.history') }}</h2>
            </div>

            <div class="panel-list">
                @forelse ($requests as $request)
                    <div class="panel-list-row panel-list-row-block">
                        <div>
                            <strong>{{ $request->service?->titre ?? 'Demande libre' }}</strong>
                            <span>{{ $request->created_at->format('d/m/Y') }}</span>
                        </div>
                        <p>{{ \Illuminate\Support\Str::limit($request->besoin, 120) }}</p>
                        <span class="panel-badge">{{ ucfirst(str_replace('_', ' ', $request->statut)) }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucune demande enregistree.</p>
                @endforelse
            </div>

            <div class="panel-card-head panel-card-head-spaced">
                <h2>{{ __('admin.requests.messages') }}</h2>
            </div>

            <div class="panel-list">
                @forelse ($contacts as $contact)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ ucfirst($contact->sujet) }}</strong>
                            <span>{{ $contact->created_at->format('d/m/Y') }}</span>
                        </div>
                        <span class="panel-badge">{{ ucfirst(str_replace('_', ' ', $contact->statut)) }}</span>
                    </div>
                @empty
                    <p class="panel-empty">Aucun message pour le moment.</p>
                @endforelse
            </div>
        </article>
    </section>
</div>
