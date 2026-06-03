<?php

namespace App\Livewire\Panel;

use App\Models\BlogArticle;
use App\Models\BlogCommentaire;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ArticleCommentsManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $statusFilter = '';

    #[Url(except: '')]
    public string $articleFilter = '';

    public ?int $selectedCommentId = null;
    public string $authorName = '';
    public string $authorEmail = '';
    public string $content = '';
    public string $processingStatus = 'en_attente';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingArticleFilter(): void
    {
        $this->resetPage();
    }

    public function selectComment(int $commentId): void
    {
        $comment = BlogCommentaire::query()
            ->with(['article', 'user', 'parent'])
            ->findOrFail($commentId);

        $this->selectedCommentId = $comment->id;
        $this->authorName = (string) $comment->auteur_nom;
        $this->authorEmail = (string) ($comment->auteur_email ?? '');
        $this->content = (string) $comment->contenu;
        $this->processingStatus = (string) $comment->statut;
    }

    public function updateComment(): void
    {
        $validated = $this->validate([
            'selectedCommentId' => ['required', 'exists:blog_commentaires,id'],
            'authorName' => ['required', 'string', 'max:100'],
            'authorEmail' => ['nullable', 'email', 'max:191'],
            'content' => ['required', 'string', 'max:4000'],
            'processingStatus' => ['required', 'in:en_attente,approuve,spam,rejete'],
        ]);

        $comment = BlogCommentaire::query()->findOrFail($validated['selectedCommentId']);

        $comment->update([
            'auteur_nom' => $validated['authorName'],
            'auteur_email' => $validated['authorEmail'] !== '' ? $validated['authorEmail'] : null,
            'contenu' => $validated['content'],
            'statut' => $validated['processingStatus'],
        ]);

        session()->flash('panel_success', __('admin.flash.article_comment_updated'));
    }

    public function render(): View
    {
        $search = trim($this->search);

        $comments = BlogCommentaire::query()
            ->with(['article', 'user', 'parent'])
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('auteur_nom', 'like', $term)
                        ->orWhere('auteur_email', 'like', $term)
                        ->orWhere('contenu', 'like', $term);
                });
            })
            ->when($this->statusFilter !== '', fn ($query) => $query->where('statut', $this->statusFilter))
            ->when($this->articleFilter !== '', fn ($query) => $query->where('article_id', (int) $this->articleFilter))
            ->orderByRaw("CASE WHEN statut = 'en_attente' THEN 0 WHEN statut = 'approuve' THEN 1 WHEN statut = 'spam' THEN 2 ELSE 3 END")
            ->latest('updated_at')
            ->paginate(10);

        $selectedComment = $this->selectedCommentId
            ? BlogCommentaire::query()->with(['article', 'user', 'parent'])->find($this->selectedCommentId)
            : $comments->first();

        if ($selectedComment && $this->selectedCommentId === null) {
            $this->selectedCommentId = $selectedComment->id;
            $this->authorName = (string) $selectedComment->auteur_nom;
            $this->authorEmail = (string) ($selectedComment->auteur_email ?? '');
            $this->content = (string) $selectedComment->contenu;
            $this->processingStatus = (string) $selectedComment->statut;
        }

        return view('livewire.panel.article-comments-manager', [
            'comments' => $comments,
            'selectedComment' => $selectedComment,
            'articles' => BlogArticle::query()->where('statut', 'publie')->orderByDesc('publie_le')->get(),
        ]);
    }
}
