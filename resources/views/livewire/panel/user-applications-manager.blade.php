<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.user_applications.list_title') }}</h2>
                <p>{{ __('admin.user_applications.intro') }}</p>
            </div>

            <div class="panel-list">
                @forelse ($applications as $application)
                    <button type="button" wire:click="selectApplication({{ $application->id }})" class="panel-application-item{{ $selectedApplication && $selectedApplication->id === $application->id ? ' is-active' : '' }}">
                        <div>
                            <strong>{{ $application->opportunite?->titre ?: __('admin.user_applications.labels.offer') }}</strong>
                            <span>{{ $application->created_at->format('d/m/Y') }}</span>
                            <span>{{ $application->email }}</span>
                        </div>
                        <span class="panel-badge">{{ $application->statusLabel() }}</span>
                    </button>
                @empty
                    <p class="panel-empty">{{ __('admin.user_applications.empty') }}</p>
                @endforelse
            </div>
        </article>

        <article class="panel-card">
            @if ($selectedApplication)
                @php
                    $timeline = $selectedApplication->messages->sortBy('created_at');
                @endphp

                <div class="panel-card-head">
                    <h2>{{ __('admin.user_applications.detail_title') }}</h2>
                </div>

                <div class="panel-application-detail">
                    <div class="panel-application-meta">
                        <div>
                            <span>{{ __('admin.user_applications.labels.status') }}</span>
                            <strong>{{ $selectedApplication->statusLabel() }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_applications.labels.submitted') }}</span>
                            <strong>{{ $selectedApplication->created_at->format('d/m/Y H:i') }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_applications.labels.offer') }}</span>
                            <strong>{{ $selectedApplication->opportunite?->titre ?: '-' }}</strong>
                        </div>
                        <div>
                            <span>{{ __('admin.user_applications.labels.phone') }}</span>
                            <strong>{{ $selectedApplication->telephone ?: '-' }}</strong>
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_applications.labels.summary') }}</strong>
                        <div class="panel-list panel-list-spaced">
                            @if ($selectedApplication->message)
                                <div class="panel-list-row panel-list-row-block">
                                    <div>
                                        <strong>{{ __('admin.user_applications.labels.message') }}</strong>
                                    </div>
                                    <p>{{ $selectedApplication->message }}</p>
                                </div>
                            @endif

                            @if ($selectedApplication->notes_admin && $timeline->isEmpty())
                                <div class="panel-list-row panel-list-row-block">
                                    <div>
                                        <strong>{{ __('admin.user_applications.labels.admin_note') }}</strong>
                                    </div>
                                    <p>{{ $selectedApplication->notes_admin }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.user_applications.labels.files') }}</strong>
                        <div class="panel-file-links">
                            <a href="{{ route('panel.user.applications.download', ['candidature' => $selectedApplication->id, 'type' => 'lettre']) }}">{{ __('admin.applications.files.letter') }}</a>
                            @foreach ($selectedApplication->diplome_fichiers ?? [] as $index => $path)
                                <a href="{{ route('panel.user.applications.download', ['candidature' => $selectedApplication->id, 'type' => 'diplome', 'index' => $index]) }}">{{ __('admin.applications.files.diploma') }} {{ $index + 1 }}</a>
                            @endforeach
                            @foreach ($selectedApplication->attestation_fichiers ?? [] as $index => $path)
                                <a href="{{ route('panel.user.applications.download', ['candidature' => $selectedApplication->id, 'type' => 'attestation', 'index' => $index]) }}">{{ __('admin.applications.files.certificate') }} {{ $index + 1 }}</a>
                            @endforeach
                        </div>
                    </div>

                    <div class="panel-card panel-card-soft">
                        <strong>{{ __('admin.application_messages.thread_title') }}</strong>
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
                                        <a href="{{ route('panel.user.applications.download-message', ['candidature' => $selectedApplication->id, 'message' => $message->id]) }}" class="panel-attachment-chip">
                                            {{ $message->attachment_name ?: __('admin.application_messages.attachment') }}
                                        </a>
                                    @endif
                                </article>
                            @empty
                                <p class="panel-empty">{{ __('admin.application_messages.empty') }}</p>
                            @endforelse
                        </div>
                    </div>

                    <form wire:submit="sendReply" class="panel-form-grid" enctype="multipart/form-data">
                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.user_applications.labels.reply') }}</span>
                            <textarea wire:model="replyMessage" rows="5"></textarea>
                            @error('replyMessage') <small>{{ $message }}</small> @enderror
                        </label>

                        <label class="panel-field panel-field-span">
                            <span>{{ __('admin.application_messages.attachment') }}</span>
                            <input type="file" wire:model="replyAttachment" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" />
                            <small>{{ __('admin.application_messages.attachment_hint') }}</small>
                            @error('replyAttachment') <small>{{ $message }}</small> @enderror
                        </label>

                        <div class="panel-action-row panel-field-span">
                            <button type="submit" class="panel-primary-btn">{{ __('admin.user_applications.send') }}</button>
                        </div>
                    </form>
                </div>
            @else
                <p class="panel-empty">{{ __('admin.user_applications.empty') }}</p>
            @endif
        </article>
    </section>
</div>
