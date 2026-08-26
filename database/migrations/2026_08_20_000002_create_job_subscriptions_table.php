<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_subscription_payments', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('weeks');
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('XOF');
            $table->string('provider')->default('fedapay');
            $table->string('provider_transaction_id')->nullable()->unique();
            $table->string('provider_reference')->nullable();
            $table->string('status')->default('pending');
            $table->string('checkout_url', 1000)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_subscription_payments');
    }
};
