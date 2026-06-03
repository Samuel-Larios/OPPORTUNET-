<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()
                ->constrained('users')->nullOnDelete();

            $table->string('nom_original', 255);
            $table->string('nom_fichier', 255);
            $table->string('chemin', 500);
            $table->string('url', 500)->nullable();
            $table->string('type_mime', 100);
            $table->unsignedBigInteger('taille')->nullable();
            $table->string('extension', 20)->nullable();
            $table->integer('largeur')->nullable();
            $table->integer('hauteur')->nullable();
            $table->string('alt', 200)->nullable();
            $table->string('collection', 80)->default('general');
            $table->json('conversions')->nullable();
            $table->timestamps();

            $table->index('collection');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medias');
    }
};
