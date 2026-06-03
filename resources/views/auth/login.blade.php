<x-layouts.auth :title="__('admin.auth.login_title')">
    <form method="POST" action="{{ route('login.store') }}" class="panel-auth-form">
        @csrf
        <input type="hidden" name="redirect_to" value="{{ request('redirect_to', old('redirect_to')) }}" />

        <label class="panel-field">
            <span>{{ __('admin.auth.email') }}</span>
            <input type="email" name="email" value="{{ old('email') }}" required />
            @error('email')
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

        <label class="panel-checkline">
            <input type="checkbox" name="remember" value="1" />
            <span>{{ __('admin.auth.remember') }}</span>
        </label>

        <button type="submit" class="panel-primary-btn">{{ __('admin.auth.login_submit') }}</button>
    </form>

    <p class="panel-auth-alt">
        {{ __('admin.auth.no_account') }}
        <a href="{{ route('register.user', ['redirect_to' => request('redirect_to', old('redirect_to'))]) }}">{{ __('admin.auth.create_simple_user_account') }}</a>
    </p>

    <p class="panel-auth-alt">
        {{ __('admin.auth.company_account_hint') }}
        <a href="{{ route('register.company', ['redirect_to' => request('redirect_to', old('redirect_to'))]) }}">{{ __('admin.auth.create_company_account') }}</a>
    </p>
</x-layouts.auth>
