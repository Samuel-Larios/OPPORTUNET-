<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'category_slug' => 'redaction-cv',
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
                'en_vedette' => true,
            ],
            [
                'category_slug' => 'coaching',
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
                'en_vedette' => true,
            ],
            [
                'category_slug' => 'orientation',
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
                'en_vedette' => true,
            ],
            [
                'category_slug' => null,
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
                'en_vedette' => true,
            ],
        ];

        foreach ($services as $service) {
            $categoryId = $service['category_slug']
                ? Category::query()
                    ->where('type', 'service')
                    ->where('slug', $service['category_slug'])
                    ->value('id')
                : null;

            DB::table('services')->updateOrInsert(
                ['slug' => $service['slug']],
                [
                    'categorie_id' => $categoryId,
                    'titre' => $service['titre_fr'],
                    'titre_fr' => $service['titre_fr'],
                    'titre_en' => $service['titre_en'],
                    'type' => $service['type'],
                    'description_courte' => $service['description_courte_fr'],
                    'description_courte_fr' => $service['description_courte_fr'],
                    'description_courte_en' => $service['description_courte_en'],
                    'description_longue' => $service['description_longue_fr'],
                    'description_longue_fr' => $service['description_longue_fr'],
                    'description_longue_en' => $service['description_longue_en'],
                    'prix' => $service['prix'],
                    'devise' => $service['devise'],
                    'duree' => $service['duree_fr'],
                    'duree_fr' => $service['duree_fr'],
                    'duree_en' => $service['duree_en'],
                    'icone' => $service['icone'],
                    'ordre' => $service['ordre'],
                    'actif' => true,
                    'en_vedette' => $service['en_vedette'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
