<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidatures_offres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();
            $table->foreignId('opportunite_id')
                ->constrained('opportunites')
                ->cascadeOnDelete();

            $table->string('prenom', 80);
            $table->string('nom', 80);
            $table->string('email', 191);
            $table->string('telephone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('pays', 80)->nullable();

            $table->string('lettre_motivation', 500);
            $table->json('diplome_fichiers');
            $table->json('attestation_fichiers');
            $table->text('message')->nullable();

            $table->enum('statut', [
                'en_attente',
                'en_revue',
                'retenue',
                'rejetee',
                'informations_complementaires',
            ])->default('en_attente');

            $table->text('notes_admin')->nullable();
            $table->foreignId('traite_par')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('traite_le')->nullable();
            $table->timestamp('email_traitement_envoye_le')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'opportunite_id']);
            $table->index(['statut', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidatures_offres');
    }
};
