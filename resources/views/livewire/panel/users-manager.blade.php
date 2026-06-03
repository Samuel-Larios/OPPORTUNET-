<div class="panel-stack">
    @if (session('panel_success'))
        <div class="panel-alert-success">{{ session('panel_success') }}</div>
    @endif

    <section class="panel-grid-2 panel-grid-2-wide">
        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ $editingUserId ? __('admin.users.edit_user') : __('admin.users.new_user') }}</h2>
            </div>

            <form wire:submit="saveUser" class="panel-form-grid">
                <label class="panel-field">
                    <span>{{ __('admin.auth.first_name') }}</span>
                    <input type="text" wire:model="prenom" />
                    @error('prenom') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.auth.last_name') }}</span>
                    <input type="text" wire:model="nom" />
                    @error('nom') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.auth.email') }}</span>
                    <input type="email" wire:model="email" />
                    @error('email') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.auth.phone') }}</span>
                    <input type="text" wire:model="telephone" />
                    @error('telephone') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.auth.country') }}</span>
                    <input type="text" wire:model="pays" />
                    @error('pays') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field">
                    <span>{{ __('admin.users.role') }}</span>
                    <select wire:model="roleId">
                        <option value="">Choisir</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->libelle }}</option>
                        @endforeach
                    </select>
                    @error('roleId') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-field panel-field-span">
                    <span>{{ __('admin.auth.password') }}</span>
                    <input type="password" wire:model="password" />
                    @error('password') <small>{{ $message }}</small> @enderror
                </label>

                <label class="panel-checkline panel-field-span">
                    <input type="checkbox" wire:model="actif" />
                    <span>{{ __('admin.users.active') }}</span>
                </label>

                <div class="panel-action-row panel-field-span">
                    <button type="submit" class="panel-primary-btn">{{ __('admin.users.save') }}</button>
                    <button type="button" wire:click="resetForm" class="panel-secondary-btn">{{ __('admin.users.reset') }}</button>
                </div>
            </form>
        </article>

        <article class="panel-card">
            <div class="panel-card-head">
                <h2>{{ __('admin.pages.users') }}</h2>
            </div>

            <div class="panel-toolbar">
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="{{ __('admin.users.search') }}" />
                <select wire:model.live="roleFilter">
                    <option value="">{{ __('admin.users.all_roles') }}</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->libelle }}</option>
                    @endforeach
                </select>
            </div>

            <div class="panel-table-wrap">
                <table class="panel-table">
                    <thead>
                        <tr>
                            <th>{{ __('admin.users.name') }}</th>
                            <th>{{ __('admin.users.role') }}</th>
                            <th>{{ __('admin.users.status') }}</th>
                            <th>{{ __('admin.users.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <strong>{{ $user->fullName() }}</strong>
                                    <span>{{ $user->email }}</span>
                                </td>
                                <td>{{ $user->role?->libelle }}</td>
                                <td>
                                    <span class="panel-badge {{ $user->actif ? 'is-success' : 'is-muted' }}">
                                        {{ $user->actif ? __('admin.users.active') : __('admin.users.inactive') }}
                                    </span>
                                </td>
                                <td class="panel-inline-actions">
                                    <button type="button" wire:click="editUser({{ $user->id }})" class="panel-secondary-btn panel-small-btn">{{ __('admin.users.edit') }}</button>
                                    <button type="button" wire:click="toggleUser({{ $user->id }})" class="panel-secondary-btn panel-small-btn">{{ __('admin.users.toggle') }}</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="panel-pagination">
                    {{ $users->links() }}
                </div>
            @endif
        </article>
    </section>
</div>
