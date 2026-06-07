# Opportunet Mondiale

Plateforme Laravel bilingue pour les opportunités, articles, formations, services CV, prières, témoignages et espaces privés utilisateur, entreprise et administration.

## Fonctionnalités principales

- Offres et opportunités avec filtres, candidatures et suivi
- Articles avec commentaires modérés
- Dépôt CV et échanges avec l’équipe
- Formations avec inscription et suivi
- Mur de prière et témoignages communautaires
- Administration par rôles
- SEO public: `robots.txt`, `sitemap.xml`, canonicals, `hreflang`, `schema.org`
- Sécurité: headers sécurisés, captcha, honeypot, rate limiting, contrôle d’accès par rôle

## Démarrage local

1. Copier `.env.example` vers `.env`
2. Générer la clé:

```bash
php artisan key:generate
```

3. Configurer la base MySQL dans `.env`
4. Installer les dépendances si nécessaire:

```bash
composer install
```

5. Lancer les migrations et seeders:

```bash
php artisan migrate --seed
```

6. Créer le lien de stockage public:

```bash
php artisan storage:link
```

7. Démarrer l’application:

```bash
php artisan serve
```

## Important avec XAMPP / Apache

Si `http://localhost` affiche la page XAMPP, ce n’est pas un bug Laravel: Apache ne pointe simplement pas encore vers ce projet.

Pour tester correctement le site dans le navigateur, il faut:

- soit utiliser `php artisan serve`
- soit créer un VirtualHost Apache dont le `DocumentRoot` pointe vers le dossier `public`

Le site ne doit jamais être servi depuis la racine du dépôt, mais depuis `public`.

## Préparation production

Le dépôt contient maintenant un exemple prêt pour la prod: `.env.production.example`.

Points à régler avant mise en ligne:

1. Renseigner `APP_URL` avec le vrai domaine HTTPS
2. Générer une vraie `APP_KEY`
3. Mettre `APP_ENV=production` et `APP_DEBUG=false`
4. Configurer la base MySQL de production
5. Configurer le SMTP réel
6. Exécuter `php artisan storage:link`
7. Exécuter `php artisan migrate --force`
8. Exécuter `php artisan optimize`
9. Lancer un worker de queue:

```bash
php artisan queue:work --tries=3
```

10. Planifier le scheduler Laravel si utilisé:

```bash
php artisan schedule:run
```

## Checklist de déploiement

```bash
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan test
```

## Vérification rapide après déploiement

- `/` charge correctement
- `/robots.txt` répond
- `/sitemap.xml` répond
- `/up` répond
- les uploads publics s’affichent correctement via `/storage/...`
- la connexion fonctionne en HTTPS
- les formulaires contact, prière et newsletter répondent

## Tests

Lancer toute la suite:

```bash
php artisan test
```

## Notes de déploiement web

- `DocumentRoot` doit pointer vers `public`
- activer HTTPS côté serveur ou reverse proxy
- si le SSL est terminé en amont, garder `APP_FORCE_HTTPS=true`
- démarrer un worker de queue en continu pour les tâches asynchrones
- surveiller les logs dans `storage/logs`
- des exemples serveur sont fournis dans `deploy/apache-vhost.example.conf` et `deploy/nginx.example.conf`
