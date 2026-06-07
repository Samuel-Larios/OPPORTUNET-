<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $payload = [
            'valeur' => '',
            'type' => 'texte',
            'groupe' => 'whatsapp',
            'label' => 'Lien du groupe WhatsApp de priere',
            'description' => 'Lien d invitation du groupe WhatsApp utilise apres un clic sur Je prie aussi.',
            'public' => false,
            'updated_at' => now(),
            'created_at' => now(),
        ];

        if (Schema::hasColumn('parametres_site', 'valeur_fr')) {
            $payload['valeur_fr'] = '';
        }

        if (Schema::hasColumn('parametres_site', 'valeur_en')) {
            $payload['valeur_en'] = '';
        }

        DB::table('parametres_site')->updateOrInsert(
            ['cle' => 'whatsapp_groupe_priere_url'],
            $payload
        );
    }

    public function down(): void
    {
        DB::table('parametres_site')
            ->where('cle', 'whatsapp_groupe_priere_url')
            ->delete();
    }
};
