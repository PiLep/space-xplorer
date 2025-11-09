# Image Generation Service - Guide d'utilisation

Le service `ImageGenerationService` permet de générer des images à partir de prompts textuels en utilisant différents fournisseurs d'IA (OpenAI DALL-E, Stability AI, etc.).

## Configuration

### Variables d'environnement

Ajoutez ces variables dans votre fichier `.env` :

```env
# Provider par défaut (openai ou stability)
IMAGE_GENERATION_PROVIDER=openai

# OpenAI DALL-E Configuration
OPENAI_API_KEY=sk-...
OPENAI_IMAGE_MODEL=dall-e-3
OPENAI_IMAGE_SIZE=1024x1024
OPENAI_IMAGE_QUALITY=standard
OPENAI_IMAGE_STYLE=vivid

# Stability AI Configuration
STABILITY_AI_API_KEY=sk-...
STABILITY_AI_ENGINE=stable-diffusion-xl-1024-v1-0
STABILITY_AI_WIDTH=1024
STABILITY_AI_HEIGHT=1024
STABILITY_AI_STEPS=30
STABILITY_AI_CFG_SCALE=7

# Paramètres globaux
IMAGE_GENERATION_TIMEOUT=60
IMAGE_GENERATION_RETRY_ATTEMPTS=3
IMAGE_GENERATION_RETRY_DELAY=2
```

## Utilisation de base

### Génération avec le provider par défaut

```php
use App\Services\ImageGenerationService;

$service = new ImageGenerationService();

try {
    $result = $service->generate('A beautiful alien planet with purple skies and floating islands');
    
    // OpenAI retourne une URL
    if (isset($result['url'])) {
        $imageUrl = $result['url'];
        // Utiliser l'URL pour afficher l'image
    }
    
    // Stability AI retourne du base64
    if (isset($result['base64'])) {
        $base64Image = $result['base64'];
        // Décoder et sauvegarder l'image
    }
} catch (\Exception $e) {
    // Gérer l'erreur
    logger()->error('Image generation failed', ['error' => $e->getMessage()]);
}
```

### Génération avec un provider spécifique

```php
// Utiliser OpenAI explicitement
$result = $service->generate('A space station orbiting a gas giant', 'openai');

// Utiliser Stability AI explicitement
$result = $service->generate('A space station orbiting a gas giant', 'stability');
```

### Vérifier les providers disponibles

```php
$availableProviders = $service->getAvailableProviders();
// Retourne: ['openai', 'stability'] (si les deux sont configurés)

if (in_array('openai', $availableProviders)) {
    // OpenAI est disponible
}
```

### Vérifier si un provider est configuré

```php
if ($service->isProviderConfigured('openai')) {
    // OpenAI est configuré et prêt à l'emploi
}
```

## Format de réponse

### OpenAI DALL-E

```php
[
    'url' => 'https://oaidalleapiprodscus.blob.core.windows.net/...',
    'provider' => 'openai',
    'revised_prompt' => 'A beautiful alien planet...', // Prompt révisé par DALL-E
]
```

### Stability AI

```php
[
    'base64' => 'iVBORw0KGgoAAAANSUhEUg...', // Image encodée en base64
    'provider' => 'stability',
    'finish_reason' => 'SUCCESS',
]
```

## Exemple d'intégration dans un contrôleur

```php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageGenerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function generate(Request $request, ImageGenerationService $service): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:1000',
            'provider' => 'nullable|string|in:openai,stability',
        ]);

        try {
            $result = $service->generate(
                $request->input('prompt'),
                $request->input('provider')
            );

            return response()->json([
                'data' => $result,
                'message' => 'Image generated successfully',
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate image',
                'error' => $e->getMessage(),
                'status' => 'error',
            ], 500);
        }
    }
}
```

## Gestion des erreurs

Le service lance des exceptions `\Exception` dans les cas suivants :

- Provider non configuré ou manquant
- Provider non supporté
- Erreur de connexion à l'API
- Réponse invalide de l'API
- Erreur de l'API (rate limit, invalid key, etc.)

Toujours utiliser un bloc `try-catch` lors de l'utilisation du service.

## Sauvegarde des images

### Pour OpenAI (URL)

```php
$result = $service->generate('A planet', 'openai');
$imageUrl = $result['url'];

// Télécharger et sauvegarder l'image
$imageContent = file_get_contents($imageUrl);
Storage::put('images/planet-' . uniqid() . '.png', $imageContent);
```

### Pour Stability AI (Base64)

```php
$result = $service->generate('A planet', 'stability');
$base64Image = $result['base64'];

// Décoder et sauvegarder
$imageContent = base64_decode($base64Image);
Storage::put('images/planet-' . uniqid() . '.png', $imageContent);
```

## Notes importantes

- **OpenAI DALL-E 3** : Génère toujours 1 image par requête
- **Stability AI** : Retourne les images en base64, nécessite un décodage
- **Rate Limits** : Respectez les limites de chaque provider
- **Coûts** : Chaque génération a un coût, surveillez votre utilisation
- **Timeout** : Par défaut 60 secondes, ajustable via config

