# Configuration MinIO pour le développement local

MinIO est un service S3-compatible inclus dans Laravel Sail pour le développement local.

## Démarrage

1. **Démarrer les services Sail** (MinIO sera démarré automatiquement) :
   ```bash
   ./vendor/bin/sail up -d
   ```

2. **Accéder à la console MinIO** :
   - URL : http://localhost:9001
   - Username : `minioadmin` (par défaut)
   - Password : `minioadmin` (par défaut)

## Configuration du bucket

1. **Se connecter à la console MinIO** (http://localhost:9001)

2. **Créer un bucket** :
   - Cliquez sur "Create Bucket"
   - Nom du bucket : `stellar-game` (ou celui configuré dans `.env`)
   - Région : `us-east-1` (ou celle configurée)
   - Cochez "Make this bucket public" si vous voulez que les images soient publiques

3. **Configurer les credentials** (optionnel) :
   - Allez dans "Identity" > "Access Keys"
   - Créez un nouvel access key si vous voulez utiliser des credentials différents de root

## Configuration Laravel

Ajoutez ces variables dans votre `.env` pour le développement local :

```env
# MinIO Configuration (développement local)
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=minioadmin
AWS_SECRET_ACCESS_KEY=minioadmin
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=stellar-game
AWS_ENDPOINT=http://minio:9000
AWS_USE_PATH_STYLE_ENDPOINT=true
AWS_URL=http://localhost:9000/stellar-game

# Image Generation Storage
IMAGE_GENERATION_STORAGE_DISK=s3
IMAGE_GENERATION_STORAGE_PATH=images/generated
IMAGE_GENERATION_STORAGE_VISIBILITY=public
```

**Important** : `AWS_URL` doit être configuré pour que Laravel génère les bonnes URLs. 
- Pour MinIO local : `http://localhost:9000/{bucket-name}`
- Pour S3 production : Laisser vide (Laravel construit automatiquement l'URL S3)

**Note** : L'endpoint `http://minio:9000` utilise le nom du service Docker. Pour accéder depuis votre machine locale, utilisez `http://localhost:9000`.

## URLs

- **API MinIO** : http://localhost:9000
- **Console MinIO** : http://localhost:9001
- **Images générées** : http://localhost:9000/stellar-game/images/generated/...

## Production

En production, utilisez les vraies credentials AWS S3 dans votre `.env` :

```env
AWS_ACCESS_KEY_ID=your-real-access-key
AWS_SECRET_ACCESS_KEY=your-real-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-production-bucket
# Ne pas définir AWS_ENDPOINT en production (utilise AWS par défaut)
```

