<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscriptions_formations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formation_id')
                ->constrained('formations')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('prenom', 80);
            $table->string('nom', 80);
            $table->string('email', 191);
            $table->string('telephone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('pays', 80)->nullable();
            $table->string('profession', 120)->nullable();
            $table->string('niveau_etude', 80)->nullable();
            $table->text('motivation')->nullable();

            $table->enum('mode_paiement', [
                'mobile_money',
                'virement',
                'especes',
                'gratuit',
                'en_attente'
            ])->default('en_attente');
            $table->string('reference_paiement', 100)->nullable();
            $table->decimal('montant_paye', 10, 2)->nullable();

            $table->enum('statut_paiement', [
                'non_paye',
                'en_attente',
                'paye',
                'rembourse'
            ])->default('non_paye');

            $table->enum('statut', [
                'en_attente',
                'confirme',
                'annule',
                'liste_attente'
            ])->default('en_attente');

            $table->boolean('certificat_delivre')->default(false);
            $table->string('certificat_fichier')->nullable();
            $table->timestamp('confirme_le')->nullable();
            $table->text('notes_admin')->nullable();
            $table->timestamps();

            $table->unique(['formation_id', 'email']);
            $table->index(['statut', 'statut_paiement']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscriptions_formations');
    }
};
