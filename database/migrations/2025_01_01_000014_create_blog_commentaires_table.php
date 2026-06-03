<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_commentaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')
                ->constrained('blog_articles')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()
                ->constrained('blog_commentaires')->cascadeOnDelete();

            $table->string('auteur_nom', 100);
            $table->string('auteur_email', 191)->nullable();
            $table->text('contenu');
            $table->string('ip_address', 45)->nullable();

            $table->enum('statut', ['en_attente', 'approuve', 'spam', 'rejete'])
                ->default('en_attente');

            $table->timestamps();

            $table->index(['article_id', 'statut']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_commentaires');
    }
};
