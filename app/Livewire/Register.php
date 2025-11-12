<?php

namespace App\Livewire;

use App\Services\AuthService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $terms_accepted = false;

    public $status = '';

    public $terminalBooted = false;

    public $bootStep = 0;

    public $bootMessages = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'terms_accepted' => 'required|accepted',
    ];

    protected $messages = [
        'name.required' => 'Name required.',
        'email.required' => 'Email required.',
        'email.email' => 'Invalid email format.',
        'email.unique' => 'This email is already registered.',
        'password.required' => 'Password required.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Passwords do not match.',
        'terms_accepted.required' => 'You must accept the corporate terms and conditions.',
        'terms_accepted.accepted' => 'You must accept the corporate terms and conditions.',
    ];

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
            '[INIT] Initializing registration terminal...',
            '[OK] Terminal initialized',
            '[LOAD] Connecting to user database...',
            '[OK] Database connection established',
            '[LOAD] Loading registration interface...',
            '[OK] Interface ready',
            '[READY] System ready for new user registration',
        ];

        if ($this->bootStep < count($steps)) {
            $this->bootMessages[] = $steps[$this->bootStep];
            $this->bootStep++;

            // Boot complete, show registration form
            if ($this->bootStep >= count($steps)) {
                $this->terminalBooted = true;
            }
        }
    }

    public function register(AuthService $authService)
    {
        $this->status = '[PROCESSING] Creating new user account...';
        $this->validate();

        try {
            $authService->registerFromArray([
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
            ]);

            $this->status = '[SUCCESS] Account created successfully. Redirecting to email verification...';

            // Réinitialiser le flag pour que l'animation se joue sur le dashboard après inscription
            session(['terminal_boot_seen' => false]);

            // Small delay to show success message
            sleep(1);

            // Redirect to email verification page
            return $this->redirect(route('email.verify'), navigate: true);
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
            $this->status = '[ERROR] Registration failed.';
            $this->addError('email', $e->getMessage() ?: 'An error occurred during registration. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.register');
    }
}
