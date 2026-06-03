<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.training_registrations.list_title') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.training_registrations.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.training_registrations.all_statuses') }}</option>
                    @foreach (__('admin.training_registrations.statuses') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="paymentFilter">
                    <option value="">{{ __('admin.training_registrations.all_payments') }}</option>
                    @foreach (__('admin.training_registrations.payment_statuses') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="formationFilter">
                    <option value="">{{ __('admin.training_registrations.all_trainings') }}</option>
                    @foreach ($formations as $formation)
                        <option value="{{ $formation->id }}">{{ $formation->titre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-list panel-list-spaced">
                @forelse ($registrations as $registration)
                    <button type="button" wire:click="selectRegistration({{ $registration->id }})" class="panel-application-item{{ $selectedRegistration && $selectedRegistration->id === $registration->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $registration->prenom }} {{ $registration->nom }}</strong>
                            <span>{{ $registration->formation?->titre ?: __('admin.training_registrations.labels.formation') }}</span>
                            <span>{{ $registration->paymentStatusLabel() }}</span>
                        </div>
                        <span class="panel-badge{{ $registration->est_suspendue ? ' is-muted' : '' }}">{{ $registration->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.training_registrations.empty') }}</p>
                @endforelse
            </div>

            @if ($registrations->hasPages())
                <div class="panel-pagination">
                    {{ $registrations->links() }}
                </div>
            @endif
        </article>

        <article class="panel-card">
            @if ($selectedRegistration)
                @php
                    $timeline = $selectedRegistration->messages->sortBy('created_at');
                @endphp

                <div class="panel-card-head">
                    <h2>{{ __('admin.training_registrations.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.training_registrations.labels.candidate') }}</span>
                            <strong>{{ $selectedRegistration->prenom }} {{ $selectedRegistration->nom }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.training_registrations.labels.formation') }}</span>
                            <strong>{{ $selectedRegistration->formation?->titre ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.training_registrations.labels.email') }}</span>
                            <strong>{{ $selectedRegistration->email }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.training_registrations.labels.phone') }}</span>
                            <strong>{{ $selectedRegistration->telephone ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.training_registrations.labels.whatsapp') }}</span>
                            <strong>{{ $selectedRegistration->whatsapp ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.training_registrations.labels.country') }}</span>
                            <strong>{{ $selectedRegistration->pays ?: '-' }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.training_registrations.labels.motivation') }}</strong>
                        <div class="panel-detail-grid">
                            <span>{{ __('admin.training_registrations.labels.profession') }}: {{ $selectedRegistration->profession ?: '-' }}</span>
                            <span>{{ __('admin.training_registrations.labels.education') }}: {{ $selectedRegistration->niveau_etude ?: '-' }}</span>
                            <span>{{ __('admin.training_registrations.labels.submitted') }}: {{ $selectedRegistration->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @if ($selectedRegistration->motivation)
                            <p style="margin-top: 12px;">{{ $selectedRegistration->motivation }}</p>
                        @endif
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
                                        <a href="{{ route('panel.admin.training-registrations.download-message', ['registration' => $selectedRegistration->id, 'message' => $message->id]) }}" class="panel-attachment-chip">
                                            {{ $message->attachment_name ?: __('admin.cv_depots.files.attachment') }}
                                        </a>
                                    @endif
                                </article>
                            @empty
                                <p class="panel-empty">{{ __('admin.training_registrations.messages.empty') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <form wire:submit="updateRegistration" class="panel-form-grid" enctype="multipart/form-data">
                        <label class="panel-field">
                            <span>{{ __('admin.training_registrations.labels.status') }}</span>
                            <select wire:model="processingStatus">
                                @foreach (__('admin.training_registrations.statuses') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('processingStatus') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.training_registrations.labels.payment_status') }}</span>
                            <select wire:model="paymentStatus">
                                @foreach (__('admin.training_registrations.payment_statuses') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('paymentStatus') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.training_registrations.labels.payment_mode') }}</span>
                            <select wire:model="paymentMode">
                                @foreach (__('admin.training_registrations.payment_modes') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('paymentMode') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.training_registrations.labels.payment_reference') }}</span>
                            <input type="text" wire:model="paymentReference" />
                            @error('paymentReference') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.training_registrations.labels.amount_paid') }}</span>
                            <input type="number" min="0" step="0.01" wire:model="amountPaid" />
                            @error('amountPaid') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-checkline">
                            <input type="checkbox" wire:model="isSuspended" />
                            <span>{{ __('admin.training_registrations.labels.suspension') }}</span>
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.training_registrations.labels.suspension_reason') }}</span>
                            <textarea wire:model="suspensionReason" rows="3"></textarea>
                            @error('suspensionReason') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.training_registrations.labels.admin_notes') }}</span>
                            <textarea wire:model="adminNotes" rows="4"></textarea>
                            @error('adminNotes') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.training_registrations.messages.reply') }}</span>
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
                            <button type="submit" class="panel-primary-btn">{{ __('admin.training_registrations.process') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.training_registrations.empty') }}</p>
            @endif
        </article>
    </section>
</div>
