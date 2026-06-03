<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('type', 40);
            $table->string('nom', 120);
            $table->string('slug', 150)->unique();
            $table->string('icone', 80)->nullable();
            $table->string('couleur', 20)->nullable();
            $table->text('description')->nullable();
            $table->boolean('actif')->default(true);
            $table->integer('ordre')->default(0);
            $table->timestamps();

            $table->index(['type', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
