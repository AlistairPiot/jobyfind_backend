# 🚀 Déploiement Backend Jobyfind + Base de données

## 📋 Vue d'ensemble

**Stack déployée :**

-   **Backend** : Symfony sur Fly.io
-   **Base de données** : MySQL sur Railway
-   **URL API** : `https://jobyfind-api.fly.dev`

---

## 🗄️ 1. Base de données - Railway

### Configuration initiale

```bash
# Variables fournies par Railway
DATABASE_URL=mysql://root:TokQwLaIKxTKqWiltoaDJjJBRWDyCULJ@yamabiko.proxy.rlwy.net:17520/railway
```

### Accès à la base

-   **Interface** : [railway.app](https://railway.app)
-   **Host** : `yamabiko.proxy.rlwy.net`
-   **Port** : `17520`
-   **Database** : `railway`
-   **Username** : `root`
-   **Password** : `TokQwLaIKxTKqWiltoaDJjJBRWDyCULJ`

---

## ⚙️ 2. Backend Symfony - Fly.io

### Fichiers de configuration créés

#### `Dockerfile.prod`

```dockerfile
FROM php:8.2-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libpq-dev libzip-dev git unzip openssl

# Extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_pgsql pdo_mysql gd zip opcache

# Configuration OPCache pour production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Composer
COPY --from=composer:2.5.5 /usr/bin/composer /usr/bin/composer

# Modules Apache
RUN a2enmod rewrite headers

# Configuration Apache
COPY apache-config.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY . .

# Génération des clés JWT
RUN mkdir -p config/jwt \
    && openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096 -pass pass:jobyfind \
    && openssl pkey -in config/jwt/private.pem -passin pass:jobyfind -out config/jwt/public.pem -pubout

# Installation des dépendances
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Permissions
RUN chown -R www-data:www-data /var/www/html

EXPOSE 8080
CMD ["apache2-foreground"]
```

#### `apache-config.conf`

```apache
<VirtualHost *:8080>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
        DirectoryIndex index.php

        <IfModule mod_rewrite.c>
            RewriteEngine On
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteRule ^(.*)$ index.php [QSA,L]
        </IfModule>
    </Directory>

    # Headers de sécurité
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

#### `fly.toml`

```toml
app = "jobyfind-api"
primary_region = "cdg"

[build]
  dockerfile = "Dockerfile.prod"

[http_service]
  internal_port = 8080
  force_https = true
  auto_stop_machines = true
  auto_start_machines = true
  min_machines_running = 0
  processes = ["app"]

[[http_service.checks]]
  grace_period = "10s"
  interval = "30s"
  method = "GET"
  timeout = "5s"
  path = "/api/health"

[env]
  APP_ENV = "prod"
  APP_DEBUG = "false"
```

#### `src/Controller/HealthController.php`

```php
<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthController extends AbstractController
{
    #[Route('/api/health', name: 'health_check', methods: ['GET'])]
    public function healthCheck(): JsonResponse
    {
        return new JsonResponse([
            'status' => 'ok',
            'timestamp' => date('c'),
            'version' => '1.0.0'
        ]);
    }
}
```

### Configuration CORS

#### `config/packages/prod/nelmio_cors.yaml`

```yaml
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ["%env(CORS_ALLOW_ORIGIN)%"]
        allow_methods: ["GET", "OPTIONS", "POST", "PUT", "PATCH", "DELETE"]
        allow_headers: ["Content-Type", "Authorization", "X-Requested-With"]
        expose_headers: ["Link"]
        max_age: 3600
    paths:
        "^/api/":
            allow_origin: ["%env(CORS_ALLOW_ORIGIN)%"]
```

### Configuration Sécurité

#### Modification de `config/packages/security.yaml`

```yaml
access_control:
    # Routes publiques
    - { path: ^/api/login, roles: PUBLIC_ACCESS }
    - { path: ^/api/register, roles: PUBLIC_ACCESS }
    - { path: ^/api/health, roles: PUBLIC_ACCESS } # ← Ajouté pour Fly.io
```

---

## 🚀 3. Processus de déploiement

### Installation Fly.io CLI

```bash
curl -L https://fly.io/install.sh | sh
export PATH="$HOME/.fly/bin:$PATH"
flyctl auth login
```

### Création de l'application

```bash
cd jobyfind_backend
flyctl apps create jobyfind-api --region cdg
```

### Configuration des secrets

```bash
flyctl secrets set DATABASE_URL="mysql://root:TokQwLaIKxTKqWiltoaDJjJBRWDyCULJ@yamabiko.proxy.rlwy.net:17520/railway"
flyctl secrets set APP_SECRET="your_generated_secret"
flyctl secrets set JWT_PASSPHRASE="jobyfind"
flyctl secrets set STRIPE_SECRET_KEY="sk_test_your_stripe_key"
flyctl secrets set CORS_ALLOW_ORIGIN="https://jobyfind-frontend.vercel.app"
```

### Déploiement

```bash
flyctl deploy
```

### Création du schéma de base de données

```bash
# Via SSH sur le serveur
flyctl ssh console --command="php bin/console doctrine:schema:drop --force"
flyctl ssh console --command="php bin/console doctrine:schema:create"
```

---

## ✅ 4. Tests de validation

### Health Check

```bash
curl https://jobyfind-api.fly.dev/api/health
# Réponse : {"status":"ok","timestamp":"2025-06-10T20:23:28+00:00","version":"1.0.0"}
```

### Test d'inscription

```bash
curl -X POST https://jobyfind-api.fly.dev/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123","role":"ROLE_SCHOOL"}'
# Réponse : {"message":"User created successfully","role":"ROLE_SCHOOL"}
```

### Test de connexion

```bash
curl -X POST https://jobyfind-api.fly.dev/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"password123"}'
# Réponse : JWT Token + rôles
```

---

## 🔧 5. Résolution des problèmes rencontrés

### Erreur 1 : Module Apache Headers

**Problème** : `Invalid command 'Header'`
**Solution** : Ajout de `a2enmod headers` dans le Dockerfile

### Erreur 2 : Permissions dossier var

**Problème** : `chown: cannot access '/var/www/html/var'`
**Solution** : `mkdir -p var/cache var/log` avant chown

### Erreur 3 : JWT_SECRET_KEY manquant

**Problème** : Variable d'environnement non trouvée
**Solution** : Génération automatique des clés JWT dans le Dockerfile

### Erreur 4 : Health check échoue

**Problème** : Route /api/health nécessitait une authentification
**Solution** : Ajout de la route dans les accès publics

### Erreur 5 : Migrations cassées

**Problème** : Ordre des migrations incorrect
**Solution** : `doctrine:schema:drop` + `doctrine:schema:create`

---

## 📊 6. Monitoring et maintenance

### URLs importantes

-   **Application** : https://jobyfind-api.fly.dev
-   **Logs** : `flyctl logs --follow`
-   **SSH** : `flyctl ssh console`
-   **Monitoring** : https://fly.io/apps/jobyfind-api/monitoring

### Commandes utiles

```bash
# Voir les logs
flyctl logs --no-tail

# Redéployer
flyctl deploy

# Voir les secrets
flyctl secrets list

# Échelle (scaling)
flyctl scale count 1

# Status de l'app
flyctl status
```

---

## 🎯 Résultat final

✅ **Backend API** : Fonctionnel sur Fly.io  
✅ **Base de données** : MySQL Railway connectée  
✅ **JWT** : Authentification opérationnelle  
✅ **CORS** : Configuré pour le frontend  
✅ **Health monitoring** : Actif  
✅ **SSL/HTTPS** : Automatique via Fly.io

**L'API backend Jobyfind est maintenant prête pour la production !**

---

## 🔄 WORKFLOWS DE MISE À JOUR PRODUCTION

### 📋 Vue d'ensemble des changements

Selon le type de modification, voici les étapes à suivre pour mettre à jour votre application en production :

---

## 🔧 1. Changements BACKEND uniquement

### Modifications de code PHP/Symfony (pas de DB)

```bash
# 1. Développement local
cd jobyfind_backend
# Faire vos modifications...

# 2. Test local
symfony serve
# Tester les endpoints modifiés

# 3. Déploiement production
flyctl deploy
# ✅ Déploiement automatique sur Fly.io

# 4. Validation production
curl https://jobyfind-api.fly.dev/api/health
# Vérifier que l'API répond correctement
```

### Nouvelles dépendances Composer

```bash
# 1. Ajouter la dépendance localement
composer require nouvelle/dependance

# 2. Vérifier que composer.lock est à jour
git add composer.json composer.lock

# 3. Déployer (les dépendances seront installées automatiquement)
flyctl deploy
```

### Modifications configuration (config/packages/\*)

```bash
# 1. Modifier les fichiers de config
# 2. Tester localement
# 3. Déployer
flyctl deploy

# 4. Si secrets nécessaires, les ajouter :
flyctl secrets set NOUVELLE_CONFIG="valeur"
flyctl deploy  # Redéployer après ajout secrets
```

---

## 🗄️ 2. Changements BASE DE DONNÉES

### Nouvelles migrations Doctrine

```bash
# 1. Créer la migration localement
cd jobyfind_backend
php bin/console make:migration

# 2. Vérifier le fichier généré dans migrations/
# 3. Test local
php bin/console doctrine:migrations:migrate

# 4. Déploiement (les migrations sont automatiques dans Dockerfile)
flyctl deploy
# ✅ Les migrations se lancent automatiquement au build

# 5. Vérification que les migrations ont fonctionné
flyctl logs --app="jobyfind-api"
# Chercher dans les logs : "Migration ... executed"
```

### Modifications de schéma importantes

```bash
# ⚠️ ATTENTION : Faire un backup avant modifications importantes !

# 1. Backup de la production (optionnel mais recommandé)
# Se connecter à Railway → Database → Connect → mysqldump

# 2. Test migration en local avec des données similaires
php bin/console doctrine:migrations:migrate

# 3. Déploiement avec surveillance
flyctl deploy
flyctl logs --app="jobyfind-api" -f  # Surveiller en temps réel

# 4. En cas de problème, rollback :
flyctl deploy --dockerfile Dockerfile.prod
# Et potentiellement rollback migration si nécessaire
```

### Ajout de données fixtures/seeders

```bash
# 1. Créer des fixtures si nécessaire
php bin/console make:fixtures

# 2. Les charger localement pour test
php bin/console doctrine:fixtures:load

# 3. Pour la production, créer une commande custom ou utiliser l'endpoint d'admin
# Les fixtures ne sont généralement pas chargées en prod
```

---

## 🎨 3. Changements FRONTEND uniquement

### Modifications UI/UX (pas d'API)

```bash
# 1. Développement local
cd jobyfind-frontend
# Faire vos modifications React...

# 2. Test local
npm run dev
# Tester l'interface

# 3. Déploiement production
vercel --prod
# ✅ Déploiement automatique sur Vercel

# 4. Validation
curl -I https://jobyfind-frontend.vercel.app
# Vérifier que le site répond
```

### Nouvelles dépendances npm

```bash
# 1. Ajouter la dépendance
npm install nouvelle-dependance

# 2. Vérifier package-lock.json
git add package.json package-lock.json

# 3. Tester localement
npm run dev

# 4. Déployer
vercel --prod
```

### Modifications configuration Vite/Vercel

```bash
# 1. Modifier vite.config.js ou vercel.json
# 2. Test local
npm run build && npm run preview

# 3. Déploiement
vercel --prod

# 4. Si variables d'environnement nécessaires :
vercel env add NOUVELLE_VAR production
vercel --prod  # Redéployer après ajout
```

---

## 🔄 4. Changements FULL-STACK (Backend + Frontend)

### Nouvelles fonctionnalités avec API

```bash
# 1. BACKEND D'ABORD
cd jobyfind_backend
# Développer les nouveaux endpoints API
# Créer migrations si nécessaire
php bin/console make:migration
php bin/console doctrine:migrations:migrate

# Test local backend
symfony serve

# 2. DÉPLOYER BACKEND
flyctl deploy
# ✅ API disponible avec nouveaux endpoints

# 3. FRONTEND ENSUITE
cd ../jobyfind-frontend
# Modifier le frontend pour utiliser nouveaux endpoints
# Tester la communication avec la prod API

# Test local frontend
npm run dev

# 4. DÉPLOYER FRONTEND
vercel --prod
# ✅ Frontend mis à jour avec nouvelle API
```

### Modifications de schéma + interface

```bash
# 1. Migration de données d'abord
cd jobyfind_backend
php bin/console make:migration
flyctl deploy  # Déployer la migration

# 2. Adapter le frontend aux nouvelles données
cd ../jobyfind-frontend
# Modifier les composants React
vercel --prod  # Déployer le frontend adapté

# 3. Validation complète
# Tester tous les flux utilisateur sur la prod
```

---

## 🚨 5. ROLLBACK / RETOUR ARRIÈRE

### Rollback Frontend (Vercel)

```bash
# 1. Voir les déploiements précédents
vercel list

# 2. Promouvoir un ancien déploiement
vercel alias set https://jobyfind-frontend-[hash-ancien].vercel.app jobyfind-frontend.vercel.app

# 3. Ou redéployer une version précédente
git checkout [commit-precedent]
vercel --prod
git checkout main  # Revenir à main après
```

### Rollback Backend (Fly.io)

```bash
# 1. Voir les releases précédentes
flyctl releases --app="jobyfind-api"

# 2. Rollback à une version précédente
flyctl releases rollback [version-number] --app="jobyfind-api"

# 3. Ou redéployer un commit précédent
git checkout [commit-precedent]
flyctl deploy
git checkout main
```

### Rollback Base de données (🚨 DÉLICAT)

```bash
# ⚠️ Les rollbacks de DB sont complexes et risqués !

# 1. Si migration récente et pas de données perdues :
php bin/console doctrine:migrations:migrate prev

# 2. Si backup disponible :
# Restaurer depuis Railway dashboard → Database → Restore

# 3. ⚠️ EN CAS D'URGENCE uniquement :
# Recréer la DB depuis zéro (PERTE DE DONNÉES !)
php bin/console doctrine:schema:drop --force --full-database
php bin/console doctrine:schema:create
php bin/console doctrine:migrations:migrate
```

---

## 🛠️ 6. COMMANDES UTILES PRODUCTION

### Surveillance et monitoring

```bash
# 📊 LOGS ET STATUS
flyctl status --app="jobyfind-api"           # Status de l'app
flyctl logs --app="jobyfind-api"             # Logs récents
flyctl logs --app="jobyfind-api" -f          # Logs en temps réel
vercel logs [deployment-url]                 # Logs Vercel

# 🔍 DEBUGGING
flyctl ssh console --app="jobyfind-api"      # SSH dans le container
flyctl logs --app="jobyfind-api" | grep ERROR # Filtrer les erreurs

# 📈 MÉTRIQUES
flyctl info --app="jobyfind-api"             # Infos de l'app
flyctl scale show --app="jobyfind-api"       # Voir le scaling
vercel list                                  # Tous les déploiements Vercel
```

### Gestion des secrets et variables

```bash
# 🔐 SECRETS BACKEND (Fly.io)
flyctl secrets list --app="jobyfind-api"           # Lister les secrets
flyctl secrets set KEY="value" --app="jobyfind-api" # Ajouter/modifier
flyctl secrets unset KEY --app="jobyfind-api"      # Supprimer

# 🌍 VARIABLES FRONTEND (Vercel)
vercel env ls                                # Lister les variables
vercel env add VITE_KEY production           # Ajouter en production
vercel env rm VITE_KEY production            # Supprimer
```

### Tests de santé et validation

```bash
# 🩺 HEALTH CHECKS
curl https://jobyfind-api.fly.dev/api/health
curl -I https://jobyfind-frontend.vercel.app

# 🔐 TEST AUTHENTIFICATION
curl -X POST https://jobyfind-api.fly.dev/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"test123","role":"ROLE_SCHOOL"}'

curl -X POST https://jobyfind-api.fly.dev/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"test123"}'

# 🌐 TEST CORS
curl -X OPTIONS https://jobyfind-api.fly.dev/api/register \
  -H "Origin: https://jobyfind-frontend.vercel.app" \
  -H "Access-Control-Request-Method: POST"
```

### Maintenance et optimisation

```bash
# 🧹 NETTOYAGE
vercel remove [old-deployment-url]           # Supprimer anciens déploiements
flyctl apps list                             # Voir toutes vos apps Fly.io

# 📊 PERFORMANCE
flyctl metrics --app="jobyfind-api"          # Métriques de performance
vercel logs --follow                         # Monitorer les performances

# 🔄 REDÉMARRAGE
flyctl restart --app="jobyfind-api"          # Redémarrer l'app
```

---

## ✅ 7. CHECKLIST DE VALIDATION POST-DÉPLOIEMENT

### Après chaque déploiement Backend

-   [ ] `curl https://jobyfind-api.fly.dev/api/health` → Status 200
-   [ ] Vérifier les logs : `flyctl logs --app="jobyfind-api"` → Pas d'erreurs
-   [ ] Test endpoint clé : Inscription/Login fonctionne
-   [ ] Base de données : Connexion OK, migrations appliquées
-   [ ] CORS : Communication avec frontend OK

### Après chaque déploiement Frontend

-   [ ] `curl -I https://jobyfind-frontend.vercel.app` → Status 200
-   [ ] Interface charge correctement dans le navigateur
-   [ ] Console navigateur : Pas d'erreurs JavaScript
-   [ ] Communication API : Appels vers Fly.io fonctionnent
-   [ ] Test complet : Inscription + Login + Navigation

### Après changements Full-Stack

-   [ ] Backend testé et validé ✅
-   [ ] Frontend testé et validé ✅
-   [ ] Communication Backend ↔ Frontend ✅
-   [ ] Base de données cohérente ✅
-   [ ] Tests utilisateur bout-en-bout ✅
-   [ ] Performance satisfaisante ✅

---

## 🎯 RÉSUMÉ DES COMMANDES ESSENTIELLES

```bash
# 🚀 DÉPLOIEMENT RAPIDE
cd jobyfind_backend && flyctl deploy           # Backend
cd jobyfind-frontend && vercel --prod          # Frontend

# 🔍 MONITORING
flyctl logs --app="jobyfind-api" -f            # Logs backend temps réel
vercel logs                                    # Logs frontend

# 🩺 HEALTH CHECK
curl https://jobyfind-api.fly.dev/api/health   # API santé
curl -I https://jobyfind-frontend.vercel.app   # Frontend statut

# 🔄 ROLLBACK D'URGENCE
flyctl releases rollback [version]             # Backend
vercel alias set [old-url] [alias]             # Frontend

# 🔐 GESTION SECRETS
flyctl secrets set KEY="value"                 # Backend secrets
vercel env add VITE_KEY production              # Frontend env vars
```

**✅ Avec ces workflows, vous avez maintenant une méthode claire et systématique pour maintenir votre application Jobyfind en production !**
