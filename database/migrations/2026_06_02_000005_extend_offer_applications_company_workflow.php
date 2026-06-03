<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE candidatures_offres
                MODIFY statut ENUM(
                    'en_attente',
                    'en_revue',
                    'retenue',
                    'proposee_entreprise',
                    'validee_entreprise',
                    'rejetee',
                    'informations_complementaires'
                )
                NOT NULL DEFAULT 'en_attente'
            ");
        }

        Schema::table('candidatures_offres', function (Blueprint $table) {
            if (! Schema::hasColumn('candidatures_offres', 'proposee_entreprise_le')) {
                $table->timestamp('proposee_entreprise_le')->nullable()->after('traite_le');
            }

            if (! Schema::hasColumn('candidatures_offres', 'validee_par_entreprise')) {
                $table->foreignId('validee_par_entreprise')->nullable()->after('proposee_entreprise_le')
                    ->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('candidatures_offres', 'validee_entreprise_le')) {
                $table->timestamp('validee_entreprise_le')->nullable()->after('validee_par_entreprise');
            }

            if (! Schema::hasColumn('candidatures_offres', 'note_entreprise')) {
                $table->text('note_entreprise')->nullable()->after('validee_entreprise_le');
            }
        });
    }

    public function down(): void
    {
        Schema::table('candidatures_offres', function (Blueprint $table) {
            if (Schema::hasColumn('candidatures_offres', 'validee_par_entreprise')) {
                $table->dropConstrainedForeignId('validee_par_entreprise');
            }

            $columns = [];

            if (Schema::hasColumn('candidatures_offres', 'note_entreprise')) {
                $columns[] = 'note_entreprise';
            }

            if (Schema::hasColumn('candidatures_offres', 'validee_entreprise_le')) {
                $columns[] = 'validee_entreprise_le';
            }

            if (Schema::hasColumn('candidatures_offres', 'proposee_entreprise_le')) {
                $columns[] = 'proposee_entreprise_le';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE candidatures_offres
                MODIFY statut ENUM(
                    'en_attente',
                    'en_revue',
                    'retenue',
                    'rejetee',
                    'informations_complementaires'
                )
                NOT NULL DEFAULT 'en_attente'
            ");
        }
    }
};
