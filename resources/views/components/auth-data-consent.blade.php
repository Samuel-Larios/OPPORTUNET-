@php
    $privacyUrl = route('site.privacy');
@endphp

<div class="auth-data-consent" data-consent-modal aria-hidden="false" role="dialog" aria-modal="true" aria-labelledby="consent-modal-title">
    <div class="auth-data-consent__dialog">
        <p class="section-label">{{ __('admin.auth.data_consent_label') }}</p>
        <h2 id="consent-modal-title">{{ __('admin.auth.data_consent_title') }}</h2>
        <p>{{ __('admin.auth.data_consent_text') }}</p>
        <label class="auth-data-consent__check">
            <input type="checkbox" data-consent-modal-checkbox />
            <span>{!! __('admin.auth.data_consent_checkbox', ['privacy_url' => $privacyUrl]) !!}</span>
        </label>
        <button type="button" class="panel-primary-btn" data-consent-continue disabled>{{ __('admin.auth.data_consent_continue') }}</button>
    </div>
</div>

<div class="auth-data-consent__form-copy">
    <p>{{ __('admin.auth.data_consent_text') }}</p>
    <label class="auth-data-consent__check">
        <input type="checkbox" name="personal_data_consent" value="1" data-consent-form-checkbox {{ old('personal_data_consent') ? 'checked' : '' }} required />
        <span>{!! __('admin.auth.data_consent_checkbox', ['privacy_url' => $privacyUrl]) !!}</span>
    </label>
    @error('personal_data_consent')
        <small>{{ $message }}</small>
    @enderror
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const modal = document.querySelector('[data-consent-modal]');
                const modalCheckbox = document.querySelector('[data-consent-modal-checkbox]');
                const formCheckbox = document.querySelector('[data-consent-form-checkbox]');
                const continueButton = document.querySelector('[data-consent-continue]');

                if (!modal || !modalCheckbox || !formCheckbox || !continueButton) return;

                modalCheckbox.addEventListener('change', function () {
                    continueButton.disabled = !this.checked;
                });

                continueButton.addEventListener('click', function () {
                    if (!modalCheckbox.checked) return;

                    formCheckbox.checked = true;
                    modal.hidden = true;
                    modal.setAttribute('aria-hidden', 'true');
                    formCheckbox.focus();
                });
            });
        </script>
    @endpush
@endonce
