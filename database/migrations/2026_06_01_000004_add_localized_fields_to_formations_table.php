<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->string('lieu_fr', 200)->nullable()->after('lieu');
            $table->string('lieu_en', 200)->nullable()->after('lieu_fr');
            $table->string('niveau_fr', 80)->nullable()->after('niveau');
            $table->string('niveau_en', 80)->nullable()->after('niveau_fr');
            $table->string('certificat_fr', 100)->nullable()->after('certificat');
            $table->string('certificat_en', 100)->nullable()->after('certificat_fr');
        });

        DB::table('formations')
            ->whereNotNull('lieu')
            ->update([
                'lieu_fr' => DB::raw('lieu'),
                'lieu_en' => DB::raw('lieu'),
            ]);

        DB::table('formations')
            ->whereNotNull('niveau')
            ->update([
                'niveau_fr' => DB::raw('niveau'),
                'niveau_en' => DB::raw('niveau'),
            ]);

        DB::table('formations')
            ->whereNotNull('certificat')
            ->update([
                'certificat_fr' => DB::raw('certificat'),
                'certificat_en' => DB::raw('certificat'),
            ]);
    }

    public function down(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn([
                'lieu_fr',
                'lieu_en',
                'niveau_fr',
                'niveau_en',
                'certificat_fr',
                'certificat_en',
            ]);
        });
    }
};
