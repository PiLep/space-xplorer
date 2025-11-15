<div>
    <h3 class="mb-4 text-xl font-semibold text-[#1e3a5f] dark:text-white">Nommer cette planète</h3>

    @if ($success)
        <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800 dark:bg-green-900 dark:text-green-200">
            Planète nommée avec succès !
        </div>
    @elseif ($error)
        <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-800 dark:bg-red-900 dark:text-red-200">
            {{ $error }}
        </div>
    @endif

    <form wire:submit="namePlanet">
        <div class="mb-4">
            <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                Nom de la planète
            </label>
            <input
                type="text"
                id="name"
                wire:model="name"
                class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 shadow-sm focus:border-[#1e3a5f] focus:outline-none focus:ring-2 focus:ring-[#1e3a5f] dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                placeholder="Entrez un nom (3-50 caractères)"
            />
            @error('name')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-3">
            <button
                type="button"
                wire:click="$dispatch('close-modal')"
                class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600"
            >
                Annuler
            </button>
            <button
                type="submit"
                class="rounded-lg bg-[#1e3a5f] px-4 py-2 text-white hover:bg-[#0a0e27] transition-colors"
            >
                Nommer
            </button>
        </div>
    </form>
</div>

