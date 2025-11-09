<?php

namespace App\Livewire;

use App\Services\AuthService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class LoginTerminal extends Component
{
    public $email = '';

    public $password = '';

    public $status = '';

    protected $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    protected $messages = [
        'email.required' => 'Email required.',
        'email.email' => 'Invalid email format.',
        'password.required' => 'Password required.',
    ];

    public function login(AuthService $authService)
    {
        $this->status = '[AUTHENTICATING] Connecting to authentication server...';
        $this->validate();

        try {
            $authService->loginFromCredentials($this->email, $this->password);
            $this->status = '[SUCCESS] Authentication successful. Redirecting...';

            // Small delay to show success message
            sleep(1);

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
