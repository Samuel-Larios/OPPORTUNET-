<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidature_offre_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidature_offre_id')
                ->constrained('candidatures_offres')
                ->cascadeOnDelete();
            $table->foreignId('sender_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('sender_role', ['admin', 'user']);
            $table->text('message')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->timestamps();

            $table->index(['candidature_offre_id', 'created_at'], 'com_application_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidature_offre_messages');
    }
};
