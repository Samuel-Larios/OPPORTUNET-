<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('spiritual_publications', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40);
            $table->string('titre');
            $table->string('titre_fr');
            $table->string('titre_en')->nullable();
            $table->string('slug')->unique();
            $table->text('extrait')->nullable();
            $table->text('extrait_fr')->nullable();
            $table->text('extrait_en')->nullable();
            $table->longText('contenu');
            $table->longText('contenu_fr');
            $table->longText('contenu_en')->nullable();
            $table->string('reference')->nullable();
            $table->string('reference_fr')->nullable();
            $table->string('reference_en')->nullable();
            $table->string('auteur')->nullable();
            $table->string('auteur_fr')->nullable();
            $table->string('auteur_en')->nullable();
            $table->boolean('actif')->default(true);
            $table->boolean('afficher_accueil')->default(false);
            $table->boolean('auto_publish')->default(false);
            $table->dateTime('scheduled_for')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->unsignedInteger('ordre')->default(0);
            $table->timestamps();

            $table->index(['type', 'actif']);
            $table->index(['type', 'afficher_accueil']);
            $table->index(['auto_publish', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('spiritual_publications');
    }
};
