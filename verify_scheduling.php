<?php
// Vérification de la structure du système de programmation

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "\n═════════════════════════════════════════════════════════════════════\n";
echo "✅ VÉRIFICATION COMPLÈTE DU SYSTÈME DE PROGRAMMATION\n";
echo "═════════════════════════════════════════════════════════════════════\n\n";

$tables = [
    'categories',
    'services',
    'versets',
    'spiritual_publications',
    'blog_articles',
    'formations',
    'opportunites',
    'newsletters'
];

$all_ok = true;

echo "Vérification des colonnes de programmation:\n";
echo "───────────────────────────────────────────\n";

foreach ($tables as $table) {
    $has_auto = \Illuminate\Support\Facades\Schema::hasColumn($table, 'auto_publish');
    $has_scheduled = \Illuminate\Support\Facades\Schema::hasColumn($table, 'scheduled_for');
    $has_published = \Illuminate\Support\Facades\Schema::hasColumn($table, 'published_at');

    $ok = $has_auto && $has_scheduled && $has_published;
    $status = $ok ? '✓' : '✗';

    echo $status . " " . str_pad($table, 30) . " (auto_publish, scheduled_for, published_at)\n";

    if (!$ok) $all_ok = false;
}

echo "\n═════════════════════════════════════════════════════════════════════\n";

if ($all_ok) {
    echo "✅ TOUS LES MODÈLES SUPPORTENT LA PROGRAMMATION\n";
    echo "\n✨ Statut: COMPLET ET OPÉRATIONNEL\n";
} else {
    echo "⚠️  Certains modèles manquent des colonnes de programmation\n";
}

echo "═════════════════════════════════════════════════════════════════════\n\n";
