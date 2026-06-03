<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')->restrictOnDelete();
            $table->foreignId('categorie_id')->nullable()
                ->constrained('categories')->nullOnDelete();

            $table->string('titre', 250);
            $table->string('slug', 280)->unique();
            $table->text('extrait')->nullable();
            $table->longText('contenu');

            $table->string('image_couverture')->nullable();
            $table->string('image_alt', 200)->nullable();
            $table->string('meta_titre', 200)->nullable();
            $table->text('meta_description')->nullable();

            $table->json('tags')->nullable();

            $table->enum('statut', ['brouillon', 'publie', 'archive'])
                ->default('brouillon');
            $table->timestamp('publie_le')->nullable();
            $table->boolean('en_vedette')->default(false);
            $table->boolean('commentaires_actifs')->default(true);

            $table->unsignedInteger('vues')->default(0);
            $table->unsignedInteger('partages')->default(0);
            $table->string('temps_lecture', 20)->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['statut', 'publie_le']);
            $table->index('en_vedette');
            // Note : SQLite ne supporte pas les index FULLTEXT.
            // $table->fullText(['titre', 'extrait', 'contenu']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_articles');
    }
};
