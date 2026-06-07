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
                'cle' => 'support_kkiapay_url',
                'valeur' => '',
                'type' => 'texte',
                'groupe' => 'paiement',
                'label' => 'Lien KKIA Pay',
                'description' => 'Lien de paiement ou de collecte KKIA Pay utilise dans la section Nous soutenir.',
                'public' => false,
            ],
            [
                'cle' => 'support_bank_name',
                'valeur' => '',
                'type' => 'texte',
                'groupe' => 'paiement',
                'label' => 'Nom de la banque',
                'description' => 'Nom de la banque pour les virements de soutien.',
                'public' => false,
            ],
            [
                'cle' => 'support_bank_account_name',
                'valeur' => '',
                'type' => 'texte',
                'groupe' => 'paiement',
                'label' => 'Titulaire du compte bancaire',
                'description' => 'Nom du titulaire du compte bancaire utilise pour les soutiens.',
                'public' => false,
            ],
            [
                'cle' => 'support_bank_account_number',
                'valeur' => '',
                'type' => 'texte',
                'groupe' => 'paiement',
                'label' => 'Numero de compte bancaire',
                'description' => 'Numero de compte bancaire a afficher dans la section Nous soutenir.',
                'public' => false,
            ],
            [
                'cle' => 'support_bank_iban',
                'valeur' => '',
                'type' => 'texte',
                'groupe' => 'paiement',
                'label' => 'IBAN',
                'description' => 'IBAN du compte de soutien si applicable.',
                'public' => false,
            ],
            [
                'cle' => 'support_bank_swift',
                'valeur' => '',
                'type' => 'texte',
                'groupe' => 'paiement',
                'label' => 'Code SWIFT / BIC',
                'description' => 'Code SWIFT ou BIC du compte bancaire si applicable.',
                'public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            $payload = [
                'valeur' => $setting['valeur'],
                'type' => $setting['type'],
                'groupe' => $setting['groupe'],
                'label' => $setting['label'],
                'description' => $setting['description'],
                'public' => $setting['public'],
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
        DB::table('parametres_site')
            ->whereIn('cle', [
                'support_kkiapay_url',
                'support_bank_name',
                'support_bank_account_name',
                'support_bank_account_number',
                'support_bank_iban',
                'support_bank_swift',
            ])
            ->delete();
    }
};
