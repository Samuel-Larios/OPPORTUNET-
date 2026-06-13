@props([
    'url',
    'title',
    'text' => null,
    'variant' => 'compact',
])

@php
    $shareUrl = (string) $url;
    $shareTitle = trim((string) $title);
    $shareText = trim(preg_replace('/\s+/', ' ', strip_tags((string) $text)));
    $shareText = $shareText !== '' ? \Illuminate\Support\Str::limit($shareText, 220) : '';
    $shareSummary = $shareText !== '' ? $shareText : '';
    $encodedUrl = urlencode($shareUrl);
    $shareSnippet = $shareSummary !== '' ? trim($shareTitle . ' - ' . $shareSummary) : $shareTitle;
    $shareMessage = trim($shareSnippet . ' ' . $shareUrl);
    $shareCopyPayload = trim($shareTitle . ($shareSummary !== '' ? "\n\n" . $shareSummary : '') . "\n\n" . $shareUrl);
    $encodedText = urlencode($shareMessage);
    $isCompact = $variant === 'compact';
@endphp

@once
    <style>
        .share-links {
            display: grid;
            gap: 10px;
            margin-top: 14px;
        }

        .share-links-label {
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6e8796;
        }

        .share-links--compact .share-links-label {
            display: none;
        }

        .share-links-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .share-link-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
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

        .share-link-btn:hover {
            transform: translateY(-1px);
            border-color: rgba(15, 90, 131, 0.28);
            box-shadow: 0 10px 22px rgba(15, 90, 131, 0.08);
        }

        .share-links--compact .share-link-btn {
            min-width: 38px;
            min-height: 38px;
            padding: 8px;
            justify-content: center;
        }

        .share-links--compact .share-link-text {
            display: none;
        }

        .share-link-icon {
            width: 16px;
            height: 16px;
            display: inline-block;
            flex: 0 0 16px;
        }

        .share-link-icon svg {
            width: 100%;
            height: 100%;
            display: block;
            fill: currentColor;
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
            const textNode = button.querySelector('[data-share-copy-label]');

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

                if (textNode) {
                    textNode.textContent = copiedLabel;
                } else {
                    button.textContent = copiedLabel;
                }

                window.setTimeout(() => {
                    if (textNode) {
                        textNode.textContent = defaultLabel;
                    } else {
                        button.textContent = defaultLabel;
                    }
                }, 1800);
            } catch (error) {
                window.prompt('Copy this text:', shareUrl);
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
            <span class="share-link-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.7-1.6h1.5V4.8c-.3 0-1.2-.1-2.3-.1-2.3 0-3.9 1.4-3.9 4v2.3H8v3h2.5v8z"/></svg>
            </span>
            <span class="share-link-text">{{ __('share.facebook') }}</span>
        </a>

        <a
            href="https://x.com/intent/post?text={{ $encodedText }}"
            target="_blank"
            rel="noopener"
            class="share-link-btn"
            aria-label="{{ __('share.x') }}"
        >
            <span class="share-link-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M18.9 3H22l-6.8 7.8L23 21h-6.1l-4.8-6.3L6.6 21H3.5l7.3-8.4L2 3h6.2l4.3 5.8L18.9 3zm-1.1 16h1.7L7.3 4.9H5.5z"/></svg>
            </span>
            <span class="share-link-text">{{ __('share.x') }}</span>
        </a>

        <a
            href="https://www.linkedin.com/sharing/share-offsite/?url={{ $encodedUrl }}"
            target="_blank"
            rel="noopener"
            class="share-link-btn"
            aria-label="{{ __('share.linkedin') }}"
        >
            <span class="share-link-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M6.9 8.1A1.8 1.8 0 1 1 6.9 4.5a1.8 1.8 0 0 1 0 3.6zM5.3 9.7h3.2V20H5.3zM10.4 9.7h3v1.4h.1c.4-.8 1.5-1.7 3.1-1.7 3.3 0 3.9 2.1 3.9 4.9V20h-3.2v-5c0-1.2 0-2.8-1.7-2.8s-2 1.3-2 2.7V20h-3.2z"/></svg>
            </span>
            <span class="share-link-text">{{ __('share.linkedin') }}</span>
        </a>

        <a
            href="https://t.me/share/url?url={{ $encodedUrl }}&text={{ urlencode($shareSnippet) }}"
            target="_blank"
            rel="noopener"
            class="share-link-btn"
            aria-label="{{ __('share.telegram') }}"
        >
            <span class="share-link-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M20.7 4.3 2.9 11.2c-1.2.5-1.2 1.2-.2 1.5l4.6 1.4 1.8 5.6c.2.6.1.9.8.9.5 0 .8-.2 1.1-.5l2.2-2.1 4.6 3.4c.9.5 1.5.2 1.7-.8l3-14.1c.3-1.2-.4-1.7-1.2-1.2zm-11 13.8-1.4-4.6 10.8-6.8z"/></svg>
            </span>
            <span class="share-link-text">{{ __('share.telegram') }}</span>
        </a>

        <a
            href="https://wa.me/?text={{ $encodedText }}"
            target="_blank"
            rel="noopener"
            class="share-link-btn"
            aria-label="{{ __('share.whatsapp') }}"
        >
            <span class="share-link-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M20 3.9A11 11 0 0 0 2.9 17.1L1.5 22.5l5.5-1.4A11 11 0 1 0 20 3.9zm-8 16.2c-1.8 0-3.6-.5-5.1-1.5l-.4-.2-3.2.8.8-3.1-.2-.4A8.4 8.4 0 1 1 12 20.1zm4.6-6.3c-.3-.2-1.6-.8-1.9-.9-.3-.1-.5-.2-.7.2l-.5.7c-.2.2-.3.3-.6.1-.3-.2-1.1-.4-2.1-1.4-.8-.7-1.4-1.7-1.6-2-.2-.3 0-.4.1-.6l.4-.5.3-.5c.1-.2.1-.4 0-.6l-.7-1.8c-.2-.4-.4-.4-.6-.4h-.5c-.2 0-.6.1-.9.4-.3.3-1.1 1-1.1 2.5 0 1.5 1.1 2.9 1.3 3.1.2.2 2.2 3.3 5.3 4.7.7.3 1.3.5 1.8.6.7.2 1.4.2 1.9.1.6-.1 1.6-.7 1.9-1.3.2-.6.2-1.2.2-1.3 0-.1-.2-.2-.5-.4z"/></svg>
            </span>
            <span class="share-link-text">{{ __('share.whatsapp') }}</span>
        </a>

        <button
            type="button"
            class="share-link-btn is-copy"
            data-share-copy="{{ $shareCopyPayload }}"
            data-default-label="{{ __('share.copy') }}"
            data-copied-label="{{ __('share.copied') }}"
            aria-label="{{ __('share.copy') }}"
        >
            <span class="share-link-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24"><path d="M16 1H6a2 2 0 0 0-2 2v12h2V3h10zm3 4H10a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h9a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2zm0 16H10V7h9z"/></svg>
            </span>
            <span class="share-link-text" data-share-copy-label>{{ __('share.copy') }}</span>
        </button>
    </div>
</div>
