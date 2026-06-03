<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_trainings.list_title') }}</h2>
                <p>{{ __('admin.user_trainings.intro') }}</p>
            </div>

            <div class="panel-list">
                @forelse ($registrations as $registration)
                    <button type="button" wire:click="selectRegistration({{ $registration->id }})" class="panel-application-item{{ $selectedRegistration && $selectedRegistration->id === $registration->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $registration->formation?->titre ?: __('admin.user_trainings.labels.formation') }}</strong>
                            <span>{{ $registration->created_at->format('d/m/Y') }}</span>
                            <span>{{ $registration->paymentStatusLabel() }}</span>
                        </div>
                        <span class="panel-badge{{ $registration->est_suspendue ? ' is-muted' : '' }}">{{ $registration->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.user_trainings.empty') }}</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            @if ($selectedRegistration)
                @php
                    $timeline = $selectedRegistration->messages->sortBy('created_at');
                @endphp

                <div class="panel-card-head">
                    <h2>{{ __('admin.user_trainings.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.user_trainings.labels.status') }}</span>
                            <strong>{{ $selectedRegistration->statusLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_trainings.labels.payment_status') }}</span>
                            <strong>{{ $selectedRegistration->paymentStatusLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_trainings.labels.payment_mode') }}</span>
                            <strong>{{ $selectedRegistration->paymentModeLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_trainings.labels.submitted') }}</span>
                            <strong>{{ $selectedRegistration->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_trainings.labels.summary') }}</strong>
                        <div class="panel-list panel-list-spaced">
                            <div class="panel-list-row panel-list-row-block">
                                <div>
                                    <strong>{{ __('admin.user_trainings.labels.formation') }}</strong>
                                </div>
                                <p>{{ $selectedRegistration->formation?->titre ?: '-' }}</p>
                            </div>
                            @if ($selectedRegistration->motivation)
                                <div class="panel-list-row panel-list-row-block">
                                    <div>
                                        <strong>{{ __('admin.user_trainings.labels.motivation') }}</strong>
                                    </div>
                                    <p>{{ $selectedRegistration->motivation }}</p>
                                </div>
                            @endif
                            @if ($selectedRegistration->est_suspendue)
                                <div class="panel-list-row panel-list-row-block">
                                    <div>
                                        <strong>{{ __('admin.user_trainings.labels.suspended') }}</strong>
                                    </div>
                                    <p>{{ $selectedRegistration->motif_suspension ?: '-' }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.training_registrations.messages.thread_title') }}</strong>
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
                                        <a href="{{ route('panel.user.trainings.download-message', ['registration' => $selectedRegistration->id, 'message' => $message->id]) }}" class="panel-attachment-chip">
                                            {{ $message->attachment_name ?: __('admin.cv_depots.files.attachment') }}
                                        </a>
                                    @endif
                                </article>
                            @empty
                                <p class="panel-empty">{{ __('admin.training_registrations.messages.empty') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <form wire:submit="sendReply" class="panel-form-grid" enctype="multipart/form-data">
                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.user_trainings.labels.reply') }}</span>
                            <textarea wire:model="replyMessage" rows="5"></textarea>
                            @error('replyMessage') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.training_registrations.messages.attachment') }}</span>
                            <input type="file" wire:model="replyAttachment" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                            <small>{{ __('admin.training_registrations.messages.attachment_hint') }}</small>
                            @error('replyAttachment') <small>{{ $message }}</small> @enderror
                        </label>

                        <div class="panel-action-row panel-field-span">
                            <button type="submit" class="panel-primary-btn">{{ __('admin.user_trainings.send') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.user_trainings.empty') }}</p>
            @endif
        </article>
    </section>
</div>
