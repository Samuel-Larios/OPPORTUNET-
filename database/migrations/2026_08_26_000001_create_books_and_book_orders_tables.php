<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('books', function (Blueprint $table) {
            $table->id(); $table->string('title'); $table->string('slug')->unique(); $table->text('description');
            $table->string('author'); $table->date('published_on'); $table->unsignedInteger('price');
            $table->string('currency', 3)->default('XOF'); $table->string('cover_path')->nullable();
            $table->string('document_path'); $table->boolean('is_published')->default(false); $table->timestamps();
        });
        Schema::create('book_orders', function (Blueprint $table) {
            $table->id(); $table->uuid('reference')->unique(); $table->foreignId('book_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); $table->unsignedInteger('amount');
            $table->string('status', 30)->default('pending'); $table->string('provider_transaction_id')->nullable()->unique();
            $table->string('checkout_url')->nullable(); $table->string('unlock_code', 64)->nullable();
            $table->string('secured_document_path')->nullable(); $table->timestamp('paid_at')->nullable(); $table->timestamp('delivered_at')->nullable(); $table->timestamps();
            $table->index(['user_id', 'status']);
        });
    }
    public function down(): void { Schema::dropIfExists('book_orders'); Schema::dropIfExists('books'); }
};
