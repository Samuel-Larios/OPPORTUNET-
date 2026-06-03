<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mur_de_prieres', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('prenom', 80);
            $table->string('pays', 80)->nullable();
            $table->string('email', 191)->nullable();
            $table->text('sujet');

            $table->enum('type', [
                'priere',
                'temoignage_reponse',
                'encouragement',
                'verset_partage'
            ])->default('priere');

            $table->boolean('anonyme')->default(false);
            $table->unsignedInteger('priants')->default(0);
            $table->enum('statut', ['en_attente', 'approuve', 'rejete'])
                ->default('en_attente');

            $table->timestamps();
            $table->index(['statut', 'created_at']);
        });

        Schema::create('prieres_soutiens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('priere_id')
                ->constrained('mur_de_prieres')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['priere_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prieres_soutiens');
        Schema::dropIfExists('mur_de_prieres');
    }
};
