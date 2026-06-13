<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('candidatures_offres', 'cv_fichier')) {
            Schema::table('candidatures_offres', function (Blueprint $table) {
                $table->string('cv_fichier', 500)->nullable()->after('pays');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('candidatures_offres', 'cv_fichier')) {
            Schema::table('candidatures_offres', function (Blueprint $table) {
                $table->dropColumn('cv_fichier');
            });
        }
    }
};
