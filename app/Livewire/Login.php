<?php

namespace App\Livewire;

use App\Livewire\Concerns\MakesApiRequests;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Login extends Component
{
    use MakesApiRequests;

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

    public function login()
    {
        $this->validate();

        try {
            $response = $this->apiPostPublic('/auth/login', [
                'email' => $this->email,
                'password' => $this->password,
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
                foreach ($errorData as $field => $messages) {
                    $this->addError($field, is_array($messages) ? $messages[0] : $messages);
                }
            } else {
                // Other errors (e.g., invalid credentials)
                $this->addError('email', $e->getMessage() ?: 'Invalid credentials. Please try again.');
            }
        }
    }

    public function render()
    {
        return view('livewire.login');
    }
}
