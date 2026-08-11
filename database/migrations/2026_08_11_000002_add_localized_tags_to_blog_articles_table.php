<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_articles', function (Blueprint $table): void {
            $table->json('tags_fr')->nullable()->after('tags');
            $table->json('tags_en')->nullable()->after('tags_fr');
        });
    }

    public function down(): void
    {
        Schema::table('blog_articles', function (Blueprint $table): void {
            $table->dropColumn(['tags_fr', 'tags_en']);
        });
    }
};
