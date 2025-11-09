<?php

namespace App\Livewire;

use App\Livewire\Concerns\MakesApiRequests;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    use MakesApiRequests;

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

            // Get current user
            $userResponse = $this->apiGet('/auth/user');
            $this->user = $userResponse['data']['user'] ?? null;

            if (! $this->user || ! $this->user['home_planet_id']) {
                $this->error = 'No home planet found. Please contact support.';
                $this->loading = false;
                return;
            }

            // Get home planet
            $planetResponse = $this->apiGet('/users/'.$this->user['id'].'/home-planet');
            $this->planet = $planetResponse['data']['planet'] ?? null;

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
