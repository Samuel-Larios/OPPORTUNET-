<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.cv_depots.list_title') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.cv_depots.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.cv_depots.all_statuses') }}</option>
                    @foreach (__('admin.cv_depots.statuses') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-list panel-list-spaced">
                @forelse ($cvDepots as $cvDepot)
                    <button type="button" wire:click="selectCvDepot({{ $cvDepot->id }})" class="panel-application-item{{ $selectedCvDepot && $selectedCvDepot->id === $cvDepot->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $cvDepot->prenom }} {{ $cvDepot->nom }}</strong>
                            <span>{{ $cvDepot->email }}</span>
                            <span>{{ $cvDepot->titre_poste ?: __('admin.cv_depots.free_profile') }}</span>
                        </div>
                        <span class="panel-badge">{{ $cvDepot->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.cv_depots.empty') }}</p>
                @endforelse
            </div>

            @if ($cvDepots->hasPages())
                <div class="panel-pagination">
                    {{ $cvDepots->links() }}
                </div>
            @endif
        </article>

        <article class="panel-card">
            @if ($selectedCvDepot)
                @php
                    $timeline = $selectedCvDepot->messages->sortBy('created_at');
                @endphp

                <div class="panel-card-head">
                    <h2>{{ __('admin.cv_depots.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.cv_depots.labels.candidate') }}</span>
                            <strong>{{ $selectedCvDepot->prenom }} {{ $selectedCvDepot->nom }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.cv_depots.labels.email') }}</span>
                            <strong>{{ $selectedCvDepot->email }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.cv_depots.labels.phone') }}</span>
                            <strong>{{ $selectedCvDepot->telephone ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.cv_depots.labels.whatsapp') }}</span>
                            <strong>{{ $selectedCvDepot->whatsapp ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.cv_depots.labels.location') }}</span>
                            <strong>{{ trim(($selectedCvDepot->ville ? $selectedCvDepot->ville . ', ' : '') . ($selectedCvDepot->pays ?: '')) ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.cv_depots.labels.contract') }}</span>
                            <strong>{{ __('cv_services.contracts.' . $selectedCvDepot->type_contrat_recherche) }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.cv_depots.labels.profile') }}</strong>
                        <div class="panel-detail-grid">
                            <span>{{ __('admin.cv_depots.labels.job_title') }}: {{ $selectedCvDepot->titre_poste ?: '-' }}</span>
                            <span>{{ __('admin.cv_depots.labels.education') }}: {{ $selectedCvDepot->niveau_etude ?: '-' }}</span>
                            <span>{{ __('admin.cv_depots.labels.study_field') }}: {{ $selectedCvDepot->domaine_etude ?: '-' }}</span>
                            <span>{{ __('admin.cv_depots.labels.experience') }}: {{ $selectedCvDepot->annees_experience !== null ? $selectedCvDepot->annees_experience : '-' }}</span>
                            <span>{{ __('admin.cv_depots.labels.birth_date') }}: {{ $selectedCvDepot->date_naissance?->format('d/m/Y') ?: '-' }}</span>
                            <span>{{ __('admin.cv_depots.labels.gender') }}: {{ $selectedCvDepot->genre ? __('admin.cv_depots.genders.' . $selectedCvDepot->genre) : '-' }}</span>
                        </div>
                    </div>

                    @if ($selectedCvDepot->competences || $selectedCvDepot->langues || $selectedCvDepot->objectif_professionnel || $selectedCvDepot->secteurs_interet)
                        <div class="panel-card panel-card-soft">
                            <strong>{{ __('admin.cv_depots.labels.project') }}</strong>
                            <div class="panel-list panel-list-spaced">
                                @if ($selectedCvDepot->competences)
                                    <div class="panel-list-row panel-list-row-block">
                                        <div>
                                            <strong>{{ __('admin.cv_depots.labels.skills') }}</strong>
                                        </div>
                                        <p>{{ $selectedCvDepot->competences }}</p>
                                    </div>
                                @endif
                                @if ($selectedCvDepot->langues)
                                    <div class="panel-list-row panel-list-row-block">
                                        <div>
                                            <strong>{{ __('admin.cv_depots.labels.languages') }}</strong>
                                        </div>
                                        <p>{{ $selectedCvDepot->langues }}</p>
                                    </div>
                                @endif
                                @if ($selectedCvDepot->objectif_professionnel)
                                    <div class="panel-list-row panel-list-row-block">
                                        <div>
                                            <strong>{{ __('admin.cv_depots.labels.goal') }}</strong>
                                        </div>
                                        <p>{{ $selectedCvDepot->objectif_professionnel }}</p>
                                    </div>
                                @endif
                                @if ($selectedCvDepot->secteurs_interet)
                                    <div class="panel-list-row panel-list-row-block">
                                        <div>
                                            <strong>{{ __('admin.cv_depots.labels.sectors') }}</strong>
                                        </div>
                                        <p>{{ $selectedCvDepot->secteurs_interet }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.cv_depots.labels.documents') }}</strong>
                        <div class="panel-file-links">
                            <a href="{{ route('panel.admin.cv-depots.download-cv', $selectedCvDepot->id) }}">{{ __('admin.cv_depots.files.cv') }}</a>
                            @if ($selectedCvDepot->linkedin_url)
                                <a href="{{ $selectedCvDepot->linkedin_url }}" target="_blank" rel="noopener">LinkedIn</a>
                            @endif
                            @if ($selectedCvDepot->portfolio_url)
                                <a href="{{ $selectedCvDepot->portfolio_url }}" target="_blank" rel="noopener">Portfolio</a>
                            @endif
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.cv_depots.labels.requests') }}</strong>
                        <div class="panel-detail-grid">
                            <span>{{ $selectedCvDepot->demande_redaction_cv ? __('cv_services.form.fields.demande_redaction_cv') : __('admin.cv_depots.labels.no_cv_request') }}</span>
                            <span>{{ $selectedCvDepot->demande_coaching ? __('cv_services.form.fields.demande_coaching') : __('admin.cv_depots.labels.no_coaching_request') }}</span>
                            <span>{{ $selectedCvDepot->demande_orientation ? __('cv_services.form.fields.demande_orientation') : __('admin.cv_depots.labels.no_orientation_request') }}</span>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.cv_depots.messages.thread_title') }}</strong>
                        <div class="panel-thread">
                            @forelse ($timeline as $message)
                                <article class="panel-message{{ $message->isAdmin() ? ' is-admin' : ' is-user' }}">
                                    <div class="panel-message-head">
                                        <strong>{{ $message->senderLabel() }}</strong>
                                        <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    @if ($message->message)
                                        <p>{{ $message->message }}</p>
                                    @endif
                                    @if ($message->attachment_path)
                                        <a href="{{ route('panel.admin.cv-depots.download-message', ['cvDepot' => $selectedCvDepot->id, 'message' => $message->id]) }}" class="panel-attachment-chip">
                                            {{ $message->attachment_name ?: __('admin.cv_depots.files.attachment') }}
                                        </a>
                                    @endif
                                </article>
                            @empty
                                <p class="panel-empty">{{ __('admin.cv_depots.messages.empty') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <form wire:submit="updateCvDepot" class="panel-form-grid" enctype="multipart/form-data">
                        <label class="panel-field">
                            <span>{{ __('admin.cv_depots.labels.status') }}</span>
                            <select wire:model="processingStatus">
                                @foreach (__('admin.cv_depots.statuses') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('processingStatus') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.cv_depots.labels.processed_by') }}</span>
                            <input type="text" value="{{ $selectedCvDepot->processedBy?->fullName() ?: '-' }}" readonly disabled />
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.cv_depots.labels.admin_notes') }}</span>
                            <textarea wire:model="adminNotes" rows="4"></textarea>
                            @error('adminNotes') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.cv_depots.messages.reply') }}</span>
                            <textarea wire:model="replyMessage" rows="5"></textarea>
                            @error('replyMessage') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.cv_depots.messages.attachment') }}</span>
                            <input type="file" wire:model="replyAttachment" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                            <small>{{ __('admin.cv_depots.messages.attachment_hint') }}</small>
                            @error('replyAttachment') <small>{{ $message }}</small> @enderror
                        </label>

                        <div class="panel-action-row panel-field-span">
                            <button type="submit" class="panel-primary-btn">{{ __('admin.cv_depots.process') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.cv_depots.empty') }}</p>
            @endif
        </article>
    </section>
</div>
