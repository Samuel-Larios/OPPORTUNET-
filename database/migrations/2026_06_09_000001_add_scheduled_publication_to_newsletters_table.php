<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->boolean('auto_publish')->default(false)->after('updated_at');
            $table->dateTime('scheduled_for')->nullable()->after('auto_publish');
            $table->dateTime('published_at')->nullable()->after('scheduled_for');

            $table->index(['auto_publish', 'scheduled_for']);
        });
    }

    public function down(): void
    {
        Schema::table('newsletters', function (Blueprint $table) {
            $table->dropIndex('newsletters_auto_publish_scheduled_for_index');
            $table->dropColumn(['auto_publish', 'scheduled_for', 'published_at']);
        });
    }
};
