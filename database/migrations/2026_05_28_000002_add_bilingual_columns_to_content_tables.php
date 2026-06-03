<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLES = [
        'categories' => [
            'nom' => ['type' => 'string', 'length' => 120],
            'description' => ['type' => 'text'],
        ],
        'services' => [
            'titre' => ['type' => 'string', 'length' => 150],
            'description_courte' => ['type' => 'text'],
            'description_longue' => ['type' => 'longText'],
            'duree' => ['type' => 'string', 'length' => 80],
            'whatsapp_message' => ['type' => 'string', 'length' => 255],
        ],
        'opportunites' => [
            'titre' => ['type' => 'string', 'length' => 200],
            'description' => ['type' => 'text'],
            'profil_recherche' => ['type' => 'text'],
            'avantages' => ['type' => 'text'],
        ],
        'formations' => [
            'titre' => ['type' => 'string', 'length' => 200],
            'description_courte' => ['type' => 'text'],
            'description_longue' => ['type' => 'longText'],
            'prerequis' => ['type' => 'text'],
            'objectifs' => ['type' => 'text'],
            'programme' => ['type' => 'text'],
            'whatsapp_message' => ['type' => 'string', 'length' => 255],
        ],
        'temoignages' => [
            'contenu' => ['type' => 'text'],
        ],
        'blog_articles' => [
            'titre' => ['type' => 'string', 'length' => 250],
            'extrait' => ['type' => 'text'],
            'contenu' => ['type' => 'longText'],
            'image_alt' => ['type' => 'string', 'length' => 200],
            'meta_titre' => ['type' => 'string', 'length' => 200],
            'meta_description' => ['type' => 'text'],
        ],
        'bannieres' => [
            'titre' => ['type' => 'string', 'length' => 200],
            'sous_titre' => ['type' => 'text'],
            'bouton1_texte' => ['type' => 'string', 'length' => 80],
            'bouton2_texte' => ['type' => 'string', 'length' => 80],
        ],
        'versets' => [
            'texte' => ['type' => 'text'],
            'version' => ['type' => 'string', 'length' => 50],
        ],
        'parametres_site' => [
            'valeur' => ['type' => 'text'],
        ],
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns): void {
                foreach ($columns as $column => $definition) {
                    foreach (['fr', 'en'] as $locale) {
                        $localizedColumn = $column . '_' . $locale;

                        if (Schema::hasColumn($table, $localizedColumn)) {
                            continue;
                        }

                        $createdColumn = match ($definition['type']) {
                            'string' => $blueprint->string($localizedColumn, $definition['length'] ?? 255),
                            'longText' => $blueprint->longText($localizedColumn),
                            default => $blueprint->text($localizedColumn),
                        };

                        $createdColumn->nullable();
                    }
                }
            });

            $this->backfillLocalizedColumns($table, array_keys($columns));
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns): void {
                $toDrop = [];

                foreach (array_keys($columns) as $column) {
                    foreach (['fr', 'en'] as $locale) {
                        $localizedColumn = $column . '_' . $locale;

                        if (Schema::hasColumn($table, $localizedColumn)) {
                            $toDrop[] = $localizedColumn;
                        }
                    }
                }

                if ($toDrop !== []) {
                    $blueprint->dropColumn($toDrop);
                }
            });
        }
    }

    private function backfillLocalizedColumns(string $table, array $columns): void
    {
        $select = ['id'];

        foreach ($columns as $column) {
            $select[] = $column;
            $select[] = $column . '_fr';
            $select[] = $column . '_en';
        }

        $rows = DB::table($table)->select($select)->get();

        foreach ($rows as $row) {
            $updates = [];

            foreach ($columns as $column) {
                $legacyValue = $row->{$column};

                if (($row->{$column . '_fr'} === null || $row->{$column . '_fr'} === '') && $legacyValue !== null && $legacyValue !== '') {
                    $updates[$column . '_fr'] = $legacyValue;
                }

                if (($row->{$column . '_en'} === null || $row->{$column . '_en'} === '') && $legacyValue !== null && $legacyValue !== '') {
                    $updates[$column . '_en'] = $legacyValue;
                }
            }

            if ($updates !== []) {
                DB::table($table)->where('id', $row->id)->update($updates);
            }
        }
    }
};
