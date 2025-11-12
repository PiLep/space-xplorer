<?php

namespace App\Livewire;

use App\Services\AuthService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
class LoginTerminal extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('sometimes|boolean')]
    public mixed $remember = false;

    protected $messages = [
        'email.required' => 'Email required.',
        'email.email' => 'Invalid email format.',
        'password.required' => 'Password required.',
    ];

    public string $status = '';

    public bool $terminalBooted = false;

    public int $bootStep = 0;

    public array $bootMessages = [];

    public function mount()
    {
        // Vérifier si l'animation a déjà été vue dans cette session
        $terminalBootSeen = session('terminal_boot_seen', false);

        if ($terminalBootSeen) {
            // Si déjà vue, passer directement au formulaire
            $this->terminalBooted = true;
            $this->bootStep = 0;
            $this->bootMessages = [];
        } else {
            // Première visite, démarrer l'animation
            $this->startTerminalBoot();
            // Marquer comme vue après le démarrage
            session(['terminal_boot_seen' => true]);
        }
    }

    public function startTerminalBoot()
    {
        $this->terminalBooted = false;
        $this->bootStep = 0;
        $this->bootMessages = [];
    }

    public function nextBootStep()
    {
        $steps = [
            '[INIT] Initializing authentication terminal...',
            '[OK] Terminal initialized',
            '[LOAD] Connecting to authentication server...',
            '[OK] Server connection established',
            '[LOAD] Loading authentication interface...',
            '[OK] Interface ready',
            '[READY] System ready for credentials',
        ];

        if ($this->bootStep < count($steps)) {
            $this->bootMessages[] = $steps[$this->bootStep];
            $this->bootStep++;

            // Boot complete, show login form
            if ($this->bootStep >= count($steps)) {
                $this->terminalBooted = true;
            }
        }
    }

    public function login(AuthService $authService)
    {
        $this->status = '[AUTHENTICATING] Connecting to authentication server...';
        $this->validate();

        try {
            $user = $authService->loginFromCredentials($this->email, $this->password, $this->remember);
            $this->status = '[SUCCESS] Authentication successful. Redirecting...';

            // Réinitialiser le flag pour que l'animation se joue sur le dashboard après login
            session(['terminal_boot_seen' => false]);

            // Small delay to show success message
            sleep(1);

            // Check if email is verified, redirect accordingly
            if (! $user->hasVerifiedEmail()) {
                return $this->redirect(route('email.verify'), navigate: true);
            }

            // Redirect to dashboard
            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            $this->status = '[ERROR] Validation failed.';
            foreach ($e->errors() as $field => $messages) {
                foreach ($messages as $message) {
                    $this->addError($field, $message);
                }
            }
        } catch (\Exception $e) {
            // Handle other errors
            $this->status = '[ERROR] Authentication failed.';
            $this->addError('email', $e->getMessage() ?: 'Invalid credentials. Access denied.');
        }
    }

    public function render()
    {
        return view('livewire.login-terminal');
    }
}
