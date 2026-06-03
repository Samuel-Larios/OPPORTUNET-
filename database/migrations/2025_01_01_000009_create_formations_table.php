<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->nullable()
                ->constrained('categories')->nullOnDelete();
            $table->foreignId('formateur_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('titre', 200);
            $table->string('slug', 230)->unique();
            $table->text('description_courte');
            $table->longText('description_longue')->nullable();
            $table->string('image_couverture')->nullable();

            $table->enum('mode', ['presentiel', 'en_ligne', 'hybride'])->default('en_ligne');
            $table->string('lieu', 200)->nullable();
            $table->string('lien_en_ligne', 500)->nullable();

            $table->decimal('prix', 10, 2)->nullable();
            $table->string('devise', 10)->default('XOF');
            $table->boolean('gratuit')->default(false);
            $table->integer('duree_heures')->nullable();
            $table->integer('nb_seances')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->time('heure_debut')->nullable();
            $table->string('fuseau_horaire', 50)->default('Africa/Cotonou');

            $table->unsignedInteger('places_max')->nullable();
            $table->unsignedInteger('places_restantes')->nullable();
            $table->string('niveau', 80)->nullable();
            $table->text('prerequis')->nullable();
            $table->text('objectifs')->nullable();
            $table->text('programme')->nullable();
            $table->string('certificat', 100)->nullable();

            $table->enum('statut', [
                'brouillon',
                'ouverte',
                'complete',
                'terminee',
                'annulee'
            ])->default('brouillon');

            $table->boolean('en_vedette')->default(false);
            $table->unsignedInteger('vues')->default(0);
            $table->string('whatsapp_message')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['statut', 'date_debut']);
            $table->index('en_vedette');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('formations');
    }
};
