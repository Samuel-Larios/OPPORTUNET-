<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('roles')->updateOrInsert(
            ['nom' => 'entreprise'],
            [
                'libelle' => 'Entreprise',
                'description' => 'Compte entreprise pouvant soumettre des offres a validation et suivre les profils proposes.',
                'permissions' => json_encode(['submit_offers', 'review_selected_profiles']),
                'actif' => true,
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );
    }

    public function down(): void
    {
        DB::table('roles')->where('nom', 'entreprise')->delete();
    }
};
