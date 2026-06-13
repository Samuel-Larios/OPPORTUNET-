<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'categories' => false,
            'services' => false,
            'versets' => false,
            'blog_articles' => true,
            'formations' => true,
            'opportunites' => true,
        ] as $table => $needsScheduledStatus) {
            Schema::table($table, function (Blueprint $table) use ($needsScheduledStatus) {
                $table->boolean('auto_publish')->default(false)->after('updated_at');
                $table->dateTime('scheduled_for')->nullable()->after('auto_publish');
                $table->dateTime('published_at')->nullable()->after('scheduled_for');

                if ($needsScheduledStatus) {
                    $table->string('scheduled_status', 50)->nullable()->after('published_at');
                }

                $table->index(['auto_publish', 'scheduled_for']);
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'categories' => false,
            'services' => false,
            'versets' => false,
            'blog_articles' => true,
            'formations' => true,
            'opportunites' => true,
        ] as $tableName => $needsScheduledStatus) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName, $needsScheduledStatus) {
                $table->dropIndex($tableName . '_auto_publish_scheduled_for_index');

                if ($needsScheduledStatus) {
                    $table->dropColumn('scheduled_status');
                }

                $table->dropColumn(['auto_publish', 'scheduled_for', 'published_at']);
            });
        }
    }
};
