<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public $planet = null;

    public $user = null;

    public $loading = true;

    public $error = null;

    public function mount()
    {
        $this->loadUserAndPlanet();
    }

    public function loadUserAndPlanet()
    {
        try {
            $this->loading = true;
            $this->error = null;

            // Get current user from session
            $this->user = Auth::user();

            if (! $this->user) {
                $this->error = 'You must be logged in to view your dashboard.';
                $this->loading = false;

                return;
            }

            // Load user with home planet relationship
            $this->user->load('homePlanet');

            if (! $this->user->home_planet_id) {
                $this->error = 'No home planet found. Please contact support.';
                $this->loading = false;

                return;
            }

            // Get home planet
            $this->planet = $this->user->homePlanet;

            if (! $this->planet) {
                $this->error = 'Planet data could not be loaded.';
            }
        } catch (\Exception $e) {
            $this->error = 'Failed to load planet data: '.$e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
