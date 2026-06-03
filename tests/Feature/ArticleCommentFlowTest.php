<?php

namespace Tests\Feature;

use App\Livewire\Panel\ArticleCommentsManager;
use App\Models\BlogArticle;
use App\Models\BlogCommentaire;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ArticleCommentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_verified_user_comment_without_parent_id_is_saved_as_pending(): void
    {
        $article = BlogArticle::query()->where('statut', 'publie')->firstOrFail();
        $article->update(['commentaires_actifs' => true]);

        $user = User::factory()->create([
            'prenom' => 'Samuel',
            'nom' => 'Lecteur',
            'name' => 'Samuel Lecteur',
            'email_verified_at' => now(),
            'actif' => true,
        ]);

        $response = $this->actingAs($user)->post(route('articles.comments.store', $article->slug), [
            'contenu' => 'Merci pour cet article tres utile.',
        ]);

        $response->assertRedirect(route('articles.show', $article->slug) . '#article-comments');

        $this->assertDatabaseHas('blog_commentaires', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'parent_id' => null,
            'auteur_nom' => 'Samuel Lecteur',
            'statut' => 'en_attente',
        ]);
    }

    public function test_guest_comment_is_redirected_to_login(): void
    {
        $article = BlogArticle::query()->where('statut', 'publie')->firstOrFail();
        $article->update(['commentaires_actifs' => true]);

        $response = $this->post(route('articles.comments.store', $article->slug), [
            'auteur_nom' => 'Samuel',
            'auteur_email' => 'samuel.comment@example.com',
            'contenu' => 'Merci pour cet article tres utile.',
        ]);

        $response->assertRedirect(route('login', [
            'redirect_to' => route('articles.show', $article->slug) . '#article-comments',
        ]));

        $this->assertDatabaseMissing('blog_commentaires', [
            'article_id' => $article->id,
            'contenu' => 'Merci pour cet article tres utile.',
        ]);
    }

    public function test_admin_can_approve_comment_and_it_becomes_visible_on_article_page(): void
    {
        $adminRole = $this->firstOrCreateRole('admin', 'Administrateur');
        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
            'prenom' => 'Alice',
            'nom' => 'Admin',
            'actif' => true,
        ]);

        $article = BlogArticle::query()->where('statut', 'publie')->firstOrFail();
        $article->update(['commentaires_actifs' => true]);

        $comment = BlogCommentaire::query()->create([
            'article_id' => $article->id,
            'auteur_nom' => 'Samuel',
            'auteur_email' => 'samuel.comment@example.com',
            'contenu' => 'Commentaire a approuver.',
            'ip_address' => '127.0.0.1',
            'statut' => 'en_attente',
        ]);

        $this->actingAs($admin);

        Livewire::test(ArticleCommentsManager::class)
            ->call('selectComment', $comment->id)
            ->set('processingStatus', 'approuve')
            ->call('updateComment')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('blog_commentaires', [
            'id' => $comment->id,
            'statut' => 'approuve',
        ]);

        $this->get(route('articles.show', $article->slug))
            ->assertOk()
            ->assertSeeText('Commentaire a approuver.');
    }

    public function test_reply_to_approved_comment_is_saved(): void
    {
        $article = BlogArticle::query()->where('statut', 'publie')->firstOrFail();
        $article->update(['commentaires_actifs' => true]);

        $user = User::factory()->create([
            'prenom' => 'Samuel',
            'nom' => 'Lecteur',
            'name' => 'Samuel Lecteur',
            'email_verified_at' => now(),
            'actif' => true,
        ]);

        $parentComment = BlogCommentaire::query()->create([
            'article_id' => $article->id,
            'auteur_nom' => 'Naomi',
            'auteur_email' => 'naomi@example.com',
            'contenu' => 'Commentaire parent.',
            'ip_address' => '127.0.0.1',
            'statut' => 'approuve',
        ]);

        $response = $this->actingAs($user)->post(route('articles.comments.store', $article->slug), [
            'parent_id' => $parentComment->id,
            'contenu' => 'Reponse au commentaire parent.',
        ]);

        $response->assertRedirect(route('articles.show', $article->slug) . '#article-comments');

        $this->assertDatabaseHas('blog_commentaires', [
            'article_id' => $article->id,
            'user_id' => $user->id,
            'parent_id' => $parentComment->id,
            'contenu' => 'Reponse au commentaire parent.',
            'statut' => 'en_attente',
        ]);
    }

    public function test_comment_submission_is_blocked_when_comments_are_disabled(): void
    {
        $article = BlogArticle::query()->where('statut', 'publie')->firstOrFail();
        $article->update(['commentaires_actifs' => false]);

        $this->post(route('articles.comments.store', $article->slug), [
            'auteur_nom' => 'Samuel',
            'auteur_email' => 'samuel.comment@example.com',
            'contenu' => 'Commentaire non autorise.',
        ])->assertForbidden();

        $this->assertDatabaseMissing('blog_commentaires', [
            'contenu' => 'Commentaire non autorise.',
        ]);
    }

    private function firstOrCreateRole(string $name, string $label): Role
    {
        return Role::query()->firstOrCreate([
            'nom' => $name,
        ], [
            'libelle' => $label,
            'permissions' => json_encode(['*']),
            'actif' => true,
        ]);
    }
}
