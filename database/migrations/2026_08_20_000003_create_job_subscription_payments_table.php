<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weeks');
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('XOF');
            $table->dateTime('starts_at');
            $table->dateTime('expires_at');
            $table->foreignId('payment_id')->nullable()->unique()->constrained('job_subscription_payments')->nullOnDelete();
            $table->timestamps();
            $table->index(['user_id', 'expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_subscriptions');
    }
};
