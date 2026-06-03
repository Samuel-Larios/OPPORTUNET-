<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('opportunites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->nullable()
                ->constrained('categories')->nullOnDelete();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('titre', 200);
            $table->string('slug', 230)->unique();
            $table->string('organisation', 150)->nullable();
            $table->string('logo_organisation')->nullable();
            $table->enum('type', [
                'emploi',
                'stage',
                'bourse',
                'appel_offre',
                'volontariat',
                'formation_externe',
                'autre'
            ])->default('emploi');
            $table->enum('contrat', [
                'cdi',
                'cdd',
                'stage',
                'freelance',
                'temps_partiel',
                'bénévolat',
                'non_applicable'
            ])->nullable();
            $table->string('lieu', 150)->nullable();
            $table->string('pays', 80)->nullable();
            $table->boolean('teletravail')->default(false);
            $table->text('description');
            $table->text('profil_recherche')->nullable();
            $table->text('avantages')->nullable();
            $table->string('lien_candidature', 500)->nullable();
            $table->string('email_candidature', 191)->nullable();
            $table->decimal('salaire_min', 12, 2)->nullable();
            $table->decimal('salaire_max', 12, 2)->nullable();
            $table->string('devise_salaire', 10)->default('XOF');
            $table->date('date_expiration')->nullable();
            $table->date('date_publication')->nullable();
            $table->enum('statut', ['brouillon', 'publie', 'expire', 'archive'])
                ->default('brouillon');
            $table->boolean('en_vedette')->default(false);
            $table->boolean('urgent')->default(false);
            $table->unsignedInteger('vues')->default(0);
            $table->unsignedInteger('candidatures')->default(0);
            $table->string('source', 200)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['statut', 'date_expiration']);
            $table->index(['pays', 'type']);
            $table->index('en_vedette');
            // Note : SQLite ne supporte pas les index FULLTEXT.
            // $table->fullText(['titre', 'description']);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('opportunites');
    }
};
