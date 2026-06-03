<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('prenom', 80);
            $table->string('nom', 80)->nullable();
            $table->string('email', 191);
            $table->string('telephone', 20)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->string('pays', 80)->nullable();

            $table->enum('sujet', [
                'information',
                'service',
                'formation',
                'offre',
                'partenariat',
                'technique',
                'autre'
            ])->default('information');

            $table->string('sujet_personnalise', 200)->nullable();
            $table->text('message');

            $table->enum('priorite', ['normale', 'urgente'])->default('normale');
            $table->enum('statut', ['non_lu', 'lu', 'en_cours', 'repondu', 'archive'])
                ->default('non_lu');

            $table->text('reponse_admin')->nullable();

            $table->foreignId('traite_par')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->timestamp('repondu_le')->nullable();

            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['statut', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
