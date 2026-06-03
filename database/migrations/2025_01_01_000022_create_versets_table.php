<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('versets', function (Blueprint $table) {
            $table->id();
            $table->text('reference');
            $table->text('texte');
            $table->string('version', 50)->default('LSG');
            $table->boolean('actif')->default(true);
            $table->boolean('afficher_accueil')->default(false);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('versets');
    }
};
