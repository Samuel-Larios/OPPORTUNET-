<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bannieres', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 200);
            $table->text('sous_titre')->nullable();
            $table->string('image', 500)->nullable();
            $table->string('image_mobile', 500)->nullable();
            $table->string('bouton1_texte', 80)->nullable();
            $table->string('bouton1_lien', 500)->nullable();
            $table->string('bouton1_style', 40)->default('primary');
            $table->string('bouton2_texte', 80)->nullable();
            $table->string('bouton2_lien', 500)->nullable();
            $table->string('bouton2_style', 40)->default('whatsapp');
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bannieres');
    }
};
