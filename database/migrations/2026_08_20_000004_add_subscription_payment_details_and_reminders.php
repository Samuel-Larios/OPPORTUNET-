<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_subscription_payments', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->after('user_id');
            $table->string('customer_email')->nullable()->after('customer_name');
            $table->string('customer_phone', 30)->nullable()->after('customer_email');
            $table->string('customer_country', 100)->nullable()->after('customer_phone');
            $table->string('payment_method', 100)->nullable()->after('provider_reference');
            $table->string('provider_status', 100)->nullable()->after('payment_method');
        });

        Schema::create('job_subscription_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30);
            $table->timestamp('sent_at');
            $table->timestamps();
            $table->unique(['job_subscription_id', 'type']);
            $table->index(['user_id', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_subscription_reminders');

        Schema::table('job_subscription_payments', function (Blueprint $table) {
            $table->dropColumn([
                'customer_name', 'customer_email', 'customer_phone', 'customer_country',
                'payment_method', 'provider_status',
            ]);
        });
    }
};
