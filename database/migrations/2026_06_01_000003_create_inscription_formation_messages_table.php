<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inscription_formation_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_formation_id')
                ->constrained('inscriptions_formations')
                ->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()
                ->constrained('users')->nullOnDelete();
            $table->enum('sender_role', ['admin', 'user']);
            $table->text('message')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->timestamps();

            $table->index(['inscription_formation_id', 'created_at'], 'ifm_registration_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inscription_formation_messages');
    }
};
