#!/bin/bash

echo "🚀 Script de déploiement Jobyfind Backend"

# Vérifier que Fly CLI est installé
if ! command -v flyctl &> /dev/null; then
    echo "❌ Fly CLI n'est pas installé. Installez-le avec: curl -L https://fly.io/install.sh | sh"
    exit 1
fi

# Vérifier si l'app existe déjà
if ! flyctl apps list | grep -q "jobyfind-api"; then
    echo "📦 Création de l'application Fly.io..."
    flyctl launch --name jobyfind-api --region cdg --no-deploy
fi

echo "🔧 Configuration des variables d'environnement..."

# Variables d'environnement essentielles
flyctl secrets set \
    APP_ENV=prod \
    APP_DEBUG=false \
    APP_SECRET="$(openssl rand -hex 32)" \
    JWT_PASSPHRASE="$(openssl rand -hex 16)"

echo "⚠️  Variables à configurer manuellement :"
echo "1. DATABASE_URL (depuis Railway)"
echo "2. STRIPE_SECRET_KEY (clé de production Stripe)"
echo "3. CORS_ALLOW_ORIGIN (domaine Vercel)"

echo ""
echo "Pour configurer ces variables, utilisez :"
echo "flyctl secrets set DATABASE_URL=\"postgresql://user:password@host:port/dbname\""
echo "flyctl secrets set STRIPE_SECRET_KEY=\"sk_live_...\""
echo "flyctl secrets set CORS_ALLOW_ORIGIN=\"https://votreapp.vercel.app\""

echo ""
echo "Une fois configuré, déployez avec :"
echo "flyctl deploy" 