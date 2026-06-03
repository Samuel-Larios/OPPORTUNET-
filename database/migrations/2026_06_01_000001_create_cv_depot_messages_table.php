<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cv_depot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cv_depot_id')
                ->constrained('cv_depots')
                ->cascadeOnDelete();
            $table->foreignId('sender_id')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->enum('sender_role', ['user', 'admin']);
            $table->text('message')->nullable();
            $table->string('attachment_path')->nullable();
            $table->string('attachment_name')->nullable();
            $table->string('attachment_mime', 120)->nullable();
            $table->timestamps();

            $table->index(['cv_depot_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cv_depot_messages');
    }
};
