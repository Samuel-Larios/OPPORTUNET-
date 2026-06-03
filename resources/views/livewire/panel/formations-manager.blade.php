<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.trainings.title') }}</h2>
                <p>{{ __('admin.trainings.intro') }}</p>
            </div>

            <form wire:submit="saveFormation" class="panel-form-grid" enctype="multipart/form-data">
                <label class="panel-field">
                    <span>FR - {{ __('admin.trainings.training') }}</span>
                    <input type="text" wire:model.live.debounce.300ms="titreFr" />
                    @error('titreFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>EN - {{ __('admin.trainings.training') }}</span>
                    <input type="text" wire:model="titreEn" />
                    @error('titreEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.trainings.slug') }}</span>
                    <input type="text" wire:model="slug" readonly disabled />
                    <small>{{ __('admin.trainings.slug_hint') }}</small>
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.category') }}</span>
                    <select wire:model="categorieId">
                        <option value="">{{ __('admin.trainings.category_placeholder') }}</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->nom }}</option>
                        @endforeach
                    </select>
                    @error('categorieId') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.trainer') }}</span>
                    <select wire:model="formateurId">
                        <option value="">{{ __('admin.trainings.trainer_placeholder') }}</option>
                        @foreach ($trainers as $trainer)
                            <option value="{{ $trainer->id }}">{{ $trainer->fullName() }}</option>
                        @endforeach
                    </select>
                    @error('formateurId') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>FR - Description courte</span>
                    <textarea wire:model="descriptionCourteFr" rows="3"></textarea>
                    @error('descriptionCourteFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>EN - Short description</span>
                    <textarea wire:model="descriptionCourteEn" rows="3"></textarea>
                    @error('descriptionCourteEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>FR - {{ __('admin.trainings.program') }}</span>
                    <textarea wire:model="descriptionLongueFr" rows="5"></textarea>
                    @error('descriptionLongueFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>EN - {{ __('admin.trainings.program') }}</span>
                    <textarea wire:model="descriptionLongueEn" rows="5"></textarea>
                    @error('descriptionLongueEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.mode') }}</span>
                    <select wire:model="mode">
                        @foreach ($trainingModes as $trainingMode)
                            <option value="{{ $trainingMode }}">{{ __('admin.trainings.modes.' . $trainingMode) }}</option>
                        @endforeach
                    </select>
                    @error('mode') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.status') }}</span>
                    <select wire:model="statut">
                        @foreach ($trainingStatuses as $trainingStatus)
                            <option value="{{ $trainingStatus }}">{{ __('admin.trainings.statuses.' . $trainingStatus) }}</option>
                        @endforeach
                    </select>
                    @error('statut') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>FR - {{ __('admin.trainings.location') }}</span>
                    <input type="text" wire:model="lieuFr" />
                    @error('lieuFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>EN - {{ __('admin.trainings.location') }}</span>
                    <input type="text" wire:model="lieuEn" />
                    @error('lieuEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.trainings.online_link') }}</span>
                    <input type="url" wire:model="lienEnLigne" />
                    @error('lienEnLigne') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.price') }}</span>
                    <input type="number" min="0" step="0.01" wire:model="prix" />
                    @error('prix') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.currency') }}</span>
                    <input type="text" wire:model="devise" />
                    @error('devise') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.duration_hours') }}</span>
                    <input type="number" min="1" wire:model="dureeHeures" />
                    @error('dureeHeures') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.sessions') }}</span>
                    <input type="number" min="1" wire:model="nbSeances" />
                    @error('nbSeances') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.start_date') }}</span>
                    <input type="date" wire:model="dateDebut" />
                    @error('dateDebut') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.end_date') }}</span>
                    <input type="date" wire:model="dateFin" />
                    @error('dateFin') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.start_time') }}</span>
                    <input type="time" wire:model="heureDebut" />
                    @error('heureDebut') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.timezone') }}</span>
                    <input type="text" wire:model="fuseauHoraire" />
                    @error('fuseauHoraire') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.max_places') }}</span>
                    <input type="number" min="1" wire:model="placesMax" />
                    @error('placesMax') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.trainings.remaining_places') }}</span>
                    <input type="number" min="0" wire:model="placesRestantes" />
                    @error('placesRestantes') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>FR - {{ __('admin.trainings.level') }}</span>
                    <input type="text" wire:model="niveauFr" />
                    @error('niveauFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>EN - {{ __('admin.trainings.level') }}</span>
                    <input type="text" wire:model="niveauEn" />
                    @error('niveauEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>FR - {{ __('admin.trainings.prerequisites') }}</span>
                    <textarea wire:model="prerequisFr" rows="4"></textarea>
                    @error('prerequisFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>EN - {{ __('admin.trainings.prerequisites') }}</span>
                    <textarea wire:model="prerequisEn" rows="4"></textarea>
                    @error('prerequisEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>FR - {{ __('admin.trainings.objectives') }}</span>
                    <textarea wire:model="objectifsFr" rows="4"></textarea>
                    @error('objectifsFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>EN - {{ __('admin.trainings.objectives') }}</span>
                    <textarea wire:model="objectifsEn" rows="4"></textarea>
                    @error('objectifsEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>FR - {{ __('admin.trainings.program') }}</span>
                    <textarea wire:model="programmeFr" rows="5"></textarea>
                    @error('programmeFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>EN - {{ __('admin.trainings.program') }}</span>
                    <textarea wire:model="programmeEn" rows="5"></textarea>
                    @error('programmeEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>FR - {{ __('admin.trainings.certificate') }}</span>
                    <input type="text" wire:model="certificatFr" />
                    @error('certificatFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>EN - {{ __('admin.trainings.certificate') }}</span>
                    <input type="text" wire:model="certificatEn" />
                    @error('certificatEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>FR - {{ __('admin.trainings.whatsapp_message') }}</span>
                    <textarea wire:model="whatsappMessageFr" rows="3"></textarea>
                    @error('whatsappMessageFr') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>EN - {{ __('admin.trainings.whatsapp_message') }}</span>
                    <textarea wire:model="whatsappMessageEn" rows="3"></textarea>
                    @error('whatsappMessageEn') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.trainings.image') }}</span>
                    <input type="file" wire:model="imageCouverture" accept="image/*" />
                    <small>{{ __('admin.trainings.image_hint') }}</small>
                    @error('imageCouverture') <small>{{ $message }}</small> @enderror
                </label>

                @if ($currentImageUrl !== '')
                    <div class="panel-field panel-field-span">
                        <span>{{ __('admin.trainings.current_image') }}</span>
                        <img src="{{ $currentImageUrl }}" alt="{{ __('admin.trainings.current_image') }}" style="max-width: 220px; border-radius: 18px;" />
                        <button type="button" class="panel-secondary-link" wire:click="removeImage">{{ __('admin.trainings.remove_image') }}</button>
                    </div>
                @endif

                <label class="panel-checkline">
                    <input type="checkbox" wire:model="gratuit" />
                    <span>{{ __('admin.trainings.free') }}</span>
                </label>

                <label class="panel-checkline">
                    <input type="checkbox" wire:model="inscriptionsOuvertes" />
                    <span>{{ __('admin.trainings.open_registrations') }}</span>
                </label>

                <label class="panel-checkline">
                    <input type="checkbox" wire:model="enVedette" />
                    <span>{{ __('admin.trainings.featured') }}</span>
                </label>

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.trainings.save') }}</button>
                    <button type="button" class="panel-secondary-btn" wire:click="resetForm">{{ __('admin.trainings.reset') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.trainings.list') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.trainings.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.trainings.all_statuses') }}</option>
                    @foreach ($trainingStatuses as $trainingStatus)
                        <option value="{{ $trainingStatus }}">{{ __('admin.trainings.statuses.' . $trainingStatus) }}</option>
                    @endforeach
                </select>
                <select wire:model.live="modeFilter">
                    <option value="">{{ __('admin.trainings.all_modes') }}</option>
                    @foreach ($trainingModes as $trainingMode)
                        <option value="{{ $trainingMode }}">{{ __('admin.trainings.modes.' . $trainingMode) }}</option>
                    @endforeach
                </select>
                <select wire:model.live="categoryFilter">
                    <option value="">{{ __('admin.trainings.all_categories') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->nom }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-list">
                @forelse ($formations as $formation)
                    <div class="panel-list-row">
                        <div>
                            <strong>{{ $formation->titre }}</strong>
                            <span>{{ $formation->category?->nom ?: __('admin.trainings.category_placeholder') }}</span>
                            <span>
                                {{ __('admin.trainings.statuses.' . $formation->statut) }}
                                ·
                                {{ __('admin.trainings.modes.' . $formation->mode) }}
                                @if ($formation->date_debut)
                                    · {{ $formation->date_debut->format('d/m/Y') }}
                                @endif
                            </span>
                        </div>
                        <div class="panel-inline-actions">
                            <button type="button" class="panel-secondary-btn" wire:click="editFormation({{ $formation->id }})">{{ __('admin.trainings.edit') }}</button>
                            <button type="button" class="panel-danger-btn" wire:click="deleteFormation({{ $formation->id }})">{{ __('admin.trainings.delete') }}</button>
                        </div>
                    </div>
                @empty
                    <p class="panel-empty">{{ __('trainings.empty.title') }}</p>
                @endforelse
            </div>

            @if ($formations->hasPages())
                <div class="panel-pagination">
                    {{ $formations->links() }}
                </div>
            @endif
        </article>
    </section>
</div>
