<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('temoignages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('prenom', 80);
            $table->string('nom', 80)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('photo')->nullable();
            $table->string('pays', 80)->nullable();
            $table->string('profession', 120)->nullable();

            $table->text('contenu');

            $table->enum('type', [
                'emploi_trouve',
                'formation_suivie',
                'service_cv',
                'coaching',
                'general'
            ])->default('general');

            $table->unsignedTinyInteger('note')->nullable();

            $table->enum('statut', ['en_attente', 'approuve', 'rejete'])
                ->default('en_attente');

            $table->boolean('en_vedette')->default(false);
            $table->integer('ordre')->default(0);
            $table->timestamps();

            $table->index(['statut', 'en_vedette']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('temoignages');
    }
};
