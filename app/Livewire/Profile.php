<?php

namespace App\Livewire;

use App\Livewire\Concerns\MakesApiRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    use MakesApiRequests;

    public $user = null;

    public $name = '';

    public $email = '';

    public $loading = true;

    public $saving = false;

    public $error = null;

    public $success = null;

    protected $rules = [
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|max:255',
    ];

    protected $messages = [
        'name.max' => 'Le nom ne peut pas dépasser 255 caractères.',
        'email.email' => 'L\'email doit être valide.',
        'email.max' => 'L\'email ne peut pas dépasser 255 caractères.',
    ];

    public function mount()
    {
        $this->loadUser();
    }

    public function loadUser()
    {
        try {
            $this->loading = true;
            $this->error = null;

            $response = $this->apiGet('/auth/user');
            $this->user = $response['data']['user'] ?? null;

            if ($this->user) {
                $this->name = $this->user['name'] ?? '';
                $this->email = $this->user['email'] ?? '';
            }
        } catch (\Exception $e) {
            $this->error = 'Failed to load user data: '.$e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function updateProfile()
    {
        $this->validate();
        $this->saving = true;
        $this->error = null;
        $this->success = null;

        try {
            $data = [];
            if ($this->name !== ($this->user['name'] ?? '')) {
                $data['name'] = $this->name;
            }
            if ($this->email !== ($this->user['email'] ?? '')) {
                $data['email'] = $this->email;
            }

            if (empty($data)) {
                $this->success = 'No changes to save.';
                $this->saving = false;

                return;
            }

            $response = $this->apiPut('/users/'.$this->user['id'], $data);

            // Reload user data
            $this->loadUser();
            $this->success = 'Profile updated successfully!';
        } catch (\Exception $e) {
            $errorData = json_decode($e->getMessage(), true);

            if (is_array($errorData)) {
                // Validation errors from API
                foreach ($errorData as $field => $messages) {
                    $this->addError($field, is_array($messages) ? $messages[0] : $messages);
                }
            } else {
                // Other errors
                $this->error = $e->getMessage() ?: 'Failed to update profile. Please try again.';
            }
        } finally {
            $this->saving = false;
        }
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
