<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['nom' => 'editeur'],
            [
                'libelle' => 'Editeur',
                'description' => 'Gestion des offres, publications et contenus associes.',
                'permissions' => json_encode(['manage_offers', 'manage_trainings', 'view_stats']),
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('roles')->where('nom', 'editeur')->delete();
    }
};
