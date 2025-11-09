<?php

namespace App\Livewire;

use App\Livewire\Concerns\MakesApiRequests;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Register extends Component
{
    use MakesApiRequests;

    public $name = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'password' => 'required|string|min:8|confirmed',
    ];

    protected $messages = [
        'name.required' => 'Le nom est requis.',
        'email.required' => 'L\'email est requis.',
        'email.email' => 'L\'email doit être valide.',
        'email.unique' => 'Cet email est déjà utilisé.',
        'password.required' => 'Le mot de passe est requis.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'password.confirmed' => 'Les mots de passe ne correspondent pas.',
    ];

    public function register()
    {
        $this->validate();

        try {
            $response = $this->apiPostPublic('/auth/register', [
                'name' => $this->name,
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
            ]);

            // Store token in session (already done in AuthController, but ensure it's there)
            if (isset($response['data']['token'])) {
                Session::put('sanctum_token', $response['data']['token']);
            }

            // Redirect to dashboard
            return $this->redirect(route('dashboard'), navigate: true);
        } catch (\Exception $e) {
            // Handle API errors
            $errorMessage = $e->getMessage();
            $errorData = json_decode($errorMessage, true);

            if (is_array($errorData) && ! empty($errorData)) {
                // Validation errors from API
                foreach ($errorData as $field => $messages) {
                    if (is_array($messages)) {
                        foreach ($messages as $message) {
                            $this->addError($field, $message);
                        }
                    } else {
                        $this->addError($field, $messages);
                    }
                }
            } else {
                // Other errors - show on email field or as general error
                if (str_contains($errorMessage, 'email') || str_contains($errorMessage, 'Email')) {
                    $this->addError('email', $errorMessage);
                } else {
                    $this->addError('email', $errorMessage ?: 'An error occurred during registration. Please try again.');
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.register');
    }
}
