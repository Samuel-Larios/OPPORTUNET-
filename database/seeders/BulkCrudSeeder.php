<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BulkCrudSeeder extends Seeder
{
    private const TARGET = 50;

    private const USER_TARGET = 60;

    private array $columnCache = [];

    private Carbon $now;

    private string $runToken;

    public function run(): void
    {
        $this->now = now();
        $this->runToken = $this->now->format('YmdHis');

        $this->seedUsers();
        $this->seedCategories();
        $this->seedServices();
        $this->seedFormations();
        $this->seedOpportunities();
        $this->seedArticles();
        $this->seedArticleComments();
        $this->seedVerses();
        $this->seedSpiritualPublications('pensee', self::TARGET);
        $this->seedSpiritualPublications('exhortation', self::TARGET);
        $this->seedSpiritualPublications('priere_jour', self::TARGET);
        $this->seedContacts();
        $this->seedCvDepots();
        $this->seedTrainingRegistrations();
        $this->seedApplications();
        $this->seedTestimonials();
        $this->seedPrayerWall();
        $this->seedNewsletterSubscribers();
        $this->seedNewsletters();
    }

    private function seedUsers(): void
    {
        $missing = max(0, self::USER_TARGET - DB::table('users')->count());

        if ($missing === 0) {
            return;
        }

        $roles = DB::table('roles')
            ->pluck('id', 'nom')
            ->all();

        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = DB::table('users')->count() + $i;
            $roleName = match ($sequence % 6) {
                0 => 'entreprise',
                1 => 'editeur',
                2 => 'admin',
                default => 'user',
            };
            $roleId = $roles[$roleName] ?? ($roles['user'] ?? null);
            $firstName = 'Utilisateur' . $sequence;
            $lastName = 'Seed' . $sequence;

            $rows[] = $this->row('users', [
                'role_id' => $roleId,
                'name' => $firstName . ' ' . $lastName,
                'prenom' => $firstName,
                'nom' => $lastName,
                'email' => "seed-user-{$this->runToken}-{$sequence}@example.test",
                'telephone' => '22901' . str_pad((string) (700000 + $sequence), 6, '0', STR_PAD_LEFT),
                'pays' => $sequence % 3 === 0 ? 'Benin' : ($sequence % 3 === 1 ? 'Togo' : 'Cote d Ivoire'),
                'ville' => $sequence % 2 === 0 ? 'Cotonou' : 'Porto-Novo',
                'bio' => "Profil de demonstration numero {$sequence} pour les tests du panel.",
                'genre' => $sequence % 2 === 0 ? 'homme' : 'femme',
                'date_naissance' => Carbon::parse('1990-01-01')->addDays($sequence)->toDateString(),
                'profession' => $roleName === 'entreprise' ? 'Responsable RH' : 'Consultant',
                'niveau_etude' => $sequence % 2 === 0 ? 'Master' : 'Licence',
                'whatsapp' => '22901' . str_pad((string) (710000 + $sequence), 6, '0', STR_PAD_LEFT),
                'email_verified_at' => $this->now->copy()->subDays($sequence % 20),
                'password' => Hash::make('password'),
                'remember_token' => Str::random(10),
                'actif' => true,
                'newsletter' => $sequence % 2 === 0,
                'langue' => $sequence % 4 === 0 ? 'en' : 'fr',
                'derniere_connexion' => $this->now->copy()->subDays($sequence % 15),
                'created_at' => $this->now->copy()->subDays($sequence % 90),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('users', $rows);
    }

    private function seedCategories(): void
    {
        $missing = max(0, self::TARGET - DB::table('categories')->count());

        if ($missing === 0) {
            return;
        }

        $types = ['offre', 'formation', 'blog', 'service'];
        $icons = ['briefcase', 'book-open', 'newspaper', 'sparkles', 'globe', 'users'];
        $colors = ['#2563EB', '#0F766E', '#D97706', '#9333EA', '#DC2626', '#14B8A6'];
        $baseCount = DB::table('categories')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $type = $types[$sequence % count($types)];
            $nameFr = 'Categorie ' . ucfirst($type) . ' ' . $sequence;
            $nameEn = ucfirst($type) . ' category ' . $sequence;

            $rows[] = $this->row('categories', [
                'type' => $type,
                'nom' => $nameFr,
                'nom_fr' => $nameFr,
                'nom_en' => $nameEn,
                'slug' => "seed-{$type}-category-{$this->runToken}-{$sequence}",
                'icone' => $icons[$sequence % count($icons)],
                'couleur' => $colors[$sequence % count($colors)],
                'description' => "Categorie de demonstration {$sequence} pour {$type}.",
                'description_fr' => "Categorie de demonstration {$sequence} pour {$type}.",
                'description_en' => "Demo category {$sequence} for {$type}.",
                'actif' => true,
                'ordre' => $sequence,
                'auto_publish' => false,
                'scheduled_for' => null,
                'published_at' => $this->now->copy()->subDays($sequence % 30),
                'created_at' => $this->now->copy()->subDays($sequence % 120),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('categories', $rows);
    }

    private function seedServices(): void
    {
        $missing = max(0, self::TARGET - DB::table('services')->count());

        if ($missing === 0) {
            return;
        }

        $categoryIds = DB::table('categories')->where('type', 'service')->pluck('id')->values()->all();
        $types = ['redaction_cv', 'coaching', 'orientation', 'accompagnement', 'autre'];
        $baseCount = DB::table('services')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $type = $types[$sequence % count($types)];
            $titleFr = 'Service premium ' . $sequence;
            $titleEn = 'Premium service ' . $sequence;
            $shortFr = "Service de demonstration {$sequence} pour accompagner les utilisateurs.";
            $shortEn = "Demo service {$sequence} designed to support platform users.";
            $longFr = "Ce service de demonstration {$sequence} couvre l accompagnement, la clarte strategique et la mise en action.";
            $longEn = "This demo service {$sequence} covers support, strategic clarity, and action planning.";

            $rows[] = $this->row('services', [
                'categorie_id' => $categoryIds[$sequence % max(1, count($categoryIds))] ?? null,
                'titre' => $titleFr,
                'titre_fr' => $titleFr,
                'titre_en' => $titleEn,
                'slug' => "seed-service-{$this->runToken}-{$sequence}",
                'description_courte' => $shortFr,
                'description_courte_fr' => $shortFr,
                'description_courte_en' => $shortEn,
                'description_longue' => $longFr,
                'description_longue_fr' => $longFr,
                'description_longue_en' => $longEn,
                'icone' => 'sparkles',
                'image' => null,
                'type' => $type,
                'prix' => 10000 + ($sequence * 500),
                'devise' => 'XOF',
                'duree' => ($sequence % 4 + 1) . ' jours',
                'duree_fr' => ($sequence % 4 + 1) . ' jours',
                'duree_en' => ($sequence % 4 + 1) . ' days',
                'whatsapp_message' => "Bonjour, je souhaite en savoir plus sur le service {$titleFr}.",
                'whatsapp_message_fr' => "Bonjour, je souhaite en savoir plus sur le service {$titleFr}.",
                'whatsapp_message_en' => "Hello, I would like to know more about {$titleEn}.",
                'actif' => true,
                'en_vedette' => $sequence % 5 === 0,
                'ordre' => $sequence,
                'auto_publish' => false,
                'scheduled_for' => null,
                'published_at' => $this->now->copy()->subDays($sequence % 45),
                'created_at' => $this->now->copy()->subDays($sequence % 120),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('services', $rows);
    }

    private function seedFormations(): void
    {
        $missing = max(0, self::TARGET - DB::table('formations')->count());

        if ($missing === 0) {
            return;
        }

        $categoryIds = DB::table('categories')->where('type', 'formation')->pluck('id')->values()->all();
        $trainerIds = DB::table('users')
            ->whereIn('role_id', DB::table('roles')->whereIn('nom', ['admin', 'editeur', 'coach'])->pluck('id'))
            ->pluck('id')
            ->values()
            ->all();
        $modes = ['presentiel', 'en_ligne', 'hybride'];
        $statuses = ['ouverte', 'complete', 'terminee', 'brouillon'];
        $baseCount = DB::table('formations')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $titleFr = 'Formation intensive ' . $sequence;
            $titleEn = 'Intensive training ' . $sequence;
            $status = $statuses[$sequence % count($statuses)];
            $isFree = $sequence % 4 === 0;
            $startDate = $this->now->copy()->addDays($sequence % 60 + 3);

            $rows[] = $this->row('formations', [
                'categorie_id' => $categoryIds[$sequence % max(1, count($categoryIds))] ?? null,
                'formateur_id' => $trainerIds[$sequence % max(1, count($trainerIds))] ?? null,
                'titre' => $titleFr,
                'titre_fr' => $titleFr,
                'titre_en' => $titleEn,
                'slug' => "seed-training-{$this->runToken}-{$sequence}",
                'description_courte' => "Presentation courte de la formation {$sequence}.",
                'description_courte_fr' => "Presentation courte de la formation {$sequence}.",
                'description_courte_en' => "Short overview for training {$sequence}.",
                'description_longue' => "Contenu detaille de la formation {$sequence} avec ateliers pratiques et suivi.",
                'description_longue_fr' => "Contenu detaille de la formation {$sequence} avec ateliers pratiques et suivi.",
                'description_longue_en' => "Detailed content for training {$sequence} with practical workshops and follow-up.",
                'image_couverture' => null,
                'mode' => $modes[$sequence % count($modes)],
                'lieu' => $sequence % 2 === 0 ? 'Cotonou' : 'En ligne',
                'lieu_fr' => $sequence % 2 === 0 ? 'Cotonou' : 'En ligne',
                'lieu_en' => $sequence % 2 === 0 ? 'Cotonou' : 'Online',
                'lien_en_ligne' => 'https://example.test/trainings/' . $sequence,
                'prix' => $isFree ? 0 : 25000 + ($sequence * 350),
                'devise' => 'XOF',
                'gratuit' => $isFree,
                'duree_heures' => 6 + ($sequence % 12),
                'nb_seances' => 2 + ($sequence % 5),
                'date_debut' => $startDate->toDateString(),
                'date_fin' => $startDate->copy()->addDays(3 + ($sequence % 6))->toDateString(),
                'heure_debut' => '09:00:00',
                'fuseau_horaire' => 'Africa/Cotonou',
                'places_max' => 20 + ($sequence % 25),
                'places_restantes' => 5 + ($sequence % 10),
                'niveau' => $sequence % 3 === 0 ? 'Intermediaire' : 'Debutant',
                'niveau_fr' => $sequence % 3 === 0 ? 'Intermediaire' : 'Debutant',
                'niveau_en' => $sequence % 3 === 0 ? 'Intermediate' : 'Beginner',
                'prerequis' => "Prerequis de base pour la formation {$sequence}.",
                'prerequis_fr' => "Prerequis de base pour la formation {$sequence}.",
                'prerequis_en' => "Basic prerequisites for training {$sequence}.",
                'objectifs' => "Objectifs pedagogiques de la formation {$sequence}.",
                'objectifs_fr' => "Objectifs pedagogiques de la formation {$sequence}.",
                'objectifs_en' => "Learning goals for training {$sequence}.",
                'programme' => "Programme synthese de la formation {$sequence}.",
                'programme_fr' => "Programme synthese de la formation {$sequence}.",
                'programme_en' => "Program summary for training {$sequence}.",
                'certificat' => $sequence % 2 === 0 ? 'Certificat numerique' : 'Attestation',
                'certificat_fr' => $sequence % 2 === 0 ? 'Certificat numerique' : 'Attestation',
                'certificat_en' => $sequence % 2 === 0 ? 'Digital certificate' : 'Certificate of attendance',
                'statut' => $status,
                'inscriptions_ouvertes' => $status === 'ouverte',
                'en_vedette' => $sequence % 6 === 0,
                'vues' => 40 + $sequence,
                'whatsapp_message' => "Bonjour, je souhaite m inscrire a {$titleFr}.",
                'whatsapp_message_fr' => "Bonjour, je souhaite m inscrire a {$titleFr}.",
                'whatsapp_message_en' => "Hello, I would like to register for {$titleEn}.",
                'auto_publish' => false,
                'scheduled_for' => null,
                'published_at' => $status === 'brouillon' ? null : $this->now->copy()->subDays($sequence % 50),
                'scheduled_status' => null,
                'created_at' => $this->now->copy()->subDays($sequence % 150),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('formations', $rows);
    }

    private function seedOpportunities(): void
    {
        $missing = max(0, self::TARGET - DB::table('opportunites')->count());

        if ($missing === 0) {
            return;
        }

        $categoryIds = DB::table('categories')->where('type', 'offre')->pluck('id')->values()->all();
        $roleIds = DB::table('roles')->pluck('id', 'nom')->all();
        $companyRoleId = $roleIds['entreprise'] ?? null;
        $companyUserIds = DB::table('users')
            ->when($companyRoleId, fn ($query) => $query->where('role_id', $companyRoleId))
            ->pluck('id')
            ->values()
            ->all();
        $adminIds = DB::table('users')
            ->whereIn('role_id', array_filter([$roleIds['super_admin'] ?? null, $roleIds['admin'] ?? null, $roleIds['editeur'] ?? null]))
            ->pluck('id')
            ->values()
            ->all();
        $types = ['emploi', 'stage', 'bourse', 'appel_offre', 'volontariat', 'formation_externe', 'autre'];
        $contracts = ['cdi', 'cdd', 'stage', 'freelance', 'temps_partiel', 'bénévolat', 'non_applicable'];
        $statuses = ['publie', 'en_attente_validation', 'brouillon', 'archive'];
        $baseCount = DB::table('opportunites')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $status = $statuses[$sequence % count($statuses)];
            $titleFr = 'Opportunite pro ' . $sequence;
            $titleEn = 'Career opportunity ' . $sequence;
            $publishedAt = $status === 'publie' ? $this->now->copy()->subDays($sequence % 35) : null;
            $validatorId = $status === 'publie' ? ($adminIds[$sequence % max(1, count($adminIds))] ?? null) : null;

            $rows[] = $this->row('opportunites', [
                'categorie_id' => $categoryIds[$sequence % max(1, count($categoryIds))] ?? null,
                'user_id' => $companyUserIds[$sequence % max(1, count($companyUserIds))] ?? null,
                'titre' => $titleFr,
                'titre_fr' => $titleFr,
                'titre_en' => $titleEn,
                'slug' => "seed-opportunity-{$this->runToken}-{$sequence}",
                'organisation' => 'Organisation Seed ' . $sequence,
                'logo_organisation' => null,
                'type' => $types[$sequence % count($types)],
                'contrat' => $contracts[$sequence % count($contracts)],
                'lieu' => $sequence % 2 === 0 ? 'Cotonou' : 'Remote',
                'pays' => $sequence % 3 === 0 ? 'Benin' : ($sequence % 3 === 1 ? 'Senegal' : 'Ghana'),
                'teletravail' => $sequence % 2 === 0,
                'description' => "Description complete de l opportunite {$sequence}.",
                'description_fr' => "Description complete de l opportunite {$sequence}.",
                'description_en' => "Detailed description for opportunity {$sequence}.",
                'profil_recherche' => "Profil recherche pour l opportunite {$sequence}.",
                'profil_recherche_fr' => "Profil recherche pour l opportunite {$sequence}.",
                'profil_recherche_en' => "Candidate profile for opportunity {$sequence}.",
                'avantages' => "Avantages proposes pour l opportunite {$sequence}.",
                'avantages_fr' => "Avantages proposes pour l opportunite {$sequence}.",
                'avantages_en' => "Benefits provided for opportunity {$sequence}.",
                'lien_candidature' => 'https://example.test/opportunities/' . $sequence,
                'email_candidature' => "recrutement{$sequence}@example.test",
                'salaire_min' => 150000 + ($sequence * 1000),
                'salaire_max' => 220000 + ($sequence * 1200),
                'devise_salaire' => 'XOF',
                'date_expiration' => $this->now->copy()->addDays(15 + ($sequence % 60))->toDateString(),
                'date_publication' => $publishedAt?->toDateString(),
                'statut' => $status,
                'valide_par' => $validatorId,
                'valide_le' => $publishedAt,
                'notes_validation_admin' => $validatorId ? "Validation effectuee pour l opportunite {$sequence}." : null,
                'en_vedette' => $sequence % 5 === 0,
                'urgent' => $sequence % 7 === 0,
                'vues' => 100 + $sequence,
                'candidatures' => $sequence % 12,
                'source' => 'Seeding automatique',
                'auto_publish' => false,
                'scheduled_for' => null,
                'published_at' => $publishedAt,
                'scheduled_status' => null,
                'created_at' => $this->now->copy()->subDays($sequence % 180),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('opportunites', $rows);
    }

    private function seedArticles(): void
    {
        $missing = max(0, self::TARGET - DB::table('blog_articles')->count());

        if ($missing === 0) {
            return;
        }

        $categoryIds = DB::table('categories')->where('type', 'blog')->pluck('id')->values()->all();
        $authorIds = DB::table('users')->pluck('id')->values()->all();
        $statuses = ['publie', 'brouillon', 'archive'];
        $baseCount = DB::table('blog_articles')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $status = $statuses[$sequence % count($statuses)];
            $titleFr = 'Article inspiration ' . $sequence;
            $titleEn = 'Inspiration article ' . $sequence;

            $rows[] = $this->row('blog_articles', [
                'user_id' => $authorIds[$sequence % max(1, count($authorIds))] ?? null,
                'categorie_id' => $categoryIds[$sequence % max(1, count($categoryIds))] ?? null,
                'titre' => $titleFr,
                'titre_fr' => $titleFr,
                'titre_en' => $titleEn,
                'slug' => "seed-article-{$this->runToken}-{$sequence}",
                'extrait' => "Extrait de demonstration pour l article {$sequence}.",
                'extrait_fr' => "Extrait de demonstration pour l article {$sequence}.",
                'extrait_en' => "Demo excerpt for article {$sequence}.",
                'contenu' => "Contenu complet de l article {$sequence} avec plusieurs idees, conseils et applications pratiques.",
                'contenu_fr' => "Contenu complet de l article {$sequence} avec plusieurs idees, conseils et applications pratiques.",
                'contenu_en' => "Full content for article {$sequence} with ideas, advice, and practical applications.",
                'image_couverture' => null,
                'image_alt' => "Visuel article {$sequence}",
                'image_alt_fr' => "Visuel article {$sequence}",
                'image_alt_en' => "Article image {$sequence}",
                'meta_titre' => $titleFr,
                'meta_titre_fr' => $titleFr,
                'meta_titre_en' => $titleEn,
                'meta_description' => "Meta description pour l article {$sequence}.",
                'meta_description_fr' => "Meta description pour l article {$sequence}.",
                'meta_description_en' => "Meta description for article {$sequence}.",
                'tags' => json_encode(['seed', 'article', 'demo-' . $sequence]),
                'statut' => $status,
                'publie_le' => $status === 'publie' ? $this->now->copy()->subDays($sequence % 70) : null,
                'en_vedette' => $sequence % 6 === 0,
                'commentaires_actifs' => true,
                'vues' => 50 + $sequence,
                'partages' => $sequence % 15,
                'temps_lecture' => (4 + ($sequence % 8)) . ' min',
                'auto_publish' => false,
                'scheduled_for' => null,
                'published_at' => $status === 'publie' ? $this->now->copy()->subDays($sequence % 70) : null,
                'scheduled_status' => null,
                'created_at' => $this->now->copy()->subDays($sequence % 200),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('blog_articles', $rows);
    }

    private function seedArticleComments(): void
    {
        $missing = max(0, self::TARGET - DB::table('blog_commentaires')->count());

        if ($missing === 0) {
            return;
        }

        $articleIds = DB::table('blog_articles')->pluck('id')->values()->all();
        $userIds = DB::table('users')->pluck('id')->values()->all();
        $statuses = ['en_attente', 'approuve', 'spam', 'rejete'];
        $baseCount = DB::table('blog_commentaires')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;

            $rows[] = $this->row('blog_commentaires', [
                'article_id' => $articleIds[$sequence % max(1, count($articleIds))] ?? null,
                'user_id' => $sequence % 2 === 0 ? ($userIds[$sequence % max(1, count($userIds))] ?? null) : null,
                'parent_id' => null,
                'auteur_nom' => 'Commentateur ' . $sequence,
                'auteur_email' => "comment-{$this->runToken}-{$sequence}@example.test",
                'contenu' => "Commentaire de demonstration numero {$sequence}.",
                'ip_address' => '127.0.0.' . (($sequence % 200) + 1),
                'statut' => $statuses[$sequence % count($statuses)],
                'created_at' => $this->now->copy()->subDays($sequence % 45),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('blog_commentaires', $rows);
    }

    private function seedVerses(): void
    {
        $missing = max(0, self::TARGET - DB::table('versets')->count());

        if ($missing === 0) {
            return;
        }

        $baseCount = DB::table('versets')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $referenceFr = 'Psaume ' . (20 + $sequence) . ':' . (($sequence % 15) + 1);
            $referenceEn = 'Psalm ' . (20 + $sequence) . ':' . (($sequence % 15) + 1);

            $rows[] = $this->row('versets', [
                'reference' => $referenceFr,
                'reference_fr' => $referenceFr,
                'reference_en' => $referenceEn,
                'texte' => "Texte biblique de demonstration numero {$sequence} pour encourager la foi.",
                'texte_fr' => "Texte biblique de demonstration numero {$sequence} pour encourager la foi.",
                'texte_en' => "Demo Bible verse number {$sequence} to strengthen faith.",
                'version' => $sequence % 2 === 0 ? 'LSG' : 'BDS',
                'version_fr' => $sequence % 2 === 0 ? 'LSG' : 'BDS',
                'version_en' => $sequence % 2 === 0 ? 'KJV' : 'NIV',
                'actif' => true,
                'afficher_accueil' => $sequence <= 6,
                'ordre' => $sequence,
                'auto_publish' => false,
                'scheduled_for' => null,
                'published_at' => $this->now->copy()->subDays($sequence % 40),
                'created_at' => $this->now->copy()->subDays($sequence % 100),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('versets', $rows);
    }

    private function seedSpiritualPublications(string $type, int $target): void
    {
        $existing = DB::table('spiritual_publications')->where('type', $type)->count();
        $missing = max(0, $target - $existing);

        if ($missing === 0) {
            return;
        }

        $labels = [
            'pensee' => ['fr' => 'Pensee du jour', 'en' => 'Thought of the day'],
            'exhortation' => ['fr' => 'Exhortation', 'en' => 'Exhortation'],
            'priere_jour' => ['fr' => 'Priere du jour', 'en' => 'Prayer of the day'],
        ];
        $baseCount = $existing;
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $titleFr = $labels[$type]['fr'] . ' ' . $sequence;
            $titleEn = $labels[$type]['en'] . ' ' . $sequence;

            $rows[] = $this->row('spiritual_publications', [
                'type' => $type,
                'titre' => $titleFr,
                'titre_fr' => $titleFr,
                'titre_en' => $titleEn,
                'slug' => "seed-{$type}-{$this->runToken}-{$sequence}",
                'extrait' => "Extrait de demonstration pour {$titleFr}.",
                'extrait_fr' => "Extrait de demonstration pour {$titleFr}.",
                'extrait_en' => "Demo excerpt for {$titleEn}.",
                'contenu' => "Contenu de demonstration {$sequence} pour le type {$type}, avec un message clair et encourageant.",
                'contenu_fr' => "Contenu de demonstration {$sequence} pour le type {$type}, avec un message clair et encourageant.",
                'contenu_en' => "Demo content {$sequence} for {$type} with a clear and encouraging message.",
                'reference' => $type === 'priere_jour' ? null : 'Reference ' . $sequence,
                'reference_fr' => $type === 'priere_jour' ? null : 'Reference ' . $sequence,
                'reference_en' => $type === 'priere_jour' ? null : 'Reference ' . $sequence,
                'auteur' => $sequence % 3 === 0 ? 'Equipe Opportunet' : 'Auteur Seed',
                'auteur_fr' => $sequence % 3 === 0 ? 'Equipe Opportunet' : 'Auteur Seed',
                'auteur_en' => $sequence % 3 === 0 ? 'Opportunet Team' : 'Seed Author',
                'actif' => true,
                'afficher_accueil' => $sequence <= 6,
                'auto_publish' => false,
                'scheduled_for' => null,
                'published_at' => $this->now->copy()->subDays($sequence % 35),
                'ordre' => $sequence,
                'created_at' => $this->now->copy()->subDays($sequence % 100),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('spiritual_publications', $rows);
    }

    private function seedContacts(): void
    {
        $missing = max(0, self::TARGET - DB::table('contacts')->count());

        if ($missing === 0) {
            return;
        }

        $userIds = DB::table('users')->pluck('id')->values()->all();
        $adminIds = $this->staffUserIds();
        $subjects = ['information', 'service', 'formation', 'offre', 'partenariat', 'technique', 'autre'];
        $statuses = ['non_lu', 'lu', 'en_cours', 'repondu', 'archive'];
        $baseCount = DB::table('contacts')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $status = $statuses[$sequence % count($statuses)];
            $handledBy = in_array($status, ['repondu', 'archive'], true)
                ? ($adminIds[$sequence % max(1, count($adminIds))] ?? null)
                : null;

            $rows[] = $this->row('contacts', [
                'user_id' => $userIds[$sequence % max(1, count($userIds))] ?? null,
                'prenom' => 'Contact' . $sequence,
                'nom' => 'Seed' . $sequence,
                'email' => "contact-{$this->runToken}-{$sequence}@example.test",
                'telephone' => '22902' . str_pad((string) (600000 + $sequence), 6, '0', STR_PAD_LEFT),
                'whatsapp' => '22902' . str_pad((string) (610000 + $sequence), 6, '0', STR_PAD_LEFT),
                'pays' => $sequence % 2 === 0 ? 'Benin' : 'Niger',
                'sujet' => $subjects[$sequence % count($subjects)],
                'sujet_personnalise' => $sequence % 7 === 0 ? 'Sujet personnalise ' . $sequence : null,
                'message' => "Message de contact de demonstration numero {$sequence}.",
                'priorite' => $sequence % 5 === 0 ? 'urgente' : 'normale',
                'statut' => $status,
                'reponse_admin' => $handledBy ? "Reponse admin pour le contact {$sequence}." : null,
                'notes_admin' => "Note interne pour le contact {$sequence}.",
                'traite_par' => $handledBy,
                'repondu_le' => $handledBy ? $this->now->copy()->subDays($sequence % 12) : null,
                'rappel_le' => $sequence % 6 === 0 ? $this->now->copy()->addDays(2) : null,
                'rappel_note' => $sequence % 6 === 0 ? 'Relance prevue par email.' : null,
                'ip_address' => '127.0.1.' . (($sequence % 200) + 1),
                'created_at' => $this->now->copy()->subDays($sequence % 60),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('contacts', $rows);
    }

    private function seedCvDepots(): void
    {
        $missing = max(0, self::TARGET - DB::table('cv_depots')->count());

        if ($missing === 0) {
            return;
        }

        $userIds = DB::table('users')->pluck('id')->values()->all();
        $adminIds = $this->staffUserIds();
        $statuses = ['nouveau', 'en_traitement', 'traite', 'archive'];
        $contractTypes = ['cdi', 'cdd', 'stage', 'freelance', 'tous'];
        $baseCount = DB::table('cv_depots')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $status = $statuses[$sequence % count($statuses)];
            $handledBy = $status === 'traite' || $status === 'archive'
                ? ($adminIds[$sequence % max(1, count($adminIds))] ?? null)
                : null;

            $rows[] = $this->row('cv_depots', [
                'user_id' => $userIds[$sequence % max(1, count($userIds))] ?? null,
                'prenom' => 'Candidat' . $sequence,
                'nom' => 'Cv' . $sequence,
                'email' => "cv-{$this->runToken}-{$sequence}@example.test",
                'telephone' => '22903' . str_pad((string) (500000 + $sequence), 6, '0', STR_PAD_LEFT),
                'whatsapp' => '22903' . str_pad((string) (510000 + $sequence), 6, '0', STR_PAD_LEFT),
                'pays' => $sequence % 2 === 0 ? 'Benin' : 'Burkina Faso',
                'ville' => $sequence % 2 === 0 ? 'Abomey-Calavi' : 'Parakou',
                'date_naissance' => Carbon::parse('1992-01-01')->addDays($sequence)->toDateString(),
                'genre' => $sequence % 2 === 0 ? 'homme' : 'femme',
                'titre_poste' => 'Charge de projet ' . $sequence,
                'niveau_etude' => $sequence % 3 === 0 ? 'Master' : 'Licence',
                'domaine_etude' => 'Gestion de projet',
                'competences' => 'Coordination, communication, suivi evaluation',
                'langues' => 'Francais, Anglais',
                'annees_experience' => 1 + ($sequence % 8),
                'objectif_professionnel' => "Objectif professionnel du candidat {$sequence}.",
                'secteurs_interet' => 'ONG, education, tech',
                'type_contrat_recherche' => $contractTypes[$sequence % count($contractTypes)],
                'teletravail_souhaite' => $sequence % 2 === 0,
                'cv_fichier' => "seed/cv/cv-{$sequence}.pdf",
                'linkedin_url' => "https://linkedin.example.test/in/cv-{$sequence}",
                'portfolio_url' => "https://portfolio.example.test/cv-{$sequence}",
                'message' => "Message complementaire pour le depot CV {$sequence}.",
                'demande_redaction_cv' => $sequence % 2 === 0,
                'demande_coaching' => $sequence % 3 === 0,
                'demande_orientation' => $sequence % 4 === 0,
                'statut' => $status,
                'notes_admin' => "Notes admin pour le depot {$sequence}.",
                'traite_par' => $handledBy,
                'traite_le' => $handledBy ? $this->now->copy()->subDays($sequence % 10) : null,
                'created_at' => $this->now->copy()->subDays($sequence % 80),
                'updated_at' => $this->now,
                'deleted_at' => null,
            ]);
        }

        $this->insertRows('cv_depots', $rows);
    }

    private function seedTrainingRegistrations(): void
    {
        $missing = max(0, self::TARGET - DB::table('inscriptions_formations')->count());

        if ($missing === 0) {
            return;
        }

        $formationIds = DB::table('formations')->pluck('id')->values()->all();
        $userIds = DB::table('users')->pluck('id')->values()->all();
        $adminIds = $this->staffUserIds();
        $paymentModes = ['mobile_money', 'virement', 'especes', 'gratuit', 'en_attente'];
        $paymentStatuses = ['non_paye', 'en_attente', 'paye', 'rembourse'];
        $statuses = ['en_attente', 'confirme', 'annule', 'liste_attente'];
        $baseCount = DB::table('inscriptions_formations')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $status = $statuses[$sequence % count($statuses)];
            $handledBy = $status !== 'en_attente'
                ? ($adminIds[$sequence % max(1, count($adminIds))] ?? null)
                : null;

            $rows[] = $this->row('inscriptions_formations', [
                'formation_id' => $formationIds[$sequence % max(1, count($formationIds))] ?? null,
                'user_id' => $userIds[$sequence % max(1, count($userIds))] ?? null,
                'prenom' => 'Inscrit' . $sequence,
                'nom' => 'Formation' . $sequence,
                'email' => "registration-{$this->runToken}-{$sequence}@example.test",
                'telephone' => '22904' . str_pad((string) (400000 + $sequence), 6, '0', STR_PAD_LEFT),
                'whatsapp' => '22904' . str_pad((string) (410000 + $sequence), 6, '0', STR_PAD_LEFT),
                'pays' => $sequence % 2 === 0 ? 'Benin' : 'Mali',
                'profession' => 'Jeune professionnel',
                'niveau_etude' => $sequence % 2 === 0 ? 'Licence' : 'Master',
                'motivation' => "Motivation de l inscrit {$sequence}.",
                'mode_paiement' => $paymentModes[$sequence % count($paymentModes)],
                'reference_paiement' => $sequence % 2 === 0 ? 'PAY-' . $sequence : null,
                'montant_paye' => $sequence % 2 === 0 ? 15000 + ($sequence * 100) : null,
                'statut_paiement' => $paymentStatuses[$sequence % count($paymentStatuses)],
                'statut' => $status,
                'certificat_delivre' => $status === 'confirme' && $sequence % 3 === 0,
                'certificat_fichier' => $status === 'confirme' && $sequence % 3 === 0 ? "seed/certificates/cert-{$sequence}.pdf" : null,
                'confirme_le' => $status === 'confirme' ? $this->now->copy()->subDays($sequence % 7) : null,
                'notes_admin' => "Notes admin inscription {$sequence}.",
                'est_suspendue' => $sequence % 9 === 0,
                'suspendue_le' => $sequence % 9 === 0 ? $this->now->copy()->subDays(1) : null,
                'motif_suspension' => $sequence % 9 === 0 ? 'Documents en attente.' : null,
                'traite_par' => $handledBy,
                'traite_le' => $handledBy ? $this->now->copy()->subDays($sequence % 5) : null,
                'created_at' => $this->now->copy()->subDays($sequence % 90),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('inscriptions_formations', $rows);
    }

    private function seedApplications(): void
    {
        $missing = max(0, self::TARGET - DB::table('candidatures_offres')->count());

        if ($missing === 0) {
            return;
        }

        $candidateUsers = DB::table('users')
            ->whereIn('role_id', DB::table('roles')->whereIn('nom', ['user', 'editeur', 'admin'])->pluck('id'))
            ->pluck('id')
            ->values()
            ->all();
        $opportunityIds = DB::table('opportunites')->pluck('id')->values()->all();
        $adminIds = $this->staffUserIds();
        $statuses = ['en_attente', 'en_revue', 'retenue', 'proposee_entreprise', 'validee_entreprise', 'rejetee', 'informations_complementaires'];
        $baseCount = DB::table('candidatures_offres')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $status = $statuses[$sequence % count($statuses)];
            $handledBy = $status !== 'en_attente' ? ($adminIds[$sequence % max(1, count($adminIds))] ?? null) : null;

            $rows[] = $this->row('candidatures_offres', [
                'user_id' => $candidateUsers[$sequence % max(1, count($candidateUsers))] ?? null,
                'opportunite_id' => $opportunityIds[$sequence % max(1, count($opportunityIds))] ?? null,
                'prenom' => 'Applicant' . $sequence,
                'nom' => 'Seed' . $sequence,
                'email' => "application-{$this->runToken}-{$sequence}@example.test",
                'telephone' => '22905' . str_pad((string) (300000 + $sequence), 6, '0', STR_PAD_LEFT),
                'whatsapp' => '22905' . str_pad((string) (310000 + $sequence), 6, '0', STR_PAD_LEFT),
                'pays' => $sequence % 2 === 0 ? 'Benin' : 'Cameroon',
                'cv_fichier' => "seed/applications/cv-{$sequence}.pdf",
                'lettre_motivation' => "seed/applications/cover-letter-{$sequence}.pdf",
                'diplome_fichiers' => json_encode(["seed/applications/diploma-{$sequence}.pdf"]),
                'attestation_fichiers' => json_encode(["seed/applications/certificate-{$sequence}.pdf"]),
                'message' => "Message de candidature {$sequence}.",
                'statut' => $status,
                'notes_admin' => $handledBy ? "Evaluation admin pour la candidature {$sequence}." : null,
                'traite_par' => $handledBy,
                'traite_le' => $handledBy ? $this->now->copy()->subDays($sequence % 5) : null,
                'email_traitement_envoye_le' => $handledBy ? $this->now->copy()->subDays($sequence % 4) : null,
                'proposee_entreprise_le' => in_array($status, ['proposee_entreprise', 'validee_entreprise'], true) ? $this->now->copy()->subDays(2) : null,
                'validee_par_entreprise' => $status === 'validee_entreprise' ? ($adminIds[$sequence % max(1, count($adminIds))] ?? null) : null,
                'validee_entreprise_le' => $status === 'validee_entreprise' ? $this->now->copy()->subDay() : null,
                'note_entreprise' => $status === 'validee_entreprise' ? 'Profil valide par l entreprise.' : null,
                'created_at' => $this->now->copy()->subDays($sequence % 70),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('candidatures_offres', $rows);
    }

    private function seedTestimonials(): void
    {
        $missing = max(0, self::TARGET - DB::table('temoignages')->count());

        if ($missing === 0) {
            return;
        }

        $userIds = DB::table('users')->pluck('id')->values()->all();
        $types = ['emploi_trouve', 'formation_suivie', 'service_cv', 'coaching', 'general'];
        $statuses = ['en_attente', 'approuve', 'rejete'];
        $baseCount = DB::table('temoignages')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;

            $rows[] = $this->row('temoignages', [
                'user_id' => $userIds[$sequence % max(1, count($userIds))] ?? null,
                'prenom' => 'Temoin' . $sequence,
                'nom' => 'Seed' . $sequence,
                'email' => "testimonial-{$this->runToken}-{$sequence}@example.test",
                'photo' => null,
                'pays' => $sequence % 2 === 0 ? 'Benin' : 'Togo',
                'profession' => 'Charge de programme',
                'contenu' => "Temoignage de demonstration numero {$sequence} sur l impact de la plateforme.",
                'contenu_fr' => "Temoignage de demonstration numero {$sequence} sur l impact de la plateforme.",
                'contenu_en' => "Demo testimony number {$sequence} about the platform impact.",
                'type' => $types[$sequence % count($types)],
                'note' => 3 + ($sequence % 3),
                'statut' => $statuses[$sequence % count($statuses)],
                'en_vedette' => $sequence % 8 === 0,
                'ordre' => $sequence,
                'created_at' => $this->now->copy()->subDays($sequence % 75),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('temoignages', $rows);
    }

    private function seedPrayerWall(): void
    {
        $missing = max(0, self::TARGET - DB::table('mur_de_prieres')->count());

        if ($missing === 0) {
            return;
        }

        $userIds = DB::table('users')->pluck('id')->values()->all();
        $types = ['priere', 'temoignage_reponse', 'encouragement', 'verset_partage'];
        $statuses = ['en_attente', 'approuve', 'rejete'];
        $baseCount = DB::table('mur_de_prieres')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;

            $rows[] = $this->row('mur_de_prieres', [
                'user_id' => $userIds[$sequence % max(1, count($userIds))] ?? null,
                'prenom' => 'Priere' . $sequence,
                'pays' => $sequence % 2 === 0 ? 'Benin' : 'Gabon',
                'email' => "prayer-{$this->runToken}-{$sequence}@example.test",
                'sujet' => "Sujet de priere ou encouragement numero {$sequence}.",
                'type' => $types[$sequence % count($types)],
                'anonyme' => $sequence % 4 === 0,
                'priants' => $sequence % 30,
                'statut' => $statuses[$sequence % count($statuses)],
                'created_at' => $this->now->copy()->subDays($sequence % 65),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('mur_de_prieres', $rows);
    }

    private function seedNewsletterSubscribers(): void
    {
        $missing = max(0, self::TARGET - DB::table('newsletter_subscribers')->count());

        if ($missing === 0) {
            return;
        }

        $baseCount = DB::table('newsletter_subscribers')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;

            $rows[] = $this->row('newsletter_subscribers', [
                'prenom' => 'Abonne' . $sequence,
                'email' => "newsletter-{$this->runToken}-{$sequence}@example.test",
                'langue' => $sequence % 4 === 0 ? 'en' : 'fr',
                'source' => $sequence % 2 === 0 ? 'website' : 'admin_import',
                'is_active' => $sequence % 8 !== 0,
                'subscribed_at' => $this->now->copy()->subDays($sequence % 50),
                'created_at' => $this->now->copy()->subDays($sequence % 50),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('newsletter_subscribers', $rows);
    }

    private function seedNewsletters(): void
    {
        $missing = max(0, self::TARGET - DB::table('newsletters')->count());

        if ($missing === 0) {
            return;
        }

        $articles = DB::table('blog_articles')->select('id', 'titre', 'slug')->limit(20)->get()->values();
        $offers = DB::table('opportunites')->select('id', 'titre', 'slug')->limit(20)->get()->values();
        $verses = DB::table('versets')->select('id', 'reference')->limit(20)->get()->values();
        $spiritual = DB::table('spiritual_publications')->select('id', 'titre', 'slug', 'type')->limit(30)->get()->values();
        $sources = collect()
            ->merge($articles->map(fn ($item) => [
                'type' => 'App\\Models\\BlogArticle',
                'id' => $item->id,
                'title' => $item->titre,
                'url' => url('/articles/' . $item->slug),
                'label' => 'Article',
            ]))
            ->merge($offers->map(fn ($item) => [
                'type' => 'App\\Models\\Opportunite',
                'id' => $item->id,
                'title' => $item->titre,
                'url' => url('/offres-opportunites/' . $item->slug),
                'label' => 'Opportunite',
            ]))
            ->merge($verses->map(fn ($item) => [
                'type' => 'App\\Models\\Verset',
                'id' => $item->id,
                'title' => $item->reference,
                'url' => url('/versets-bibliques/' . $item->id),
                'label' => 'Verset',
            ]))
            ->merge($spiritual->map(fn ($item) => [
                'type' => 'App\\Models\\SpiritualPublication',
                'id' => $item->id,
                'title' => $item->titre,
                'url' => match ($item->type) {
                    'pensee' => url('/pensees-du-jour/' . $item->slug),
                    'exhortation' => url('/exhortations/' . $item->slug),
                    default => url('/prieres-du-jour?item=' . $item->slug),
                },
                'label' => match ($item->type) {
                    'pensee' => 'Pensee',
                    'exhortation' => 'Exhortation',
                    default => 'Priere du jour',
                },
            ]))
            ->values();

        $baseCount = DB::table('newsletters')->count();
        $rows = [];

        for ($i = 1; $i <= $missing; $i++) {
            $sequence = $baseCount + $i;
            $source = $sources[$sequence % max(1, $sources->count())] ?? null;
            $subject = $source
                ? "Notification publication {$source['label']} {$sequence}"
                : "Newsletter manuelle {$sequence}";

            $rows[] = $this->row('newsletters', [
                'subject' => $subject,
                'audience' => 'platform_users_and_subscribers',
                'content_type' => $source['type'] ?? null,
                'content_id' => $source['id'] ?? null,
                'content_title' => $source['title'] ?? "Contenu manuel {$sequence}",
                'content_url' => $source['url'] ?? url('/'),
                'status' => $sequence % 6 === 0 ? 'scheduled' : ($sequence % 5 === 0 ? 'draft' : 'sent'),
                'recipients_count' => 15 + ($sequence % 120),
                'sent_at' => $sequence % 6 === 0 ? null : $this->now->copy()->subDays($sequence % 30),
                'meta' => json_encode([
                    'label' => $source['label'] ?? 'Newsletter',
                    'summary' => "Resume de demonstration pour la newsletter {$sequence}.",
                ]),
                'auto_publish' => $sequence % 6 === 0,
                'scheduled_for' => $sequence % 6 === 0 ? $this->now->copy()->addDays(($sequence % 10) + 1) : null,
                'published_at' => $sequence % 6 === 0 ? null : $this->now->copy()->subDays($sequence % 30),
                'created_at' => $this->now->copy()->subDays($sequence % 60),
                'updated_at' => $this->now,
            ]);
        }

        $this->insertRows('newsletters', $rows);
    }

    private function staffUserIds(): array
    {
        $roleIds = DB::table('roles')
            ->whereIn('nom', ['super_admin', 'admin', 'editeur', 'coach'])
            ->pluck('id')
            ->all();

        return DB::table('users')
            ->whereIn('role_id', $roleIds)
            ->pluck('id')
            ->values()
            ->all();
    }

    private function row(string $table, array $data): array
    {
        $columns = array_flip($this->columns($table));

        return array_intersect_key($data, $columns);
    }

    private function columns(string $table): array
    {
        if (! array_key_exists($table, $this->columnCache)) {
            $this->columnCache[$table] = Schema::getColumnListing($table);
        }

        return $this->columnCache[$table];
    }

    private function insertRows(string $table, array $rows): void
    {
        foreach (array_chunk($rows, 100) as $chunk) {
            DB::table($table)->insert($chunk);
        }
    }
}
