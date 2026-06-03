<x-layouts.auth :title="__('admin.auth.company_register_title')">
    <form method="POST" action="{{ route('register.store') }}" class="panel-auth-form panel-auth-form-grid">
        @csrf
        <input type="hidden" name="redirect_to" value="{{ request('redirect_to', old('redirect_to')) }}" />

        <label class="panel-field">
            <span>{{ __('admin.auth.first_name') }}</span>
            <input type="text" name="prenom" value="{{ old('prenom') }}" required />
            @error('prenom')
                <small>{{ $message }}</small>
            @enderror
        </label>

        <label class="panel-field">
            <span>{{ __('admin.auth.last_name') }}</span>
            <input type="text" name="nom" value="{{ old('nom') }}" required />
            @error('nom')
                <small>{{ $message }}</small>
            @enderror
        </label>

        <label class="panel-field">
            <span>{{ __('admin.auth.email') }}</span>
            <input type="email" name="email" value="{{ old('email') }}" required />
            @error('email')
                <small>{{ $message }}</small>
            @enderror
        </label>

        <label class="panel-field">
            <span>{{ __('admin.auth.phone') }}</span>
            <input type="text" name="telephone" value="{{ old('telephone') }}" />
            @error('telephone')
                <small>{{ $message }}</small>
            @enderror
        </label>

        <label class="panel-field">
            <span>{{ __('admin.auth.country') }}</span>
            <input type="text" name="pays" value="{{ old('pays') }}" />
            @error('pays')
                <small>{{ $message }}</small>
            @enderror
        </label>

        <label class="panel-field">
            <span>{{ __('admin.auth.password') }}</span>
            <input type="password" name="password" required />
            @error('password')
                <small>{{ $message }}</small>
            @enderror
        </label>

        <label class="panel-field">
            <span>{{ __('admin.auth.password_confirmation') }}</span>
            <input type="password" name="password_confirmation" required />
        </label>

        <button type="submit" class="panel-primary-btn">{{ __('admin.auth.register_company_submit') }}</button>
    </form>

    <p class="panel-auth-alt">
        {{ __('admin.auth.have_account') }}
        <a href="{{ route('login', ['redirect_to' => request('redirect_to', old('redirect_to'))]) }}">{{ __('admin.auth.login_submit') }}</a>
    </p>

    <p class="panel-auth-alt">
        {{ __('admin.auth.user_account_hint') }}
        <a href="{{ route('register.user', ['redirect_to' => request('redirect_to', old('redirect_to'))]) }}">{{ __('admin.auth.create_simple_user_account') }}</a>
    </p>
</x-layouts.auth>
