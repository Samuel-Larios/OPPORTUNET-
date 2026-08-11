<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->string('content_preference', 30)->default('all_publications')->after('is_active');
            $table->index(['is_active', 'content_preference'], 'newsletter_subscribers_preference_index');
        });
    }

    public function down(): void
    {
        Schema::table('newsletter_subscribers', function (Blueprint $table) {
            $table->dropIndex('newsletter_subscribers_preference_index');
            $table->dropColumn('content_preference');
        });
    }
};
