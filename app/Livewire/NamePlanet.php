<?php

namespace App\Livewire;

use App\Models\CodexEntry;
use App\Services\CodexService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NamePlanet extends Component
{
    public CodexEntry $entry;

    #[Validate('required|string|min:3|max:50')]
    public string $name = '';

    public ?string $error = null;

    public bool $success = false;

    public function mount(CodexEntry $entry): void
    {
        $this->entry = $entry;
    }

    /**
     * Name the planet.
     */
    public function namePlanet(): void
    {
        $this->validate();
        $this->error = null;
        $this->success = false;

        try {
            $user = Auth::user();
            if (! $user) {
                $this->error = 'You must be logged in to name a planet.';

                return;
            }

            $codexService = app(CodexService::class);

            // Validate name using service (includes uniqueness and forbidden words)
            $codexService->validateName($this->name);

            // Name the planet
            $this->entry = $codexService->namePlanet($this->entry, $user, $this->name);

            $this->success = true;
            $this->name = '';

            // Emit event to refresh parent component
            $this->dispatch('planet-named', entryId: $this->entry->id);

            // Close modal after a short delay
            $this->dispatch('close-modal');
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->error = implode(' ', array_map(fn ($messages) => implode(' ', $messages), $e->errors()));
        } catch (\Exception $e) {
            $this->error = 'Failed to name planet: '.$e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.name-planet');
    }
}

