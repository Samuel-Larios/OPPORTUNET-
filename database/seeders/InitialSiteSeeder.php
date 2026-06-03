<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InitialSiteSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedRoles();
        $this->seedAdmin();
        $this->seedCategories();
        $this->call(ServiceSeeder::class);
        $this->seedSettings();
        $this->seedVerse();
        $this->seedBanner();
        $this->seedFormations();
        $this->seedOpportunities();
        $this->call(BlogArticleSeeder::class);
        $this->seedTestimonials();
        $this->seedPrayerWall();
    }

    private function seedRoles(): void
    {
        DB::table('roles')->insertOrIgnore([
            ['nom' => 'super_admin', 'libelle' => 'Super Administrateur', 'description' => 'Acces total a toutes les fonctionnalites.', 'permissions' => json_encode(['*']), 'actif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'admin', 'libelle' => 'Administrateur', 'description' => 'Gestion du contenu et des utilisateurs.', 'permissions' => json_encode(['manage_content', 'manage_users', 'view_stats']), 'actif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'editeur', 'libelle' => 'Editeur', 'description' => 'Gestion des offres, publications et contenus associes.', 'permissions' => json_encode(['manage_offers', 'manage_trainings', 'view_stats']), 'actif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'moderateur', 'libelle' => 'Moderateur', 'description' => 'Moderation des commentaires, prieres et temoignages.', 'permissions' => json_encode(['moderate_comments', 'moderate_prayers', 'moderate_testimonials']), 'actif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'coach', 'libelle' => 'Coach / Formateur', 'description' => 'Acces aux CV, formations et accompagnements assignes.', 'permissions' => json_encode(['view_cvs', 'manage_formations', 'view_coaching']), 'actif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'entreprise', 'libelle' => 'Entreprise', 'description' => 'Compte entreprise pouvant soumettre des offres a validation et suivre les profils proposes.', 'permissions' => json_encode(['submit_offers', 'review_selected_profiles']), 'actif' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nom' => 'user', 'libelle' => 'Utilisateur', 'description' => 'Visiteur inscrit.', 'permissions' => json_encode(['view_public', 'submit_forms']), 'actif' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    private function seedAdmin(): void
    {
        $adminEmail = 'larioss383@gmail.com';
        $adminRoleId = DB::table('roles')->where('nom', 'super_admin')->value('id');

        if (! $adminRoleId) {
            return;
        }

        $insert = [
            'password' => Hash::make('motdepasse'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        foreach (
            [
                'role_id' => $adminRoleId,
                'prenom' => 'Admin',
                'nom' => 'Opportunet',
                'name' => 'Admin Opportunet',
                'actif' => true,
                'langue' => 'fr',
            ] as $column => $value
        ) {
            if (DB::getSchemaBuilder()->hasColumn('users', $column)) {
                $insert[$column] = $value;
            }
        }

        DB::table('users')->updateOrInsert(
            ['email' => $adminEmail],
            $insert
        );
    }

    private function seedCategories(): void
    {
        $categories = [
            ['type' => 'offre', 'slug' => 'emploi-carriere', 'nom_fr' => 'Emploi et Carriere', 'nom_en' => 'Jobs and Career', 'icone' => 'briefcase', 'couleur' => '#3B82F6'],
            ['type' => 'offre', 'slug' => 'bourses-etudes', 'nom_fr' => 'Bourses et Etudes', 'nom_en' => 'Scholarships and Studies', 'icone' => 'academic-cap', 'couleur' => '#8B5CF6'],
            ['type' => 'offre', 'slug' => 'stages-alternance', 'nom_fr' => 'Stages et Alternance', 'nom_en' => 'Internships and Work-study', 'icone' => 'clipboard-list', 'couleur' => '#F59E0B'],
            ['type' => 'offre', 'slug' => 'ong-associations', 'nom_fr' => 'ONG et Associations', 'nom_en' => 'NGOs and Associations', 'icone' => 'heart', 'couleur' => '#EF4444'],
            ['type' => 'offre', 'slug' => 'appels-projets', 'nom_fr' => 'Appels a Projets', 'nom_en' => 'Calls for Projects', 'icone' => 'light-bulb', 'couleur' => '#10B981'],
            ['type' => 'formation', 'slug' => 'developpement-personnel', 'nom_fr' => 'Developpement Personnel', 'nom_en' => 'Personal Development', 'icone' => 'user', 'couleur' => '#3B82F6'],
            ['type' => 'formation', 'slug' => 'entrepreneuriat', 'nom_fr' => 'Entrepreneuriat', 'nom_en' => 'Entrepreneurship', 'icone' => 'chart-bar', 'couleur' => '#F59E0B'],
            ['type' => 'formation', 'slug' => 'digital-tech', 'nom_fr' => 'Digital et Tech', 'nom_en' => 'Digital and Tech', 'icone' => 'code', 'couleur' => '#6366F1'],
            ['type' => 'formation', 'slug' => 'leadership-management', 'nom_fr' => 'Leadership et Management', 'nom_en' => 'Leadership and Management', 'icone' => 'users', 'couleur' => '#EC4899'],
            ['type' => 'formation', 'slug' => 'soft-skills', 'nom_fr' => 'Soft Skills', 'nom_en' => 'Soft Skills', 'icone' => 'star', 'couleur' => '#14B8A6'],
            ['type' => 'blog', 'slug' => 'conseils-emploi', 'nom_fr' => 'Conseils Emploi', 'nom_en' => 'Career Advice', 'icone' => 'newspaper', 'couleur' => '#3B82F6'],
            ['type' => 'blog', 'slug' => 'inspiration', 'nom_fr' => 'Inspiration', 'nom_en' => 'Inspiration', 'icone' => 'sparkles', 'couleur' => '#F59E0B'],
            ['type' => 'blog', 'slug' => 'actualites', 'nom_fr' => 'Actualites', 'nom_en' => 'News', 'icone' => 'bell', 'couleur' => '#EF4444'],
            ['type' => 'blog', 'slug' => 'temoignages-foi', 'nom_fr' => 'Temoignages de foi', 'nom_en' => 'Faith Testimonies', 'icone' => 'heart', 'couleur' => '#8B5CF6'],
            ['type' => 'service', 'slug' => 'redaction-cv', 'nom_fr' => 'Redaction CV', 'nom_en' => 'CV Writing', 'icone' => 'document-text', 'couleur' => '#3B82F6'],
            ['type' => 'service', 'slug' => 'coaching', 'nom_fr' => 'Coaching', 'nom_en' => 'Coaching', 'icone' => 'chat-alt-2', 'couleur' => '#10B981'],
            ['type' => 'service', 'slug' => 'orientation', 'nom_fr' => 'Orientation', 'nom_en' => 'Guidance', 'icone' => 'map', 'couleur' => '#F59E0B'],
        ];

        foreach ($categories as $index => $category) {
            DB::table('categories')->updateOrInsert(
                ['slug' => $category['slug']],
                array_merge([
                    'type' => $category['type'],
                    'nom' => $category['nom_fr'],
                    'icone' => $category['icone'],
                    'couleur' => $category['couleur'],
                    'actif' => true,
                    'ordre' => $index,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $this->localizedValues('nom', $category['nom_fr'], $category['nom_en']))
            );
        }
    }

    private function seedServices(): void
    {
        $services = [
            [
                'slug' => 'redaction-optimisation-cv',
                'type' => 'redaction_cv',
                'titre_fr' => 'Redaction et Optimisation de CV',
                'titre_en' => 'CV Writing and Optimization',
                'description_courte_fr' => 'Un CV professionnel et optimise ATS pour decrocher vos entretiens.',
                'description_courte_en' => 'A professional ATS-optimized resume that helps you secure interviews.',
                'description_longue_fr' => 'Nous redigeons un CV personnalise qui met en valeur vos competences, votre experience et votre potentiel. Le service inclut analyse de profil, redaction complete, design moderne et conseils pratiques.',
                'description_longue_en' => 'We craft a personalized resume that highlights your skills, experience, and potential. The service includes profile analysis, full writing, modern design, and practical advice.',
                'prix' => 15000,
                'devise' => 'XOF',
                'duree_fr' => '48h a 72h',
                'duree_en' => '48 to 72 hours',
                'icone' => 'document-text',
                'ordre' => 1,
            ],
            [
                'slug' => 'coaching-professionnel',
                'type' => 'coaching',
                'titre_fr' => 'Coaching Professionnel',
                'titre_en' => 'Professional Coaching',
                'description_courte_fr' => 'Preparez vos entretiens, boostez votre confiance et atteignez vos objectifs professionnels.',
                'description_courte_en' => 'Prepare for interviews, grow your confidence, and reach your professional goals.',
                'description_longue_fr' => 'Nos seances individuelles vous accompagnent dans la preparation aux entretiens, la clarification de votre projet professionnel, la gestion du stress et l optimisation de votre profil LinkedIn.',
                'description_longue_en' => 'Our one-on-one sessions help you prepare for interviews, clarify your career plan, manage stress, and improve your LinkedIn profile.',
                'prix' => 25000,
                'devise' => 'XOF',
                'duree_fr' => '1 seance (1h)',
                'duree_en' => '1 session (1h)',
                'icone' => 'chat-alt-2',
                'ordre' => 2,
            ],
            [
                'slug' => 'orientation-professionnelle',
                'type' => 'orientation',
                'titre_fr' => 'Orientation Professionnelle',
                'titre_en' => 'Career Guidance',
                'description_courte_fr' => 'Trouvez votre voie grace a un bilan personnalise et des conseils d experts.',
                'description_courte_en' => 'Find your path through personalized assessment and expert guidance.',
                'description_longue_fr' => 'Nous vous aidons a identifier votre potentiel, choisir le bon secteur et planifier un parcours professionnel coherent avec vos aspirations.',
                'description_longue_en' => 'We help you identify your potential, choose the right sector, and build a professional journey aligned with your aspirations.',
                'prix' => 20000,
                'devise' => 'XOF',
                'duree_fr' => '2 seances (2x1h)',
                'duree_en' => '2 sessions (2x1h)',
                'icone' => 'map',
                'ordre' => 3,
            ],
            [
                'slug' => 'accompagnement-complet',
                'type' => 'accompagnement',
                'titre_fr' => 'Accompagnement Complet',
                'titre_en' => 'Full Support Program',
                'description_courte_fr' => 'Un suivi sur mesure de A a Z pour decrocher votre prochaine opportunite.',
                'description_courte_en' => 'Tailored end-to-end support to help you secure your next opportunity.',
                'description_longue_fr' => 'Ce pack inclut CV et lettre de motivation, seances de coaching, veille personnalisee, preparation aux entretiens et suivi post-candidature.',
                'description_longue_en' => 'This package includes resume and cover letter support, coaching sessions, personalized opportunity monitoring, interview preparation, and post-application follow-up.',
                'prix' => 55000,
                'devise' => 'XOF',
                'duree_fr' => '1 mois',
                'duree_en' => '1 month',
                'icone' => 'support',
                'ordre' => 4,
            ],
        ];

        foreach ($services as $service) {
            DB::table('services')->updateOrInsert(
                ['slug' => $service['slug']],
                array_merge([
                    'titre' => $service['titre_fr'],
                    'type' => $service['type'],
                    'description_courte' => $service['description_courte_fr'],
                    'description_longue' => $service['description_longue_fr'],
                    'prix' => $service['prix'],
                    'devise' => $service['devise'],
                    'duree' => $service['duree_fr'],
                    'icone' => $service['icone'],
                    'ordre' => $service['ordre'],
                    'actif' => true,
                    'en_vedette' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $this->localizedValues('titre', $service['titre_fr'], $service['titre_en']), $this->localizedValues('description_courte', $service['description_courte_fr'], $service['description_courte_en']), $this->localizedValues('description_longue', $service['description_longue_fr'], $service['description_longue_en']), $this->localizedValues('duree', $service['duree_fr'], $service['duree_en']))
            );
        }
    }

    private function seedSettings(): void
    {
        $settings = [
            ['cle' => 'site_nom', 'fr' => 'Opportunet Mondiale', 'en' => 'Opportunet Mondiale', 'type' => 'texte', 'groupe' => 'general', 'label' => 'Nom du site', 'public' => true],
            ['cle' => 'site_slogan', 'fr' => 'Votre avenir commence ici', 'en' => 'Your future starts here', 'type' => 'texte', 'groupe' => 'general', 'label' => 'Slogan', 'public' => true],
            ['cle' => 'site_email', 'fr' => 'contact@opportunetmondiale.com', 'en' => 'contact@opportunetmondiale.com', 'type' => 'texte', 'groupe' => 'contact', 'label' => 'Email principal', 'public' => true],
            ['cle' => 'site_horaires', 'fr' => 'Lundi - Samedi 08:00 - 22:00', 'en' => 'Monday - Saturday 08:00 - 22:00', 'type' => 'texte', 'groupe' => 'contact', 'label' => 'Horaires de contact', 'public' => true],
            ['cle' => 'site_adresse', 'fr' => 'En face de la Mairie de Missérété, Ouémé, BJ', 'en' => 'Opposite the Missérété Town Hall, Ouémé, BJ', 'type' => 'texte', 'groupe' => 'contact', 'label' => 'Adresse principale', 'public' => true],
            ['cle' => 'whatsapp_numero', 'fr' => '+229XXXXXXXXX', 'en' => '+229XXXXXXXXX', 'type' => 'texte', 'groupe' => 'whatsapp', 'label' => 'Numero WhatsApp', 'public' => true],
            ['cle' => 'whatsapp_message_defaut', 'fr' => 'Bonjour Opportunet Mondiale, je souhaite plus d informations.', 'en' => 'Hello Opportunet Mondiale, I would like more information.', 'type' => 'texte', 'groupe' => 'whatsapp', 'label' => 'Message WhatsApp par defaut', 'public' => true],
            ['cle' => 'facebook_url', 'fr' => '', 'en' => '', 'type' => 'texte', 'groupe' => 'reseaux', 'label' => 'Facebook URL', 'public' => true],
            ['cle' => 'instagram_url', 'fr' => '', 'en' => '', 'type' => 'texte', 'groupe' => 'reseaux', 'label' => 'Instagram URL', 'public' => true],
            ['cle' => 'linkedin_url', 'fr' => '', 'en' => '', 'type' => 'texte', 'groupe' => 'reseaux', 'label' => 'LinkedIn URL', 'public' => true],
            ['cle' => 'youtube_url', 'fr' => '', 'en' => '', 'type' => 'texte', 'groupe' => 'reseaux', 'label' => 'YouTube URL', 'public' => true],
            ['cle' => 'tiktok_url', 'fr' => '', 'en' => '', 'type' => 'texte', 'groupe' => 'reseaux', 'label' => 'TikTok URL', 'public' => true],
            ['cle' => 'google_analytics_id', 'fr' => '', 'en' => '', 'type' => 'texte', 'groupe' => 'seo', 'label' => 'Google Analytics ID', 'public' => false],
            ['cle' => 'meta_description_defaut', 'fr' => 'Opportunet Mondiale - Offres d emploi, formations, coaching et accompagnement professionnel en Afrique.', 'en' => 'Opportunet Mondiale - Jobs, training, coaching, and professional support across Africa.', 'type' => 'texte', 'groupe' => 'seo', 'label' => 'Meta description par defaut', 'public' => false],
            ['cle' => 'moderation_commentaires', 'fr' => '1', 'en' => '1', 'type' => 'booleen', 'groupe' => 'moderation', 'label' => 'Moderation des commentaires', 'public' => false],
            ['cle' => 'moderation_prieres', 'fr' => '1', 'en' => '1', 'type' => 'booleen', 'groupe' => 'moderation', 'label' => 'Moderation des prieres', 'public' => false],
            ['cle' => 'moderation_temoignages', 'fr' => '1', 'en' => '1', 'type' => 'booleen', 'groupe' => 'moderation', 'label' => 'Moderation des temoignages', 'public' => false],
            ['cle' => 'inscription_ouverte', 'fr' => '1', 'en' => '1', 'type' => 'booleen', 'groupe' => 'general', 'label' => 'Inscriptions ouvertes', 'public' => false],
            ['cle' => 'maintenance', 'fr' => '0', 'en' => '0', 'type' => 'booleen', 'groupe' => 'general', 'label' => 'Mode maintenance', 'public' => false],
        ];

        foreach ($settings as $setting) {
            DB::table('parametres_site')->updateOrInsert(
                ['cle' => $setting['cle']],
                array_merge([
                    'valeur' => $setting['fr'],
                    'type' => $setting['type'],
                    'groupe' => $setting['groupe'],
                    'label' => $setting['label'],
                    'public' => $setting['public'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $this->localizedValues('valeur', $setting['fr'], $setting['en']))
            );
        }
    }

    private function seedVerse(): void
    {
        DB::table('versets')->updateOrInsert(
            ['ordre' => 1],
            array_merge([
                'reference' => 'Jeremie 29:11',
                'texte' => 'Car je connais les projets que j ai formes sur vous, dit l Eternel, projets de paix et non de malheur, afin de vous donner un avenir et de l esperance.',
                'version' => 'LSG',
                'actif' => true,
                'afficher_accueil' => true,
                'ordre' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ], $this->localizedValues('texte', 'Car je connais les projets que j ai formes sur vous, dit l Eternel, projets de paix et non de malheur, afin de vous donner un avenir et de l esperance.', 'For I know the plans I have for you, says the Lord, plans for peace and not for harm, to give you a future and a hope.'), $this->localizedValues('version', 'LSG', 'NIV'))
        );
    }

    private function seedBanner(): void
    {
        DB::table('bannieres')->updateOrInsert(
            ['ordre' => 1],
            array_merge([
                'titre' => 'Des services concrets pour votre avenir',
                'sous_titre' => 'Une plateforme bilingue pour vos opportunites, vos formations, votre orientation et votre croissance professionnelle et spirituelle.',
                'bouton1_texte' => 'Voir les offres',
                'bouton1_lien' => '#home-opportunities',
                'bouton1_style' => 'primary',
                'bouton2_texte' => 'Parler sur WhatsApp',
                'bouton2_lien' => '#home-contact',
                'bouton2_style' => 'whatsapp',
                'actif' => true,
                'ordre' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ], $this->localizedValues('titre', 'Des services concrets pour votre avenir', 'Practical services for your future'), $this->localizedValues('sous_titre', 'Une plateforme bilingue pour vos opportunites, vos formations, votre orientation et votre croissance professionnelle et spirituelle.', 'A bilingual platform for opportunities, training, guidance, and your professional and spiritual growth.'), $this->localizedValues('bouton1_texte', 'Voir les offres', 'See opportunities'), $this->localizedValues('bouton2_texte', 'Parler sur WhatsApp', 'Chat on WhatsApp'))
        );
    }

    private function seedFormations(): void
    {
        $categoryId = DB::table('categories')->where('slug', 'developpement-personnel')->value('id');

        $formations = [
            [
                'slug' => 'bootcamp-impact-professionnel',
                'titre_fr' => 'Bootcamp Impact Professionnel',
                'titre_en' => 'Professional Impact Bootcamp',
                'description_courte_fr' => 'Un parcours intensif pour clarifier votre projet, votre positionnement et votre prochaine etape.',
                'description_courte_en' => 'An intensive journey to clarify your path, positioning, and next step.',
                'description_longue_fr' => 'Une formation pratique pour structurer votre avenir professionnel, renforcer votre profil et avancer avec confiance.',
                'description_longue_en' => 'A practical training program to shape your future, strengthen your profile, and move forward with confidence.',
                'mode' => 'en_ligne',
                'gratuit' => false,
                'prix' => 35000,
                'devise' => 'XOF',
                'duree_heures' => 8,
                'nb_seances' => 4,
                'date_debut' => now()->addDays(12)->toDateString(),
                'date_fin' => now()->addDays(26)->toDateString(),
                'niveau' => 'intermediaire',
                'statut' => 'ouverte',
                'en_vedette' => true,
                'whatsapp_fr' => 'Bonjour, je souhaite m inscrire au Bootcamp Impact Professionnel.',
                'whatsapp_en' => 'Hello, I would like to enroll in the Professional Impact Bootcamp.',
            ],
            [
                'slug' => 'atelier-cv-linkedin',
                'titre_fr' => 'Atelier CV et LinkedIn',
                'titre_en' => 'CV and LinkedIn Workshop',
                'description_courte_fr' => 'Un atelier concret pour moderniser votre CV et valoriser votre presence professionnelle en ligne.',
                'description_courte_en' => 'A practical workshop to modernize your resume and strengthen your professional presence online.',
                'description_longue_fr' => 'Des outils simples et des retours personnalises pour rendre votre candidature plus convaincante.',
                'description_longue_en' => 'Simple tools and personalized feedback to make your applications more compelling.',
                'mode' => 'hybride',
                'gratuit' => true,
                'prix' => 0,
                'devise' => 'XOF',
                'duree_heures' => 3,
                'nb_seances' => 1,
                'date_debut' => now()->addDays(7)->toDateString(),
                'date_fin' => now()->addDays(7)->toDateString(),
                'niveau' => 'debutant',
                'statut' => 'ouverte',
                'en_vedette' => true,
                'whatsapp_fr' => 'Bonjour, je souhaite participer a l Atelier CV et LinkedIn.',
                'whatsapp_en' => 'Hello, I would like to join the CV and LinkedIn Workshop.',
            ],
        ];

        foreach ($formations as $index => $formation) {
            DB::table('formations')->updateOrInsert(
                ['slug' => $formation['slug']],
                array_merge([
                    'categorie_id' => $categoryId,
                    'titre' => $formation['titre_fr'],
                    'description_courte' => $formation['description_courte_fr'],
                    'description_longue' => $formation['description_longue_fr'],
                    'mode' => $formation['mode'],
                    'prix' => $formation['prix'],
                    'devise' => $formation['devise'],
                    'gratuit' => $formation['gratuit'],
                    'duree_heures' => $formation['duree_heures'],
                    'nb_seances' => $formation['nb_seances'],
                    'date_debut' => $formation['date_debut'],
                    'date_fin' => $formation['date_fin'],
                    'niveau' => $formation['niveau'],
                    'statut' => $formation['statut'],
                    'en_vedette' => $formation['en_vedette'],
                    'whatsapp_message' => $formation['whatsapp_fr'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $this->localizedValues('titre', $formation['titre_fr'], $formation['titre_en']), $this->localizedValues('description_courte', $formation['description_courte_fr'], $formation['description_courte_en']), $this->localizedValues('description_longue', $formation['description_longue_fr'], $formation['description_longue_en']), $this->localizedValues('whatsapp_message', $formation['whatsapp_fr'], $formation['whatsapp_en']))
            );
        }
    }

    private function seedOpportunities(): void
    {
        $jobCategoryId = DB::table('categories')->where('slug', 'emploi-carriere')->value('id');
        $scholarshipCategoryId = DB::table('categories')->where('slug', 'bourses-etudes')->value('id');
        $projectCategoryId = DB::table('categories')->where('slug', 'appels-projets')->value('id');

        $opportunities = [
            [
                'slug' => 'assistant-projet-benin',
                'categorie_id' => $jobCategoryId,
                'titre_fr' => 'Assistant Projet et Communication',
                'titre_en' => 'Project and Communications Assistant',
                'organisation' => 'Impact Afrique Hub',
                'type' => 'emploi',
                'contrat' => 'cdd',
                'lieu' => 'Cotonou',
                'pays' => 'Benin',
                'teletravail' => false,
                'description_fr' => 'Une opportunite pour les jeunes professionnels souhaitant contribuer a des projets a impact social et educatif.',
                'description_en' => 'An opportunity for young professionals eager to contribute to social and educational impact projects.',
                'profil_fr' => 'Bon niveau redactionnel, esprit d organisation et maitrise des outils numeriques.',
                'profil_en' => 'Strong writing skills, organizational mindset, and confidence with digital tools.',
                'avantages_fr' => 'Encadrement, environnement stimulant et possibilites d evolution.',
                'avantages_en' => 'Mentoring, a stimulating environment, and growth opportunities.',
                'lien_candidature' => 'mailto:contact@opportunetmondiale.com',
                'date_publication' => now()->subDays(2)->toDateString(),
                'date_expiration' => now()->addDays(18)->toDateString(),
                'statut' => 'publie',
                'en_vedette' => true,
                'urgent' => true,
            ],
            [
                'slug' => 'bourse-leadership-afrique',
                'categorie_id' => $scholarshipCategoryId,
                'titre_fr' => 'Bourse Leadership Afrique Francophone',
                'titre_en' => 'Francophone Africa Leadership Scholarship',
                'organisation' => 'Rising Leaders Initiative',
                'type' => 'bourse',
                'contrat' => 'non_applicable',
                'lieu' => 'En ligne',
                'pays' => 'Afrique',
                'teletravail' => true,
                'description_fr' => 'Un programme de bourse pour former une nouvelle generation de leaders africains engages.',
                'description_en' => 'A scholarship program designed to equip a new generation of committed African leaders.',
                'profil_fr' => 'Jeunes leaders, etudiants ou professionnels avec projet d impact.',
                'profil_en' => 'Young leaders, students, or professionals with an impact project.',
                'avantages_fr' => 'Formation, mentorat international et mise en reseau.',
                'avantages_en' => 'Training, international mentoring, and networking opportunities.',
                'lien_candidature' => 'mailto:contact@opportunetmondiale.com',
                'date_publication' => now()->subDays(4)->toDateString(),
                'date_expiration' => now()->addDays(24)->toDateString(),
                'statut' => 'publie',
                'en_vedette' => true,
                'urgent' => false,
            ],
            [
                'slug' => 'appel-a-projets-jeunes-createurs',
                'categorie_id' => $projectCategoryId,
                'titre_fr' => 'Appel a Projets Jeunes Createurs',
                'titre_en' => 'Young Creators Call for Projects',
                'organisation' => 'Opportunet Mondiale',
                'type' => 'appel_offre',
                'contrat' => 'non_applicable',
                'lieu' => 'Hybride',
                'pays' => 'International',
                'teletravail' => true,
                'description_fr' => 'Soumettez votre idee ou votre initiative a impact pour beneficier de visibilite, orientation et mentorat.',
                'description_en' => 'Submit your idea or impact initiative to receive visibility, guidance, and mentoring.',
                'profil_fr' => 'Porteurs de vision, associations, jeunes entrepreneurs et leaders communautaires.',
                'profil_en' => 'Vision-driven founders, associations, young entrepreneurs, and community leaders.',
                'avantages_fr' => 'Visibilite, mentorat, accompagnement et acces a notre ecosysteme.',
                'avantages_en' => 'Visibility, mentoring, support, and access to our ecosystem.',
                'lien_candidature' => 'mailto:contact@opportunetmondiale.com',
                'date_publication' => now()->subDay()->toDateString(),
                'date_expiration' => now()->addDays(30)->toDateString(),
                'statut' => 'publie',
                'en_vedette' => true,
                'urgent' => false,
            ],
        ];

        foreach ($opportunities as $opportunity) {
            DB::table('opportunites')->updateOrInsert(
                ['slug' => $opportunity['slug']],
                array_merge([
                    'categorie_id' => $opportunity['categorie_id'],
                    'titre' => $opportunity['titre_fr'],
                    'organisation' => $opportunity['organisation'],
                    'type' => $opportunity['type'],
                    'contrat' => $opportunity['contrat'],
                    'lieu' => $opportunity['lieu'],
                    'pays' => $opportunity['pays'],
                    'teletravail' => $opportunity['teletravail'],
                    'description' => $opportunity['description_fr'],
                    'profil_recherche' => $opportunity['profil_fr'],
                    'avantages' => $opportunity['avantages_fr'],
                    'lien_candidature' => $opportunity['lien_candidature'],
                    'date_publication' => $opportunity['date_publication'],
                    'date_expiration' => $opportunity['date_expiration'],
                    'statut' => $opportunity['statut'],
                    'en_vedette' => $opportunity['en_vedette'],
                    'urgent' => $opportunity['urgent'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $this->localizedValues('titre', $opportunity['titre_fr'], $opportunity['titre_en']), $this->localizedValues('description', $opportunity['description_fr'], $opportunity['description_en']), $this->localizedValues('profil_recherche', $opportunity['profil_fr'], $opportunity['profil_en']), $this->localizedValues('avantages', $opportunity['avantages_fr'], $opportunity['avantages_en']))
            );
        }
    }

    private function seedBlogArticles(): void
    {
        $authorId = DB::table('users')->where('email', 'larioss383@gmail.com')->value('id');
        $careerAdviceCategoryId = DB::table('categories')->where('slug', 'conseils-emploi')->value('id');
        $inspirationCategoryId = DB::table('categories')->where('slug', 'inspiration')->value('id');
        $newsCategoryId = DB::table('categories')->where('slug', 'actualites')->value('id');

        $articles = [
            [
                'slug' => '5-erreurs-qui-bloquent-votre-candidature',
                'categorie_id' => $careerAdviceCategoryId,
                'titre_fr' => '5 erreurs qui bloquent votre candidature',
                'titre_en' => '5 mistakes that block your application',
                'extrait_fr' => 'Des erreurs simples peuvent freiner une candidature solide. Voici les reperer et les corriger rapidement.',
                'extrait_en' => 'Simple mistakes can slow down a strong application. Here is how to spot and fix them quickly.',
                'contenu_fr' => "Un bon profil peut passer inaperçu quand la candidature manque de clarté.\n\nPremière erreur : envoyer le même CV partout sans adaptation.\n\nDeuxième erreur : négliger l'objet du message et la structure de l'email.\n\nTroisième erreur : manquer de preuves concrètes sur ses résultats.\n\nQuatrième erreur : oublier de relire avant l'envoi.\n\nCinquième erreur : rester passif après la candidature.\n\nUne candidature efficace raconte une trajectoire, montre une valeur claire et facilite la lecture du recruteur.",
                'contenu_en' => "A strong profile can go unnoticed when the application lacks clarity.\n\nFirst mistake: sending the same resume everywhere without adapting it.\n\nSecond mistake: neglecting the email subject line and message structure.\n\nThird mistake: failing to provide concrete proof of results.\n\nFourth mistake: forgetting to proofread before sending.\n\nFifth mistake: staying passive after the application.\n\nAn effective application tells a story, shows clear value, and makes the recruiter's job easier.",
                'tags' => ['cv', 'candidature', 'emploi'],
                'temps_lecture' => '4 min',
                'publie_le' => now()->subDays(5),
                'en_vedette' => true,
                'vues' => 124,
            ],
            [
                'slug' => 'rester-fidele-a-sa-vision-dans-les-saisons-lentes',
                'categorie_id' => $inspirationCategoryId,
                'titre_fr' => 'Rester fidele a sa vision dans les saisons lentes',
                'titre_en' => 'Staying faithful to your vision in slow seasons',
                'extrait_fr' => 'Quand les resultats tardent, il faut apprendre a avancer avec discipline, paix et conviction.',
                'extrait_en' => 'When results take time, you need to move forward with discipline, peace, and conviction.',
                'contenu_fr' => "Les saisons lentes ne sont pas des saisons inutiles.\n\nElles révèlent la profondeur de votre motivation, la qualité de votre discipline et la solidité de votre appel.\n\nDans ces moments, il devient essentiel de garder une routine saine, de documenter ses progrès et de continuer à apprendre.\n\nLa vision ne grandit pas seulement dans l'accélération. Elle grandit aussi dans la constance.",
                'contenu_en' => "Slow seasons are not useless seasons.\n\nThey reveal the depth of your motivation, the quality of your discipline, and the strength of your calling.\n\nIn such moments, it becomes essential to keep a healthy routine, document your progress, and keep learning.\n\nVision does not grow only in acceleration. It also grows in consistency.",
                'tags' => ['inspiration', 'discipline', 'vision'],
                'temps_lecture' => '3 min',
                'publie_le' => now()->subDays(3),
                'en_vedette' => true,
                'vues' => 96,
            ],
            [
                'slug' => 'opportunet-mondiale-ouvre-un-espace-articles',
                'categorie_id' => $newsCategoryId,
                'titre_fr' => 'Opportunet Mondiale ouvre un espace articles',
                'titre_en' => 'Opportunet Mondiale launches its articles space',
                'extrait_fr' => 'Une nouvelle rubrique pour partager des conseils utiles, des actualites et des contenus d encouragement.',
                'extrait_en' => 'A new section to share useful advice, updates, and encouraging content.',
                'contenu_fr' => "Opportunet Mondiale enrichit sa plateforme avec un espace éditorial dédié aux articles.\n\nVous y retrouverez des contenus sur l'employabilité, la formation, la foi, la vision et les initiatives qui transforment les parcours.\n\nL'objectif est simple : offrir un espace clair, inspirant et pratique pour aider chacun à avancer.",
                'contenu_en' => "Opportunet Mondiale is expanding its platform with an editorial space dedicated to articles.\n\nYou will find content on employability, training, faith, vision, and initiatives that transform journeys.\n\nThe goal is simple: offer a clear, inspiring, and practical space to help people move forward.",
                'tags' => ['actualites', 'plateforme', 'contenus'],
                'temps_lecture' => '2 min',
                'publie_le' => now()->subDay(),
                'en_vedette' => false,
                'vues' => 51,
            ],
        ];

        foreach ($articles as $article) {
            DB::table('blog_articles')->updateOrInsert(
                ['slug' => $article['slug']],
                array_merge([
                    'user_id' => $authorId,
                    'categorie_id' => $article['categorie_id'],
                    'titre' => $article['titre_fr'],
                    'extrait' => $article['extrait_fr'],
                    'contenu' => $article['contenu_fr'],
                    'tags' => json_encode($article['tags']),
                    'statut' => 'publie',
                    'publie_le' => $article['publie_le'],
                    'en_vedette' => $article['en_vedette'],
                    'commentaires_actifs' => true,
                    'vues' => $article['vues'],
                    'partages' => 0,
                    'temps_lecture' => $article['temps_lecture'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $this->localizedValues('titre', $article['titre_fr'], $article['titre_en']), $this->localizedValues('extrait', $article['extrait_fr'], $article['extrait_en']), $this->localizedValues('contenu', $article['contenu_fr'], $article['contenu_en']))
            );
        }
    }

    private function seedTestimonials(): void
    {
        $testimonials = [
            [
                'prenom' => 'Grace',
                'nom' => 'A.',
                'pays' => 'Benin',
                'profession' => 'Assistante administrative',
                'type' => 'service_cv',
                'note' => 5,
                'ordre' => 1,
                'contenu_fr' => 'Mon CV a ete completement repense. En quelques semaines, j ai commence a recevoir des reponses beaucoup plus serieuses.',
                'contenu_en' => 'My resume was completely reworked. Within a few weeks, I started receiving much more serious responses.',
            ],
            [
                'prenom' => 'Daniel',
                'nom' => 'K.',
                'pays' => 'Togo',
                'profession' => 'Jeune entrepreneur',
                'type' => 'coaching',
                'note' => 5,
                'ordre' => 2,
                'contenu_fr' => 'Le coaching m a aide a clarifier mon projet et a retrouver confiance pour avancer avec discipline.',
                'contenu_en' => 'The coaching helped me clarify my project and regain the confidence to move forward with discipline.',
            ],
            [
                'prenom' => 'Mireille',
                'nom' => 'S.',
                'pays' => 'Cote d Ivoire',
                'profession' => 'Etudiante',
                'type' => 'formation_suivie',
                'note' => 5,
                'ordre' => 3,
                'contenu_fr' => 'J ai trouve a la fois une orientation concrete et un vrai encouragement spirituel. C est rare de trouver les deux ensemble.',
                'contenu_en' => 'I found both practical direction and real spiritual encouragement. It is rare to find both together.',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            DB::table('temoignages')->updateOrInsert(
                [
                    'prenom' => $testimonial['prenom'],
                    'profession' => $testimonial['profession'],
                ],
                array_merge([
                    'nom' => $testimonial['nom'],
                    'pays' => $testimonial['pays'],
                    'contenu' => $testimonial['contenu_fr'],
                    'type' => $testimonial['type'],
                    'note' => $testimonial['note'],
                    'statut' => 'approuve',
                    'en_vedette' => true,
                    'ordre' => $testimonial['ordre'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ], $this->localizedValues('contenu', $testimonial['contenu_fr'], $testimonial['contenu_en']))
            );
        }
    }

    private function seedPrayerWall(): void
    {
        $entries = [
            [
                'prenom' => 'Equipe OPM',
                'pays' => 'Benin',
                'email' => null,
                'sujet' => 'Courage, Dieu n oublie aucun effort seme avec foi. Continue d avancer, meme a petits pas.',
                'type' => 'encouragement',
                'anonyme' => false,
                'priants' => 18,
            ],
            [
                'prenom' => 'Sarah',
                'pays' => 'Benin',
                'email' => null,
                'sujet' => 'Merci au Seigneur pour une porte professionnelle ouverte apres une longue periode d attente et de priere.',
                'type' => 'temoignage_reponse',
                'anonyme' => false,
                'priants' => 27,
            ],
            [
                'prenom' => 'Jean',
                'pays' => 'Togo',
                'email' => 'jean@example.com',
                'sujet' => 'Priez pour de la sagesse, une orientation claire et une opportunite stable pour ma famille.',
                'type' => 'priere',
                'anonyme' => false,
                'priants' => 32,
            ],
        ];

        foreach ($entries as $entry) {
            DB::table('mur_de_prieres')->updateOrInsert(
                [
                    'prenom' => $entry['prenom'],
                    'type' => $entry['type'],
                ],
                [
                    'pays' => $entry['pays'],
                    'email' => $entry['email'],
                    'sujet' => $entry['sujet'],
                    'anonyme' => $entry['anonyme'],
                    'priants' => $entry['priants'],
                    'statut' => 'approuve',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    private function localizedValues(string $base, string $french, ?string $english = null): array
    {
        return [
            $base . '_fr' => $french,
            $base . '_en' => $english ?? $french,
        ];
    }
}
