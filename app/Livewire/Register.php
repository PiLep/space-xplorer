<?php

namespace App\Livewire;

use App\Livewire\Concerns\MakesApiRequests;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Register extends Component
{
    use MakesApiRequests;

    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $errors = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users|max:255',
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
            $errorData = json_decode($e->getMessage(), true);
            
            if (is_array($errorData)) {
                // Validation errors from API
                $this->errors = $errorData;
                foreach ($errorData as $field => $messages) {
                    $this->addError($field, is_array($messages) ? $messages[0] : $messages);
                }
            } else {
                // Other errors
                $this->addError('email', $e->getMessage());
            }
        }
    }

    public function render()
    {
        return view('livewire.register');
    }
}
