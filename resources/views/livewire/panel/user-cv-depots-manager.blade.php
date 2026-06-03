<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_cv_depots.list_title') }}</h2>
                <p>{{ __('admin.user_cv_depots.intro') }}</p>
            </div>

            <div class="panel-list">
                @forelse ($cvDepots as $cvDepot)
                    <button type="button" wire:click="selectCvDepot({{ $cvDepot->id }})" class="panel-application-item{{ $selectedCvDepot && $selectedCvDepot->id === $cvDepot->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $cvDepot->titre_poste ?: __('admin.user_cv_depots.free_profile') }}</strong>
                            <span>{{ $cvDepot->created_at->format('d/m/Y') }}</span>
                            <span>{{ $cvDepot->email }}</span>
                        </div>
                        <span class="panel-badge">{{ $cvDepot->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.user_cv_depots.empty') }}</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            @if ($selectedCvDepot)
                @php
                    $timeline = $selectedCvDepot->messages->sortBy('created_at');
                @endphp

                <div class="panel-card-head">
                    <h2>{{ __('admin.user_cv_depots.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.user_cv_depots.labels.status') }}</span>
                            <strong>{{ $selectedCvDepot->statusLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_cv_depots.labels.submitted') }}</span>
                            <strong>{{ $selectedCvDepot->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_cv_depots.labels.contract') }}</span>
                            <strong>{{ __('cv_services.contracts.' . $selectedCvDepot->type_contrat_recherche) }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_cv_depots.labels.remote') }}</span>
                            <strong>{{ $selectedCvDepot->teletravail_souhaite ? __('admin.user_cv_depots.labels.yes') : __('admin.user_cv_depots.labels.no') }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_cv_depots.labels.documents') }}</strong>
                        <div class="panel-file-links">
                            <a href="{{ route('panel.user.cv-depots.download-cv', $selectedCvDepot->id) }}">{{ __('admin.cv_depots.files.cv') }}</a>
                        </div>
                    </div>

                    @if ($selectedCvDepot->objectif_professionnel)
                        <div class="panel-card panel-card-soft">
                            <strong>{{ __('admin.user_cv_depots.labels.summary') }}</strong>
                            <div class="panel-list panel-list-spaced">
                                @if ($selectedCvDepot->objectif_professionnel)
                                    <div class="panel-list-row panel-list-row-block">
                                        <div>
                                            <strong>{{ __('admin.cv_depots.labels.goal') }}</strong>
                                        </div>
                                        <p>{{ $selectedCvDepot->objectif_professionnel }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

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
                                        <a href="{{ route('panel.user.cv-depots.download-message', ['cvDepot' => $selectedCvDepot->id, 'message' => $message->id]) }}" class="panel-attachment-chip">
                                            {{ $message->attachment_name ?: __('admin.cv_depots.files.attachment') }}
                                        </a>
                                    @endif
                                </article>
                            @empty
                                <p class="panel-empty">{{ __('admin.cv_depots.messages.empty') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <form wire:submit="sendReply" class="panel-form-grid" enctype="multipart/form-data">
                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.user_cv_depots.labels.reply') }}</span>
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
                            <button type="submit" class="panel-primary-btn">{{ __('admin.user_cv_depots.send') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.user_cv_depots.empty') }}</p>
            @endif
        </article>
    </section>
</div>
