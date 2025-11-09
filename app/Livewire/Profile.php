<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    public $user = null;

    public $loading = true;

    public $error = null;

    public function mount()
    {
        $this->loadUser();
    }

    public function loadUser()
    {
        try {
            $this->loading = true;
            $this->error = null;

            $authUser = Auth::user();

            if (! $authUser) {
                $this->error = 'You must be logged in to view your profile.';
                $this->loading = false;

                return;
            }

            // Load user with home planet relationship
            $authUser->load('homePlanet');

            // Load user data directly from session
            $this->user = [
                'id' => $authUser->id,
                'name' => $authUser->name,
                'email' => $authUser->email,
                'avatar_url' => $authUser->avatar_url,
                'home_planet_id' => $authUser->home_planet_id,
                'home_planet_name' => $authUser->homePlanet?->name ?? null,
            ];
        } catch (\Exception $e) {
            $this->error = 'Failed to load user data: '.$e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
