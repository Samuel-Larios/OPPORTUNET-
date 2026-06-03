<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->string('subject', 255);
            $table->string('audience', 80)->default('platform_users_and_subscribers');
            $table->string('content_type', 120)->nullable();
            $table->unsignedBigInteger('content_id')->nullable();
            $table->string('content_title', 255);
            $table->string('content_url', 1000)->nullable();
            $table->string('status', 30)->default('sent');
            $table->unsignedInteger('recipients_count')->default(0);
            $table->timestamp('sent_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['content_type', 'content_id']);
            $table->index(['status', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletters');
    }
};
