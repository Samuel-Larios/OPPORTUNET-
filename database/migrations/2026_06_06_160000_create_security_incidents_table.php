<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('country_code', 2)->nullable()->index();
            $table->string('type', 80)->index();
            $table->string('severity', 20)->default('warning')->index();
            $table->string('reason', 255);
            $table->string('route_name', 120)->nullable()->index();
            $table->string('path', 255)->nullable();
            $table->string('method', 12)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('count_for_auto_block')->default(true)->index();
            $table->timestamp('created_at')->useCurrent()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_incidents');
    }
};
