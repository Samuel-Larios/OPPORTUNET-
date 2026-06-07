<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $hasFrenchColumn = Schema::hasColumn('parametres_site', 'valeur_fr');
        $hasEnglishColumn = Schema::hasColumn('parametres_site', 'valeur_en');

        $settings = [
            [
                'cle' => 'security_captcha_enabled',
                'valeur' => '1',
                'label' => 'Activer le CAPTCHA sur les formulaires publics',
            ],
            [
                'cle' => 'security_ip_auto_block_threshold',
                'valeur' => '5',
                'label' => 'Seuil de blocage automatique IP',
            ],
            [
                'cle' => 'security_ip_auto_block_window_minutes',
                'valeur' => '60',
                'label' => 'Fenetre d analyse des incidents (minutes)',
            ],
            [
                'cle' => 'security_ip_block_duration_minutes',
                'valeur' => '1440',
                'label' => 'Duree du blocage automatique IP (minutes)',
            ],
            [
                'cle' => 'security_geo_mode',
                'valeur' => 'off',
                'label' => 'Mode de filtrage geographique',
            ],
            [
                'cle' => 'security_geo_countries',
                'valeur' => '',
                'label' => 'Liste des pays geographiques (codes ISO)',
            ],
            [
                'cle' => 'security_geo_header',
                'valeur' => 'CF-IPCountry',
                'label' => 'En-tete de pays utilise par le proxy/CDN',
            ],
        ];

        foreach ($settings as $setting) {
            $payload = [
                'valeur' => $setting['valeur'],
                'type' => 'texte',
                'groupe' => 'securite',
                'label' => $setting['label'],
                'description' => null,
                'public' => false,
                'updated_at' => $now,
                'created_at' => $now,
            ];

            if ($hasFrenchColumn) {
                $payload['valeur_fr'] = $setting['valeur'];
            }

            if ($hasEnglishColumn) {
                $payload['valeur_en'] = $setting['valeur'];
            }

            DB::table('parametres_site')->updateOrInsert(
                ['cle' => $setting['cle']],
                $payload
            );
        }
    }

    public function down(): void
    {
        DB::table('parametres_site')->whereIn('cle', [
            'security_captcha_enabled',
            'security_ip_auto_block_threshold',
            'security_ip_auto_block_window_minutes',
            'security_ip_block_duration_minutes',
            'security_geo_mode',
            'security_geo_countries',
            'security_geo_header',
        ])->delete();
    }
};
