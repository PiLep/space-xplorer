# Branding - Favicons

## Vue d'Ensemble

Les favicons de Stellar sont conçus pour représenter l'identité visuelle de l'application dans les navigateurs, les écrans d'accueil mobiles, et les applications web progressives (PWA).

## Fichiers Disponibles

Tous les fichiers favicon sont disponibles dans le dossier `public/` :

| Fichier | Taille | Usage |
|---------|--------|-------|
| `favicon.ico` | 16x16, 32x32 | Favicon standard pour navigateurs |
| `favicon-16x16.png` | 16x16 | Favicon PNG pour navigateurs modernes |
| `favicon-32x32.png` | 32x32 | Favicon PNG pour navigateurs modernes |
| `apple-touch-icon.png` | 180x180 | Icône pour iOS (écran d'accueil) |
| `android-chrome-192x192.png` | 192x192 | Icône Android (PWA) |
| `android-chrome-512x512.png` | 512x512 | Icône Android haute résolution (PWA) |
| `site.webmanifest` | - | Manifeste pour PWA |

## Intégration dans le Layout

Les favicons sont automatiquement inclus dans le layout principal (`resources/views/layouts/app.blade.php`) :

```blade
<!-- Favicons -->
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<link rel="icon" href="{{ asset('favicon.ico') }}">
```

## Site Web Manifest

Le fichier `site.webmanifest` configure l'application comme Progressive Web App (PWA) :

```json
{
  "name": "Stellar - Explore the Universe",
  "short_name": "Stellar",
  "description": "Explore star systems, planets, and celestial objects",
  "icons": [
    {
      "src": "/android-chrome-192x192.png",
      "sizes": "192x192",
      "type": "image/png"
    },
    {
      "src": "/android-chrome-512x512.png",
      "sizes": "512x512",
      "type": "image/png"
    }
  ],
  "theme_color": "#0a0a0a",
  "background_color": "#0a0a0a",
  "display": "standalone",
  "start_url": "/",
  "orientation": "portrait-primary"
}
```

### Configuration

- **Theme Color** : `#0a0a0a` (noir profond, cohérent avec le design system)
- **Background Color** : `#0a0a0a` (noir profond)
- **Display** : `standalone` (application autonome)
- **Orientation** : `portrait-primary` (portrait par défaut)

## Emplacement des Fichiers

### Production

Les fichiers favicon doivent être placés dans le dossier `public/` pour être accessibles publiquement :

```
public/
├── favicon.ico
├── favicon-16x16.png
├── favicon-32x32.png
├── apple-touch-icon.png
├── android-chrome-192x192.png
├── android-chrome-512x512.png
└── site.webmanifest
```

### Documentation

Les fichiers source sont conservés dans `docs/design-system/assets/favicon/` pour référence et versioning.

## Support des Navigateurs

### Navigateurs Desktop

- **Chrome/Edge** : Utilise `favicon.ico` et les PNG selon la taille
- **Firefox** : Utilise `favicon.ico` et les PNG
- **Safari** : Utilise `favicon.ico` et `apple-touch-icon.png`

### Mobile

- **iOS** : Utilise `apple-touch-icon.png` (180x180)
- **Android** : Utilise les icônes définies dans `site.webmanifest`

### PWA

Les icônes Android sont utilisées pour les Progressive Web Apps installées sur Android.

## Design

Les favicons suivent l'identité visuelle de Stellar :
- Style rétro-futuriste cohérent avec le design system
- Couleurs du thème spatial (noir profond `#0a0a0a`)
- Représentation du logo "STELLAR" adaptée aux petites tailles

## Maintenance

Pour mettre à jour les favicons :

1. Générer les nouvelles images dans toutes les tailles requises
2. Placer les fichiers dans `docs/design-system/assets/favicon/`
3. Copier les fichiers vers `public/` :
   ```bash
   cp docs/design-system/assets/favicon/* public/
   ```
4. Mettre à jour `site.webmanifest` si nécessaire
5. Vérifier l'affichage dans différents navigateurs

## Vérification

Pour vérifier que les favicons sont correctement configurés :

1. Ouvrir l'application dans un navigateur
2. Vérifier l'onglet du navigateur (favicon visible)
3. Tester sur mobile (iOS et Android)
4. Vérifier le manifeste PWA dans les DevTools

---

**Référence** : Voir **[DESIGN-SYSTEM.md](./DESIGN-SYSTEM.md)** pour la vue d'ensemble du design system.

