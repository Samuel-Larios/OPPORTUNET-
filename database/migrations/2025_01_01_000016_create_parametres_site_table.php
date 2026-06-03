<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parametres_site', function (Blueprint $table) {
            $table->id();
            $table->string('cle', 120)->unique();
            $table->text('valeur')->nullable();
            $table->string('type', 40)->default('texte');
            $table->string('groupe', 80)->default('general');
            $table->string('label', 200)->nullable();
            $table->text('description')->nullable();
            $table->boolean('public')->default(false);
            $table->timestamps();

            $table->index('groupe');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametres_site');
    }
};
