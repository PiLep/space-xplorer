<?php

namespace App\Livewire;

use App\Models\CodexEntry;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ContributeToCodex extends Component
{
    public CodexEntry $entry;

    #[Validate('required|string|min:10|max:5000')]
    public string $content = '';

    public ?string $error = null;

    public bool $success = false;

    public function mount(CodexEntry $entry): void
    {
        $this->entry = $entry;
    }

    /**
     * Submit contribution.
     */
    public function contribute(): void
    {
        $this->validate();
        $this->error = null;
        $this->success = false;

        try {
            $user = Auth::user();
            if (! $user) {
                $this->error = 'You must be logged in to contribute.';

                return;
            }

            // Create contribution
            $contribution = $this->entry->contributions()->create([
                'contributor_user_id' => $user->id,
                'content_type' => 'description',
                'content' => $this->content,
                'status' => 'pending', // Requires moderation
            ]);

            $this->success = true;
            $this->content = '';

            // Emit event to refresh parent component
            $this->dispatch('contribution-added', entryId: $this->entry->id);

            // Close modal after a short delay
            $this->dispatch('close-modal');
        } catch (\Exception $e) {
            $this->error = 'Failed to submit contribution: '.$e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.contribute-to-codex');
    }
}

