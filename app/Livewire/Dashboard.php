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

    public $terminalBooted = false;

    public $bootStep = 0;

    public $bootMessages = [];

    public function mount()
    {
        $this->startTerminalBoot();
    }

    public function startTerminalBoot()
    {
        $this->loading = true;
        $this->terminalBooted = false;
        $this->bootStep = 0;
        $this->bootMessages = [];
    }

    public function nextBootStep()
    {
        $steps = [
            '[INIT] Initializing terminal interface...',
            '[OK] Terminal initialized',
            '[LOAD] Connecting to database...',
            '[OK] Database connection established',
            '[LOAD] Retrieving user session...',
            '[OK] Session loaded',
            '[LOAD] Accessing planetary database...',
            '[OK] Planetary data retrieved',
            '[READY] System ready',
        ];

        if ($this->bootStep < count($steps)) {
            $this->bootMessages[] = $steps[$this->bootStep];
            $this->bootStep++;

            // Boot complete, load actual data
            if ($this->bootStep >= count($steps)) {
                $this->loadUserAndPlanet();
            }
        }
    }

    public function loadUserAndPlanet()
    {
        try {
            $this->error = null;

            // Get current user from session
            $this->user = Auth::user();

            if (! $this->user) {
                $this->error = '[ERROR] You must be logged in to view your dashboard.';
                $this->loading = false;
                $this->terminalBooted = true;

                return;
            }

            // Load user with home planet relationship
            $this->user->load('homePlanet');

            if (! $this->user->home_planet_id) {
                $this->error = '[ERROR] No home planet found. Please contact support.';
                $this->loading = false;
                $this->terminalBooted = true;

                return;
            }

            // Get home planet
            $this->planet = $this->user->homePlanet;

            if (! $this->planet) {
                $this->error = '[ERROR] Planet data could not be loaded.';
            }
        } catch (\Exception $e) {
            $this->error = '[ERROR] Failed to load planet data: '.$e->getMessage();
        } finally {
            $this->loading = false;
            $this->terminalBooted = true;
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
