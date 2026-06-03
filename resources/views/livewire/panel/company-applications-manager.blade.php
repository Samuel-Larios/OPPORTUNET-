<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.company_applications.list_title') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.company_applications.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.company_applications.all_statuses') }}</option>
                    <option value="proposee_entreprise">{{ __('admin.applications.statuses.proposee_entreprise') }}</option>
                    <option value="validee_entreprise">{{ __('admin.applications.statuses.validee_entreprise') }}</option>
                </select>
            </div>

            <div class="panel-list panel-list-spaced">
                @forelse ($applications as $application)
                    <button type="button" wire:click="selectApplication({{ $application->id }})" class="panel-application-item{{ $selectedApplication && $selectedApplication->id === $application->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $application->prenom }} {{ $application->nom }}</strong>
                            <span>{{ $application->email }}</span>
                            <span>{{ $application->opportunite?->titre }}</span>
                        </div>
                        <span class="panel-badge">{{ __('admin.applications.statuses.' . $application->statut) }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.company_applications.empty') }}</p>
                @endforelse
            </div>

            @if ($applications->hasPages())
                <div class="panel-pagination">
                    {{ $applications->links() }}
                </div>
            @endif
        </article>

        <article class="panel-card">
            @if ($selectedApplication)
                <div class="panel-card-head">
                    <h2>{{ __('admin.company_applications.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.applications.labels.candidate') }}</span>
                            <strong>{{ $selectedApplication->prenom }} {{ $selectedApplication->nom }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.applications.labels.offer') }}</span>
                            <strong>{{ $selectedApplication->opportunite?->titre }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.applications.labels.email') }}</span>
                            <strong>{{ $selectedApplication->email }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.applications.labels.phone') }}</span>
                            <strong>{{ $selectedApplication->telephone ?: '-' }}</strong>
                        </div>
                    </div>

                    @if ($selectedApplication->message)
                        <div class="panel-card panel-card-soft">
                            <strong>{{ __('admin.applications.labels.message') }}</strong>
                            <p>{{ $selectedApplication->message }}</p>
                        </div>
                    @endif

                    @if ($selectedApplication->notes_admin)
                        <div class="panel-card panel-card-soft">
                            <strong>{{ __('admin.company_applications.admin_note') }}</strong>
                            <p>{{ $selectedApplication->notes_admin }}</p>
                        </div>
                    @endif

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.applications.labels.files') }}</strong>
                        <div class="panel-file-links">
                            <a href="{{ route('panel.company.applications.download', ['candidature' => $selectedApplication->id, 'type' => 'lettre']) }}">{{ __('admin.applications.files.letter') }}</a>
                            @foreach ($selectedApplication->diplome_fichiers ?? [] as $index => $path)
                                <a href="{{ route('panel.company.applications.download', ['candidature' => $selectedApplication->id, 'type' => 'diplome', 'index' => $index]) }}">{{ __('admin.applications.files.diploma') }} {{ $index + 1 }}</a>
                            @endforeach
                            @foreach ($selectedApplication->attestation_fichiers ?? [] as $index => $path)
                                <a href="{{ route('panel.company.applications.download', ['candidature' => $selectedApplication->id, 'type' => 'attestation', 'index' => $index]) }}">{{ __('admin.applications.files.certificate') }} {{ $index + 1 }}</a>
                            @endforeach
                        </div>
                    </div>

                    <form wire:submit="validateApplication" class="panel-form-grid">
                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.company_applications.company_note') }}</span>
                            <textarea wire:model="companyNote" rows="6"></textarea>
                        </label>

                        <div class="panel-action-row panel-field-span">
                            @if ($selectedApplication->statut === 'proposee_entreprise')
                                <button type="submit" class="panel-primary-btn">{{ __('admin.company_applications.validate') }}</button>
                            @else
                                <span class="panel-badge is-success">{{ __('admin.applications.statuses.validee_entreprise') }}</span>
                            @endif
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.company_applications.empty') }}</p>
            @endif
        </article>
    </section>
</div>
