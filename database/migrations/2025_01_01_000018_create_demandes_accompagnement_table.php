<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_accompagnement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->foreignId('service_id')->nullable()
                ->constrained('services')->nullOnDelete();

            $table->string('prenom', 80);
            $table->string('nom', 80);
            $table->string('email', 191);
            $table->string('telephone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('pays', 80)->nullable();

            $table->text('besoin');
            $table->text('objectif')->nullable();
            $table->string('budget', 100)->nullable();

            $table->enum('mode_contact_prefere', [
                'whatsapp',
                'email',
                'telephone',
                'presentiel'
            ])->default('whatsapp');

            $table->enum('disponibilite', [
                'matin',
                'apres_midi',
                'soir',
                'flexible'
            ])->default('flexible');

            $table->enum('statut', [
                'nouveau',
                'contacte',
                'en_cours',
                'termine',
                'annule'
            ])->default('nouveau');

            $table->text('notes_admin')->nullable();
            $table->decimal('montant_facture', 10, 2)->nullable();
            $table->string('devise', 10)->default('XOF');

            $table->foreignId('coach_assigne')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->timestamp('suivi_le')->nullable();
            $table->timestamps();

            $table->index(['statut', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_accompagnement');
    }
};
