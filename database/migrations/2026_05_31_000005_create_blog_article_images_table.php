<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_article_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blog_article_id')
                ->constrained('blog_articles')
                ->cascadeOnDelete();
            $table->string('image_path', 500);
            $table->string('alt', 200)->nullable();
            $table->string('alt_fr', 200)->nullable();
            $table->string('alt_en', 200)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedTinyInteger('sort_order')->default(1);
            $table->timestamps();

            $table->index(['blog_article_id', 'sort_order']);
            $table->index(['blog_article_id', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_article_images');
    }
};
