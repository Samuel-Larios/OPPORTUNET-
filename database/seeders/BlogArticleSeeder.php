<?php

namespace Database\Seeders;

use App\Models\BlogArticle;
use App\Models\BlogArticleImage;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogArticleSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = User::query()->where('email', 'larioss383@gmail.com')->value('id');
        $careerAdviceCategoryId = Category::query()->where('slug', 'conseils-emploi')->value('id');
        $inspirationCategoryId = Category::query()->where('slug', 'inspiration')->value('id');
        $newsCategoryId = Category::query()->where('slug', 'actualites')->value('id');
        $faithCategoryId = Category::query()->where('slug', 'temoignages-foi')->value('id');

        $articles = [
            [
                'slug' => '5-erreurs-qui-bloquent-votre-candidature',
                'categorie_id' => $careerAdviceCategoryId,
                'titre_fr' => '5 erreurs qui bloquent votre candidature',
                'titre_en' => '5 mistakes that block your application',
                'extrait_fr' => 'Des erreurs simples peuvent freiner une candidature solide. Voici comment les reperer et les corriger rapidement.',
                'extrait_en' => 'Simple mistakes can slow down a strong application. Here is how to spot and fix them quickly.',
                'contenu_fr' => "Un bon profil peut passer inaperçu quand la candidature manque de clarté.\n\nPremière erreur : envoyer le même CV partout sans adaptation.\n\nDeuxième erreur : négliger l'objet du message et la structure de l'email.\n\nTroisième erreur : manquer de preuves concrètes sur ses résultats.\n\nQuatrième erreur : oublier de relire avant l'envoi.\n\nCinquième erreur : rester passif après la candidature.\n\nUne candidature efficace raconte une trajectoire, montre une valeur claire et facilite la lecture du recruteur.",
                'contenu_en' => "A strong profile can go unnoticed when the application lacks clarity.\n\nFirst mistake: sending the same resume everywhere without adapting it.\n\nSecond mistake: neglecting the email subject line and message structure.\n\nThird mistake: failing to provide concrete proof of results.\n\nFourth mistake: forgetting to proofread before sending.\n\nFifth mistake: staying passive after the application.\n\nAn effective application tells a story, shows clear value, and makes the recruiter's job easier.",
                'tags' => ['cv', 'candidature', 'emploi'],
                'temps_lecture' => '4 min',
                'publie_le' => now()->subDays(5),
                'en_vedette' => true,
                'vues' => 124,
                'images' => [
                    [
                        'image_path' => 'images/tm-622-screen-01.jpg',
                        'alt_fr' => 'Bureau de travail avec ordinateur et bloc-notes pour preparer une candidature',
                        'alt_en' => 'Work desk with laptop and notebook used to prepare an application',
                        'is_featured' => true,
                    ],
                    [
                        'image_path' => 'images/tm-622-screen-02.jpg',
                        'alt_fr' => 'Professionnelle relisant un dossier de candidature',
                        'alt_en' => 'Professional reviewing an application file',
                    ],
                    [
                        'image_path' => 'images/tm-622-screen-03.jpg',
                        'alt_fr' => 'Notes de strategie pour ameliorer un CV et une lettre de motivation',
                        'alt_en' => 'Strategy notes to improve a resume and cover letter',
                    ],
                ],
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
                'images' => [
                    [
                        'image_path' => 'images/tm-622-screen-04.jpg',
                        'alt_fr' => 'Personne contemplant l horizon dans une saison de reflexion',
                        'alt_en' => 'Person looking at the horizon during a reflective season',
                        'is_featured' => true,
                    ],
                    [
                        'image_path' => 'images/tm-622-screen-05.jpg',
                        'alt_fr' => 'Carnet de vision et objectifs personnels en cours de redaction',
                        'alt_en' => 'Vision journal and personal goals being written down',
                    ],
                ],
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
                'images' => [
                    [
                        'image_path' => 'images/tm-622-screen-03.jpg',
                        'alt_fr' => 'Equipe de plateforme preparant le lancement de la rubrique articles',
                        'alt_en' => 'Platform team preparing the launch of the articles section',
                        'is_featured' => true,
                    ],
                    [
                        'image_path' => 'images/tm-622-screen-01.jpg',
                        'alt_fr' => 'Interface numerique presentant de nouveaux contenus editoriaux',
                        'alt_en' => 'Digital interface presenting new editorial content',
                    ],
                ],
            ],
            [
                'slug' => 'temoigner-de-sa-foi-sans-perdre-son-excellence',
                'categorie_id' => $faithCategoryId,
                'titre_fr' => 'Temoigner de sa foi sans perdre son excellence',
                'titre_en' => 'Witnessing your faith without losing your excellence',
                'extrait_fr' => 'La foi n affaiblit pas l exigence. Elle peut au contraire fortifier votre constance et votre integrite.',
                'extrait_en' => 'Faith does not weaken excellence. It can strengthen your consistency and integrity instead.',
                'contenu_fr' => "Dans de nombreux environnements professionnels, témoigner de sa foi demande de la sagesse.\n\nIl ne s'agit pas de parler plus fort, mais de vivre plus juste.\n\nL'excellence, la fiabilité, l'écoute et l'intégrité rendent un témoignage crédible.\n\nQuand la compétence rencontre la paix intérieure, elle devient une lumière visible.",
                'contenu_en' => "In many professional environments, witnessing your faith requires wisdom.\n\nIt is not about speaking louder, but about living more faithfully.\n\nExcellence, reliability, listening, and integrity make a testimony credible.\n\nWhen competence meets inner peace, it becomes a visible light.",
                'tags' => ['foi', 'travail', 'integrite'],
                'temps_lecture' => '5 min',
                'publie_le' => now()->subDays(2),
                'en_vedette' => false,
                'vues' => 68,
                'images' => [
                    [
                        'image_path' => 'images/tm-622-screen-02.jpg',
                        'alt_fr' => 'Professionnelle en posture de confiance et de service',
                        'alt_en' => 'Professional showing confidence and a spirit of service',
                        'is_featured' => true,
                    ],
                    [
                        'image_path' => 'images/tm-622-screen-05.jpg',
                        'alt_fr' => 'Moment de pause, de priere et de reflexion dans un parcours professionnel',
                        'alt_en' => 'Moment of pause, prayer, and reflection in a professional journey',
                    ],
                ],
            ],
        ];

        foreach ($articles as $articleData) {
            $images = collect($articleData['images'] ?? [])->take(5)->values();
            $featuredImage = $images->firstWhere('is_featured', true) ?? $images->first();

            $article = BlogArticle::query()->updateOrCreate(
                ['slug' => $articleData['slug']],
                array_merge([
                    'user_id' => $authorId,
                    'categorie_id' => $articleData['categorie_id'],
                    'titre' => $articleData['titre_fr'],
                    'extrait' => $articleData['extrait_fr'],
                    'contenu' => $articleData['contenu_fr'],
                    'image_couverture' => $featuredImage['image_path'] ?? null,
                    'image_alt' => $featuredImage['alt_fr'] ?? $articleData['titre_fr'],
                    'tags' => $articleData['tags'],
                    'statut' => 'publie',
                    'publie_le' => $articleData['publie_le'],
                    'en_vedette' => $articleData['en_vedette'],
                    'commentaires_actifs' => true,
                    'vues' => $articleData['vues'],
                    'partages' => 0,
                    'temps_lecture' => $articleData['temps_lecture'],
                ], $this->localizedValues('titre', $articleData['titre_fr'], $articleData['titre_en']), $this->localizedValues('extrait', $articleData['extrait_fr'], $articleData['extrait_en']), $this->localizedValues('contenu', $articleData['contenu_fr'], $articleData['contenu_en']), $this->localizedValues('image_alt', $featuredImage['alt_fr'] ?? $articleData['titre_fr'], $featuredImage['alt_en'] ?? $articleData['titre_en']))
            );

            $sortOrders = [];

            foreach ($images as $index => $imageData) {
                $sortOrder = $index + 1;
                $sortOrders[] = $sortOrder;

                BlogArticleImage::query()->updateOrCreate(
                    [
                        'blog_article_id' => $article->id,
                        'sort_order' => $sortOrder,
                    ],
                    array_merge([
                        'image_path' => $imageData['image_path'],
                        'alt' => $imageData['alt_fr'] ?? $articleData['titre_fr'],
                        'is_featured' => (bool) ($imageData['is_featured'] ?? false),
                    ], $this->localizedValues('alt', $imageData['alt_fr'] ?? $articleData['titre_fr'], $imageData['alt_en'] ?? $articleData['titre_en']))
                );
            }

            BlogArticleImage::query()
                ->where('blog_article_id', $article->id)
                ->when($sortOrders !== [], fn ($query) => $query->whereNotIn('sort_order', $sortOrders))
                ->delete();

            if (! $article->images()->where('is_featured', true)->exists()) {
                $firstImage = $article->images()->orderBy('sort_order')->first();

                if ($firstImage) {
                    $firstImage->update(['is_featured' => true]);
                }
            }
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
