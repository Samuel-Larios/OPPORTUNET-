<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->nullable()
                ->constrained('categories')->nullOnDelete();
            $table->string('titre', 150);
            $table->string('slug', 180)->unique();
            $table->text('description_courte');
            $table->longText('description_longue')->nullable();
            $table->string('icone', 80)->nullable();
            $table->string('image')->nullable();
            $table->enum('type', [
                'redaction_cv',
                'coaching',
                'orientation',
                'accompagnement',
                'autre'
            ])->default('autre');
            $table->decimal('prix', 10, 2)->nullable();
            $table->string('devise', 10)->default('XOF');
            $table->string('duree', 80)->nullable();
            $table->string('whatsapp_message')->nullable();
            $table->boolean('actif')->default(true);
            $table->boolean('en_vedette')->default(false);
            $table->integer('ordre')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
