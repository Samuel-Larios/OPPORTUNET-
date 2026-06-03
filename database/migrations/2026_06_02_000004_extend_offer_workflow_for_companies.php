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
                ALTER TABLE opportunites
                MODIFY statut ENUM('brouillon', 'en_attente_validation', 'publie', 'rejete', 'expire', 'archive')
                NOT NULL DEFAULT 'brouillon'
            ");
        }

        Schema::table('opportunites', function (Blueprint $table) {
            if (! Schema::hasColumn('opportunites', 'valide_par')) {
                $table->foreignId('valide_par')->nullable()->after('statut')
                    ->constrained('users')->nullOnDelete();
            }

            if (! Schema::hasColumn('opportunites', 'valide_le')) {
                $table->timestamp('valide_le')->nullable()->after('valide_par');
            }

            if (! Schema::hasColumn('opportunites', 'notes_validation_admin')) {
                $table->text('notes_validation_admin')->nullable()->after('valide_le');
            }
        });
    }

    public function down(): void
    {
        Schema::table('opportunites', function (Blueprint $table) {
            if (Schema::hasColumn('opportunites', 'valide_par')) {
                $table->dropConstrainedForeignId('valide_par');
            }

            $columns = [];

            if (Schema::hasColumn('opportunites', 'notes_validation_admin')) {
                $columns[] = 'notes_validation_admin';
            }

            if (Schema::hasColumn('opportunites', 'valide_le')) {
                $columns[] = 'valide_le';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE opportunites
                MODIFY statut ENUM('brouillon', 'publie', 'expire', 'archive')
                NOT NULL DEFAULT 'brouillon'
            ");
        }
    }
};
