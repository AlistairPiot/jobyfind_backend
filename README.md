# 🚀 Jobyfind Backend API

## 📋 Vue d'ensemble

**API REST Symfony** déployée en production sur **Fly.io** avec base de données **MySQL Railway**.

---

## 🌐 Production

### URLs et accès

-   **🔗 API Production** : `https://jobyfind-api.fly.dev`
-   **🩺 Health Check** : `https://jobyfind-api.fly.dev/api/health`
-   **🗄️ Base de données** : MySQL sur Railway (`yamabiko.proxy.rlwy.net:17520`)
-   **📊 Monitoring** : [Fly.io Dashboard](https://fly.io/apps/jobyfind-api/monitoring)

### Stack technique

```
PHP 8.2 + Symfony 6.4 + Apache
├── 🗄️ MySQL (Railway)
├── 🔐 JWT Authentication
├── 🌐 CORS configuré
├── 📦 Docker (Fly.io)
└── 🔧 OPCache activé
```

---

## 🚀 Déploiement

### Commande rapide

```bash
cd jobyfind_backend
flyctl deploy
```

### Première installation

```bash
# 1. Installer Fly.io CLI
curl -L https://fly.io/install.sh | sh
export PATH="$HOME/.fly/bin:$PATH"

# 2. Connexion et déploiement
flyctl auth login
flyctl deploy
```

### Variables d'environnement (Secrets)

```bash
# Lister les secrets actuels
flyctl secrets list --app="jobyfind-api"

# Ajouter/modifier un secret
flyctl secrets set KEY="value" --app="jobyfind-api"
```

**Secrets configurés :**

-   `DATABASE_URL` : Connexion MySQL Railway
-   `APP_SECRET` : Clé secrète Symfony
-   `JWT_PASSPHRASE` : Passphrase JWT (jobyfind)
-   `STRIPE_SECRET_KEY` : Clé Stripe mode test
-   `CORS_ALLOW_ORIGIN` : Frontend autorisé

---

## 🧪 Tests de production

### Health Check

```bash
curl https://jobyfind-api.fly.dev/api/health
# ✅ {"status":"ok","timestamp":"2025-01-XX","version":"1.0.0"}
```

### Test API Authentication

```bash
# Inscription
curl -X POST https://jobyfind-api.fly.dev/api/register \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"test123","role":"ROLE_SCHOOL"}'

# Connexion
curl -X POST https://jobyfind-api.fly.dev/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@test.com","password":"test123"}'
```

### Test CORS

```bash
curl -X OPTIONS https://jobyfind-api.fly.dev/api/register \
  -H "Origin: https://jobyfind-frontend.vercel.app" \
  -H "Access-Control-Request-Method: POST"
```

---

## 🛠️ Maintenance

### Commandes essentielles

```bash
# 📊 Status et logs
flyctl status --app="jobyfind-api"
flyctl logs --app="jobyfind-api" -f

# 🔄 Redémarrage
flyctl restart --app="jobyfind-api"

# 📈 Métriques
flyctl metrics --app="jobyfind-api"

# 🐛 Debug SSH
flyctl ssh console --app="jobyfind-api"
```

### Base de données

```bash
# Migrations automatiques au déploiement ✅
# Commandes manuelles si nécessaire :

flyctl ssh console --command="php bin/console doctrine:migrations:migrate"
flyctl ssh console --command="php bin/console doctrine:schema:validate"
```

### Rollback d'urgence

```bash
# Voir les versions précédentes
flyctl releases --app="jobyfind-api"

# Revenir à une version précédente
flyctl releases rollback [version-number] --app="jobyfind-api"
```

---

## 🏗️ Développement local

### Installation

```bash
# Dépendances
composer install

# Base de données locale (optionnel)
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Serveur de développement
symfony serve
# API disponible sur http://localhost:8000
```

### Variables d'environnement locales

```bash
# Créer .env.local
DATABASE_URL="mysql://user:password@127.0.0.1:3306/jobyfind"
JWT_PASSPHRASE="jobyfind"
STRIPE_SECRET_KEY="sk_test_..."
CORS_ALLOW_ORIGIN="http://localhost:3000"
```

---

## 📚 Documentation complète

### 📖 Guide de déploiement détaillé

**🔗 Voir : [`DEPLOYMENT_BACKEND_BDD.md`](DEPLOYMENT_BACKEND_BDD.md)**

Ce guide contient :

-   ✅ Processus de déploiement complet
-   🔧 Résolution des problèmes techniques
-   🗄️ Configuration base de données
-   🔄 Workflows de mise à jour
-   🚨 Procédures de rollback
-   🛠️ Commandes utiles pour la maintenance

---

## 🎯 Endpoints principaux

### Authentification

-   `POST /api/register` - Inscription utilisateur
-   `POST /api/login` - Connexion JWT
-   `POST /api/logout` - Déconnexion

### Utilisateurs

-   `GET /api/user/profile` - Profil utilisateur
-   `PUT /api/user/profile` - Mise à jour profil
-   `GET /api/users` - Liste utilisateurs (admin)

### Missions

-   `GET /api/missions` - Liste des missions
-   `POST /api/missions` - Créer mission
-   `GET /api/missions/{id}` - Détail mission
-   `PUT /api/missions/{id}` - Modifier mission
-   `DELETE /api/missions/{id}` - Supprimer mission

### Candidatures

-   `POST /api/missions/{id}/apply` - Postuler
-   `GET /api/applications` - Mes candidatures
-   `PUT /api/applications/{id}` - Modifier statut candidature

### Système

-   `GET /api/health` - Santé de l'API
-   `GET /api/skills` - Liste des compétences
-   `GET /api/badges` - Gestion des badges

---

## ✅ Checklist de déploiement

### Avant chaque déploiement

-   [ ] Tests locaux passent
-   [ ] Migrations testées localement
-   [ ] Nouvelles variables d'environnement ajoutées
-   [ ] Code committé et pushé

### Après chaque déploiement

-   [ ] Health check OK (`curl https://jobyfind-api.fly.dev/api/health`)
-   [ ] Logs sans erreurs (`flyctl logs --app="jobyfind-api"`)
-   [ ] Endpoints clés testés (register/login)
-   [ ] Communication avec frontend OK
-   [ ] Base de données cohérente

---

## 🚨 Support et monitoring

### En cas de problème

1. **Vérifier les logs** : `flyctl logs --app="jobyfind-api" -f`
2. **Status de l'app** : `flyctl status --app="jobyfind-api"`
3. **Health check** : `curl https://jobyfind-api.fly.dev/api/health`
4. **Rollback si urgent** : `flyctl releases rollback [version]`

### Monitoring automatique

-   ✅ Health checks toutes les 30s
-   ✅ Redémarrage automatique si crash
-   ✅ Métriques de performance disponibles
-   ✅ Logs centralisés sur Fly.io

---

**🎯 L'API Jobyfind est opérationnelle 24/7 en production !**

_Pour tous les détails techniques et workflows avancés, consultez la [documentation complète](DEPLOYMENT_BACKEND_BDD.md)._
