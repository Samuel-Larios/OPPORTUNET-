<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('formations', function (Blueprint $table) {
            $table->boolean('inscriptions_ouvertes')->default(true)->after('statut');
        });

        Schema::table('inscriptions_formations', function (Blueprint $table) {
            $table->boolean('est_suspendue')->default(false)->after('statut');
            $table->timestamp('suspendue_le')->nullable()->after('est_suspendue');
            $table->text('motif_suspension')->nullable()->after('suspendue_le');
            $table->foreignId('traite_par')->nullable()->after('notes_admin')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('traite_le')->nullable()->after('traite_par');
        });
    }

    public function down(): void
    {
        Schema::table('inscriptions_formations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('traite_par');
            $table->dropColumn([
                'est_suspendue',
                'suspendue_le',
                'motif_suspension',
                'traite_le',
            ]);
        });

        Schema::table('formations', function (Blueprint $table) {
            $table->dropColumn('inscriptions_ouvertes');
        });
    }
};
