@php
    $children = $commentsByParent->get($comment->id, collect());
@endphp

<article class="contact-form-card" style="margin-top: 16px;">
    <div class="article-detail-meta" style="margin-bottom: 12px;">
        <strong>{{ $comment->authorLabel() }}</strong>
        <span>{{ $comment->created_at->format('d/m/Y H:i') }}</span>
    </div>

    <p>{{ $comment->contenu }}</p>

    <div class="contact-form-actions">
        <a href="{{ route('articles.show', ['article' => $article->slug, 'reply' => $comment->id]) }}#article-comments" class="ghost-submit">
            {{ __('articles.comments.reply_action') }}
        </a>
    </div>

    @if ($children->isNotEmpty())
        <div style="margin-left: 24px;">
            @foreach ($children as $childComment)
                @include('articles.partials.comment', [
                    'article' => $article,
                    'comment' => $childComment,
                    'commentsByParent' => $commentsByParent,
                ])
            @endforeach
        </div>
    @endif
</article>
