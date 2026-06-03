<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cv_depots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('prenom', 80);
            $table->string('nom', 80);
            $table->string('email', 191);
            $table->string('telephone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('pays', 80)->nullable();
            $table->string('ville', 80)->nullable();
            $table->date('date_naissance')->nullable();
            $table->enum('genre', ['homme', 'femme', 'non_precise'])->nullable();

            $table->string('titre_poste', 150)->nullable();
            $table->string('niveau_etude', 80)->nullable();
            $table->string('domaine_etude', 120)->nullable();
            $table->text('competences')->nullable();
            $table->text('langues')->nullable();
            $table->integer('annees_experience')->nullable();
            $table->text('objectif_professionnel')->nullable();
            $table->text('secteurs_interet')->nullable();
            $table->enum('type_contrat_recherche', [
                'cdi',
                'cdd',
                'stage',
                'freelance',
                'tous'
            ])->default('tous');
            $table->boolean('teletravail_souhaite')->default(false);

            $table->string('cv_fichier')->nullable();
            $table->string('linkedin_url', 300)->nullable();
            $table->string('portfolio_url', 300)->nullable();
            $table->text('message')->nullable();

            $table->boolean('demande_redaction_cv')->default(false);
            $table->boolean('demande_coaching')->default(false);
            $table->boolean('demande_orientation')->default(false);

            $table->enum('statut', ['nouveau', 'en_traitement', 'traite', 'archive'])
                ->default('nouveau');
            $table->text('notes_admin')->nullable();
            $table->foreignId('traite_par')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('traite_le')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['statut', 'created_at']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_depots');
    }
};
