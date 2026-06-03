<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('versets', function (Blueprint $table) {
            $table->text('reference_fr')->nullable()->after('reference');
            $table->text('reference_en')->nullable()->after('reference_fr');
        });

        DB::table('versets')->orderBy('id')->eachById(function (object $verse): void {
            DB::table('versets')
                ->where('id', $verse->id)
                ->update([
                    'reference_fr' => $verse->reference,
                    'reference_en' => $verse->reference,
                ]);
        });
    }

    public function down(): void
    {
        Schema::table('versets', function (Blueprint $table) {
            $table->dropColumn([
                'reference_fr',
                'reference_en',
            ]);
        });
    }
};
