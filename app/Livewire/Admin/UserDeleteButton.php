<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UserDeleteButton extends Component
{
    public User $user;

    public bool $showConfirmModal = false;

    public ?string $error = null;

    public function mount(User $user): void
    {
        $this->user = $user;
    }

    public function openConfirmModal(): void
    {
        $this->error = null;
        $this->showConfirmModal = true;
    }

    public function closeConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->error = null;
    }

    public function delete(): void
    {
        $currentUser = Auth::guard('admin')->user();

        // Prevent self-deletion
        if ($currentUser && $currentUser->id === $this->user->id) {
            $this->error = 'You cannot delete your own account.';
            $this->showConfirmModal = false;

            return;
        }

        $this->user->delete();

        $this->redirect(route('admin.users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.user-delete-button');
    }
}

