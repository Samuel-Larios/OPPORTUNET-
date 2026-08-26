<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('opportunites', function (Blueprint $table) {
            $table->boolean('is_premium')->default(false)->after('urgent');
            $table->index(['statut', 'is_premium']);
        });
    }

    public function down(): void
    {
        Schema::table('opportunites', function (Blueprint $table) {
            $table->dropIndex(['statut', 'is_premium']);
            $table->dropColumn('is_premium');
        });
    }
};
