<x-layouts.auth :title="__('admin.auth.verify_title')">
    @if (session('status') === 'email-verified')
        <p class="panel-auth-alt">{{ __('admin.auth.verify_success') }}</p>
    @endif

    @if (session('status') === 'verification-link-sent')
        <p class="panel-auth-alt">{{ __('admin.auth.verify_resent') }}</p>
    @endif

    @if ($errors->has('verification_email'))
        <div class="panel-auth-alt">
            <p>{{ $errors->first('verification_email') }}</p>
        </div>
    @endif

    <div class="panel-auth-alt">
        <p>{{ __('admin.auth.verify_intro') }}</p>
        <p><strong>{{ auth()->user()?->email }}</strong></p>
        <p>{{ __('admin.auth.verify_hint') }}</p>
    </div>

    <form method="POST" action="{{ route('verification.send') }}" class="panel-auth-form">
        @csrf
        <button type="submit" class="panel-primary-btn">{{ __('admin.auth.verify_resend') }}</button>
    </form>

    <form method="POST" action="{{ route('logout') }}" class="panel-auth-form">
        @csrf
        <button type="submit" class="panel-secondary-btn">{{ __('admin.nav.logout') }}</button>
    </form>
</x-layouts.auth>
