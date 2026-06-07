@props([
    'url',
    'title',
    'variant' => 'compact',
])

@php
    $shareUrl = (string) $url;
    $shareTitle = trim((string) $title);
    $encodedUrl = urlencode($shareUrl);
    $encodedText = urlencode(trim($shareTitle . ' ' . $shareUrl));
@endphp

@once
    <style>
        .share-links {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .share-links--compact {
            margin-top: 12px;
        }

        .share-links--compact .share-links-label {
            display: none;
        }

        .share-links-label {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6e8796;
        }

        .share-links-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .share-links--compact .share-links-actions {
            gap: 6px;
        }

        .share-link-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 38px;
            padding: 8px 12px;
            border: 1px solid rgba(15, 90, 131, 0.14);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.94);
            color: #0f5a83;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
        }

        .share-links--compact .share-link-btn {
            min-height: 34px;
            padding: 7px 10px;
            font-size: 0.78rem;
        }

        .share-link-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(15, 90, 131, 0.28);
            box-shadow: 0 10px 22px rgba(15, 90, 131, 0.08);
        }

        .share-link-btn.is-copy {
            appearance: none;
            -webkit-appearance: none;
        }
    </style>

    <script>
        document.addEventListener('click', async function (event) {
            const button = event.target.closest('[data-share-copy]');

            if (!button) {
                return;
            }

            const shareUrl = button.getAttribute('data-share-copy');
            const defaultLabel = button.getAttribute('data-default-label') || button.textContent;
            const copiedLabel = button.getAttribute('data-copied-label') || defaultLabel;

            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(shareUrl);
                } else {
                    const tempInput = document.createElement('input');
                    tempInput.value = shareUrl;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                }

                button.textContent = copiedLabel;

                window.setTimeout(() => {
                    button.textContent = defaultLabel;
                }, 1800);
            } catch (error) {
                window.prompt('Copy this link:', shareUrl);
            }
        });
    </script>
@endonce

<div class="share-links share-links--{{ $variant }}">
    <span class="share-links-label">{{ __('share.label') }}</span>

    <div class="share-links-actions">
        <a
            href="https://www.facebook.com/sharer/sharer.php?u={{ $encodedUrl }}"
            target="_blank"
            rel="noopener"
            class="share-link-btn"
            aria-label="{{ __('share.facebook') }}"
        >
            {{ __('share.facebook') }}
        </a>

        <a
            href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}"
            target="_blank"
            rel="noopener"
            class="share-link-btn"
            aria-label="{{ __('share.linkedin') }}"
        >
            {{ __('share.linkedin') }}
        </a>

        <a
            href="https://wa.me/?text={{ $encodedText }}"
            target="_blank"
            rel="noopener"
            class="share-link-btn"
            aria-label="{{ __('share.whatsapp') }}"
        >
            {{ __('share.whatsapp') }}
        </a>

        <button
            type="button"
            class="share-link-btn is-copy"
            data-share-copy="{{ $shareUrl }}"
            data-default-label="{{ __('share.copy') }}"
            data-copied-label="{{ __('share.copied') }}"
            aria-label="{{ __('share.copy') }}"
        >
            {{ __('share.copy') }}
        </button>
    </div>
</div>
