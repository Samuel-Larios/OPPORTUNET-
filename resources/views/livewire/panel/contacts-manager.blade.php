<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.contacts.list_title') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.contacts.search') }}" />
                <select wire:model.live="statusFilter">
                    <option value="">{{ __('admin.contacts.all_statuses') }}</option>
                    @foreach (__('admin.contacts.statuses') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="priorityFilter">
                    <option value="">{{ __('admin.contacts.all_priorities') }}</option>
                    @foreach (__('admin.contacts.priorities') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
                <select wire:model.live="subjectFilter">
                    <option value="">{{ __('admin.contacts.all_subjects') }}</option>
                    @foreach (__('home.forms.contact.subjects') as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-list panel-list-spaced">
                @forelse ($contacts as $contact)
                    <button type="button" wire:click="selectContact({{ $contact->id }})" class="panel-application-item{{ $selectedContact && $selectedContact->id === $contact->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $contact->fullName() }}</strong>
                            <span>{{ $contact->subjectLabel() }}{{ $contact->sujet === 'autre' && $contact->sujet_personnalise ? ' - ' . $contact->sujet_personnalise : '' }}</span>
                            <span>{{ $contact->email }}</span>
                            @if ($contact->rappel_le)
                                <span>{{ __('admin.contacts.labels.reminder') }}: {{ $contact->rappel_le->format('d/m/Y H:i') }}</span>
                            @endif
                        </div>
                        <span class="panel-badge{{ $contact->priorite === 'urgente' ? '' : ' is-muted' }}">{{ $contact->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.contacts.empty') }}</p>
                @endforelse
            </div>

            @if ($contacts->hasPages())
                <div class="panel-pagination">
                    {{ $contacts->links() }}
                </div>
            @endif
        </article>

        <article class="panel-card">
            @if ($selectedContact)
                <div class="panel-card-head">
                    <h2>{{ __('admin.contacts.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    @if ($selectedContact->reminderIsDue())
                        <div class="panel-card panel-card-soft">
                            <strong>{{ __('admin.contacts.labels.reminder_due') }}</strong>
                            <p style="margin-top: 8px;">
                                {{ $selectedContact->rappel_le?->format('d/m/Y H:i') }}
                                @if ($selectedContact->rappel_note)
                                    - {{ $selectedContact->rappel_note }}
                                @endif
                            </p>
                        </div>
                    @endif

                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.contacts.labels.author') }}</span>
                            <strong>{{ $selectedContact->fullName() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.email') }}</span>
                            <strong>{{ $selectedContact->email }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.phone') }}</span>
                            <strong>{{ $selectedContact->telephone ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.whatsapp') }}</span>
                            <strong>{{ $selectedContact->whatsapp ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.country') }}</span>
                            <strong>{{ $selectedContact->pays ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.subject') }}</span>
                            <strong>{{ $selectedContact->subjectLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.custom_subject') }}</span>
                            <strong>{{ $selectedContact->sujet_personnalise ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.priority') }}</span>
                            <strong>{{ $selectedContact->priorityLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.submitted') }}</span>
                            <strong>{{ $selectedContact->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.account') }}</span>
                            <strong>{{ $selectedContact->user?->fullName() ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.processed_by') }}</span>
                            <strong>{{ $selectedContact->processedBy?->fullName() ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.contacts.labels.replied_at') }}</span>
                            <strong>{{ $selectedContact->repondu_le?->format('d/m/Y H:i') ?: '-' }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.contacts.labels.message') }}</strong>
                        <p style="margin-top: 12px;">{{ $selectedContact->message }}</p>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.contacts.messages.thread_title') }}</strong>
                        @if (! $repliesEnabled)
                            <p style="margin-top: 12px;">{{ __('admin.contacts.messages.migration_required') }}</p>
                        @else
                            <div class="panel-thread">
                                @forelse ($replyTimeline as $reply)
                                    <article class="panel-message is-admin">
                                        <div class="panel-message-head">
                                            <strong>{{ $reply->senderLabel() }}</strong>
                                            <span>{{ ($reply->sent_at ?? $reply->created_at)?->format('d/m/Y H:i') }}</span>
                                        </div>
                                        <p>{{ $reply->message }}</p>
                                        <div class="panel-action-row">
                                            <button type="button" wire:click="deleteReply({{ $reply->id }})" class="panel-secondary-btn panel-small-btn">
                                                {{ __('admin.contacts.messages.delete') }}
                                            </button>
                                        </div>
                                    </article>
                                @empty
                                    <p class="panel-empty">{{ __('admin.contacts.messages.empty') }}</p>
                                @endforelse
                            </div>
                        @endif
                    </div>

                    <form wire:submit="updateContact" class="panel-form-grid">
                        <label class="panel-field">
                            <span>{{ __('admin.contacts.labels.status') }}</span>
                            <select wire:model="processingStatus">
                                @foreach (__('admin.contacts.statuses') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('processingStatus') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.contacts.labels.priority') }}</span>
                            <select wire:model="processingPriority">
                                @foreach (__('admin.contacts.priorities') as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('processingPriority') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.contacts.labels.admin_notes') }}</span>
                            <textarea rows="4" wire:model="adminNotes"></textarea>
                            @error('adminNotes') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.contacts.labels.reminder') }}</span>
                            <input type="datetime-local" wire:model="reminderAt" />
                            @error('reminderAt') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field">
                            <span>{{ __('admin.contacts.labels.reminder_note') }}</span>
                            <input type="text" wire:model="reminderNote" />
                            @error('reminderNote') <small>{{ $message }}</small> @enderror
                        </label>

                        <div class="panel-action-row panel-field-span">
                            <button type="submit" class="panel-primary-btn">{{ __('admin.contacts.process') }}</button>
                        </div>
                    </form>

                    <form wire:submit="sendReply" class="panel-form-grid">
                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.contacts.messages.reply') }}</span>
                            <textarea rows="5" wire:model="replyMessage"></textarea>
                            @error('replyMessage') <small>{{ $message }}</small> @enderror
                        </label>

                        <div class="panel-action-row panel-field-span">
                            <button type="submit" class="panel-primary-btn" @disabled(! $repliesEnabled)>{{ __('admin.contacts.messages.send') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.contacts.empty') }}</p>
            @endif
        </article>
    </section>
</div>
