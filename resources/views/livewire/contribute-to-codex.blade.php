<div>
    <h3 class="mb-4 font-sans text-xl font-semibold text-space-primary text-glow-subtle dark:text-white">[ACTION] Soumettre un rapport</h3>

    @if ($success)
        <x-alert type="success" message="[SUCCESS] Contribution soumise avec succès ! Elle sera examinée avant publication." class="mb-4" />
    @elseif ($error)
        <x-alert type="error" :message="$error" class="mb-4" />
    @endif

    <form wire:submit="contribute">
        <div class="mb-4">
            <label for="content" class="mb-2 block font-mono text-sm font-medium text-gray-300">
                [CONTENT] Contenu du rapport
            </label>
            <textarea
                id="content"
                wire:model="content"
                rows="6"
                class="font-mono w-full rounded-lg border border-border-dark bg-surface-dark px-4 py-3 text-white placeholder-gray-500 shadow-sm transition-all focus:border-space-secondary focus:outline-none focus:ring-2 focus:ring-space-secondary focus:ring-opacity-50"
                placeholder="[INPUT] Ajoutez des informations supplémentaires (10-5000 caractères)"
            ></textarea>
            @error('content')
                <p class="mt-1 font-mono text-sm text-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3">
            <button
                type="button"
                wire:click="$dispatch('close-modal')"
                class="font-mono rounded-lg border border-border-dark bg-surface-dark px-4 py-2 text-gray-300 hover:bg-surface-medium hover:text-white transition-colors"
            >
                [CANCEL] Annuler
            </button>
            <button
                type="submit"
                class="font-mono rounded-lg bg-space-secondary px-4 py-2 font-semibold text-space-black hover:bg-space-secondary-dark transition-colors glow-secondary"
            >
                [SUBMIT] Soumettre
            </button>
        </div>
    </form>
</div>

