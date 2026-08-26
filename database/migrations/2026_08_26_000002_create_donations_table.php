<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->unsignedInteger('amount');
            $table->string('currency', 3)->default('XOF');
            $table->string('donor_name', 160);
            $table->string('donor_email', 191);
            $table->string('donor_phone', 30);
            $table->string('status', 30)->default('pending');
            $table->string('provider_status', 40)->nullable();
            $table->string('provider_transaction_id')->nullable()->unique();
            $table->string('provider_reference')->nullable();
            $table->text('checkout_url')->nullable();
            $table->json('provider_payload')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('status_checked_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
