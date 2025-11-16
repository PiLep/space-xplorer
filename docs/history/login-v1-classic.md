# Login v1 - Interface Classique

**Date d'archivage** : 2024  
**Statut** : 📦 Archivé - Remplacé par Login v2 (Terminal)

## Description

Version originale de la page de connexion avec une interface de formulaire classique, remplacée par la version terminal pour une meilleure intégration avec le thème spatial du projet.

## Fichiers archivés

### Composant Livewire
- **Fichier** : `app/Livewire/Login.php`
- **Route** : `/login` (anciennement)
- **Vue** : `resources/views/livewire/login.blade.php`

### Caractéristiques

#### Interface Classique
- Design de formulaire traditionnel
- Labels et champs de formulaire standards
- Messages d'erreur simples sous les champs
- Bouton de soumission avec état de chargement
- Lien vers l'inscription en bas du formulaire

#### Fonctionnalités
- Validation des champs email et password
- Messages d'erreur en français
- Redirection immédiate vers le dashboard après connexion
- Support du mode sombre

#### Code source

**Composant Livewire** (`app/Livewire/Login.php`) :
```php
<?php

namespace App\Livewire;

use App\Services\AuthService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Login extends Component
{
    public $email = '';
    public $password = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    protected $messages = [
        'email.required' => 'L\'email est requis.',
        'email.email' => 'L\'email doit être valide.',
        'password.required' => 'Le mot de passe est requis.',
    ];

    public function login(AuthService $authService)
    {
        $this->validate();

        try {
            $authService->loginFromCredentials($this->email, $this->password);

            // Redirect to dashboard
            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (\Exception $e) {
            // Handle other errors
            $this->addError('email', $e->getMessage() ?: 'Invalid credentials. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
```

**Vue Blade** (`resources/views/livewire/login.blade.php`) :
```blade
<div class="max-w-md mx-auto mt-8">
    <div class="bg-white dark:bg-surface-dark shadow-md rounded-lg px-8 pt-6 pb-8 mb-4 border border-gray-200 dark:border-border-dark scan-effect">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white dark:text-glow-subtle mb-6 text-center">
            Sign In
        </h2>

        <form wire:submit="login">
            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Email
                </label>
                <input
                    type="email"
                    id="email"
                    wire:model="email"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('email') border-error dark:border-error @enderror"
                    placeholder="Enter your email"
                    autofocus
                >
                @error('email')
                    <p class="text-error text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label for="password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    wire:model="password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('password') border-error dark:border-error @enderror"
                    placeholder="Enter your password"
                >
                @error('password')
                    <p class="text-error text-xs italic mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-between">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-2 px-4 rounded focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 disabled:opacity-50 w-full transition-colors glow-primary hover:glow-primary"
                >
                    <span wire:loading.remove wire:target="login">Sign In</span>
                    <span wire:loading wire:target="login">Signing in...</span>
                </button>
            </div>

            <!-- Register Link -->
            <div class="mt-4 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-space-secondary hover:text-space-secondary-light dark:text-space-secondary dark:hover:text-space-secondary-light font-bold">
                        Register
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
```

## Raison du remplacement

Le login v1 a été remplacé par le login v2 (terminal) pour :
- Mieux s'intégrer avec le thème spatial du projet
- Offrir une expérience utilisateur plus immersive
- Aligner l'interface avec l'identité visuelle du projet Stellar
- Améliorer la cohérence du design system

## Notes

- Cette implémentation utilisait le même service `AuthService` que la version terminal
- Les messages d'erreur étaient en français dans cette version
- Le design suivait le design system standard du projet
- Support complet du mode sombre

## Conservation

Cette documentation est conservée pour référence historique et peut servir de base pour :
- Comprendre l'évolution de l'interface de connexion
- Réutiliser des patterns de formulaire classique si nécessaire
- Référence pour d'autres formulaires du projet

