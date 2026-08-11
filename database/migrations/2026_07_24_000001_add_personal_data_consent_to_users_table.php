<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('personal_data_consent_at')->nullable()->after('newsletter');
            $table->string('personal_data_consent_version', 32)->nullable()->after('personal_data_consent_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['personal_data_consent_at', 'personal_data_consent_version']);
        });
    }
};
