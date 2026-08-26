<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_subscription_payments', function (Blueprint $table) {
            $table->timestamp('status_checked_at')->nullable()->after('provider_status');
        });
    }

    public function down(): void
    {
        Schema::table('job_subscription_payments', function (Blueprint $table) {
            $table->dropColumn('status_checked_at');
        });
    }
};
