<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('spiritual_publications', function (Blueprint $table): void {
            $table->unsignedInteger('vues')->default(0)->after('afficher_accueil');
        });

        Schema::table('versets', function (Blueprint $table): void {
            $table->unsignedInteger('vues')->default(0)->after('afficher_accueil');
        });
    }

    public function down(): void
    {
        Schema::table('spiritual_publications', function (Blueprint $table): void {
            $table->dropColumn('vues');
        });

        Schema::table('versets', function (Blueprint $table): void {
            $table->dropColumn('vues');
        });
    }
};
