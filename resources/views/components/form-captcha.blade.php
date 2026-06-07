@props([
    'wrapperClass' => 'panel-field',
    'errorClass' => 'text-danger',
])

@php($captchaEnabled = \App\Support\FormCaptcha::isEnabled())

@if ($captchaEnabled)
    <label class="{{ $wrapperClass }}">
        <span>{{ __('security.captcha.label') }}</span>
        <small>{{ __('security.captcha.help', ['operation' => \App\Support\FormCaptcha::prompt()]) }}</small>
        <input
            type="text"
            name="captcha_answer"
            value="{{ old('captcha_answer') }}"
            placeholder="{{ __('security.captcha.placeholder') }}"
            autocomplete="off" />
        @error('captcha_answer')
            <small class="{{ $errorClass }}">{{ $message }}</small>
        @enderror
    </label>
@endif
