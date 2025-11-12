<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    public $user = null;

    public $loading = true;

    public $error = null;

    public $showAvatarModal = false;

    public $availableAvatars = [];

    public $loadingAvatars = false;

    public $selectingAvatar = false;

    public $avatarMessage = null;

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
                'avatar_generating' => $authUser->avatar_generating ?? false,
                'home_planet_id' => $authUser->home_planet_id,
                'home_planet_name' => $authUser->homePlanet?->name ?? null,
                'matricule' => $authUser->matricule,
            ];
        } catch (\Exception $e) {
            $this->error = 'Failed to load user data: '.$e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function openAvatarModal()
    {
        $this->showAvatarModal = true;
        $this->loadingAvatars = true;
        $this->availableAvatars = [];
        $this->avatarMessage = null;

        try {
            $user = Auth::user();
            if (! $user) {
                $this->avatarMessage = '[ERROR] Authentication required for bio-profile regeneration.';
                $this->loadingAvatars = false;

                return;
            }

            // Get or create Sanctum token for API request
            $token = $user->createToken('avatar-change')->plainTextToken;

            try {
                // Fetch available avatars from API
                $response = Http::withToken($token)
                    ->get(url('/api/resources/avatars'));

                if ($response->successful()) {
                    $data = $response->json();
                    $this->availableAvatars = $data['data']['avatars'] ?? [];
                } else {
                    $this->avatarMessage = '[ERROR] Failed to load available bio-profiles. Please try again.';
                }
            } finally {
                // Clean up token after use
                $user->tokens()->where('name', 'avatar-change')->delete();
            }
        } catch (\Exception $e) {
            $this->avatarMessage = '[ERROR] Failed to load bio-profiles: '.$e->getMessage();
        } finally {
            $this->loadingAvatars = false;
        }
    }

    public function selectAvatar($resourceId)
    {
        $this->selectingAvatar = true;
        $this->avatarMessage = null;

        try {
            $user = Auth::user();
            if (! $user) {
                $this->avatarMessage = '[ERROR] Authentication required for bio-profile regeneration.';
                $this->selectingAvatar = false;

                return;
            }

            // Get or create Sanctum token for API request
            $token = $user->createToken('avatar-change')->plainTextToken;

            try {
                // Update avatar via API
                $response = Http::withToken($token)
                    ->put(url("/api/users/{$user->id}/avatar"), [
                        'resource_id' => $resourceId,
                    ]);

                if ($response->successful()) {
                    // Reload user from database to get updated avatar
                    $user->refresh();
                    $user->load('homePlanet');

                    // Reload user data in component
                    $this->loadUser();

                    $this->avatarMessage = '[OK] Bio-profile regeneration complete.';
                    $this->closeAvatarModal();
                } else {
                    $errorData = $response->json();
                    $this->avatarMessage = '[ERROR] '.($errorData['message'] ?? 'Bio-profile regeneration failed. Please try again.');
                }
            } finally {
                // Clean up token after use
                $user->tokens()->where('name', 'avatar-change')->delete();
            }
        } catch (\Exception $e) {
            $this->avatarMessage = '[ERROR] Bio-profile regeneration failed: '.$e->getMessage();
        } finally {
            $this->selectingAvatar = false;
        }
    }

    public function closeAvatarModal()
    {
        $this->showAvatarModal = false;
        $this->availableAvatars = [];
        $this->avatarMessage = null;
        $this->loadingAvatars = false;
        $this->selectingAvatar = false;
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
