<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Note: ce projet utilise déjà une migration users générée par défaut.
        // Pour éviter les collisions lors de migrate:fresh en SQLite, on ne recrée pas ici.
        // La structure complète users (avec role_id, champs supplémentaires) devra être appliquée après.
        return;

        Schema::create('users', function (Blueprint $table) {

            $table->id();
            $table->foreignId('role_id')->default(5)
                ->constrained('roles')->restrictOnDelete();
            $table->string('prenom', 80);
            $table->string('nom', 80);
            $table->string('email', 191)->unique();
            $table->string('telephone', 20)->nullable();
            $table->string('pays', 80)->nullable();
            $table->string('ville', 80)->nullable();
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->enum('genre', ['homme', 'femme', 'non_precise'])->default('non_precise');
            $table->date('date_naissance')->nullable();
            $table->string('profession', 120)->nullable();
            $table->string('niveau_etude', 80)->nullable();
            $table->string('whatsapp', 20)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('actif')->default(true);
            $table->boolean('newsletter')->default(false);
            $table->string('langue', 10)->default('fr');
            $table->timestamp('derniere_connexion')->nullable();
            $table->string('token_verification')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['email', 'actif']);
            $table->index('pays');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
