@php
use Illuminate\Support\Str;
@endphp

<div>
    <form method="POST" action="{{ route('admin.resources.store') }}" class="space-y-6">
        @csrf

        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Resource Type <span class="text-red-500">*</span>
            </label>
            <select 
                name="type" 
                id="type" 
                required 
                wire:model.live="type"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('type') border-red-500 @enderror">
                <option value="">Select a type</option>
                <option value="avatar_image">Avatar Image</option>
                <option value="planet_image">Planet Image</option>
                <option value="planet_video">Planet Video</option>
            </select>
            @error('type')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        @if(count($this->suggestions) > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Prompt Suggestions
                </label>
                <div class="space-y-2 mb-4">
                    @foreach($this->suggestions as $index => $suggestion)
                        <button
                            type="button"
                            wire:click="useSuggestion({{ $index }})"
                            class="w-full text-left p-3 bg-gray-50 dark:bg-surface-dark border border-gray-200 dark:border-border-dark rounded hover:bg-gray-50 dark:hover:bg-surface-medium transition-colors group"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm text-gray-700 dark:text-gray-300 flex-1 line-clamp-2 group-hover:text-gray-900 dark:group-hover:text-white">
                                    {{ Str::limit($suggestion, 150) }}...
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">Click to use</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <div>
            <label for="prompt" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Prompt <span class="text-red-500">*</span>
            </label>
            <textarea
                name="prompt"
                id="prompt"
                rows="6"
                required
                minlength="10"
                maxlength="2000"
                wire:model="prompt"
                placeholder="Enter a detailed prompt for AI generation or select a suggestion above..."
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('prompt') border-red-500 @enderror"
            ></textarea>
            @error('prompt')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Minimum 10 characters, maximum 2000 characters. 
                @if($type)
                    <span class="text-space-primary">Select a suggestion above to get started quickly.</span>
                @endif
            </p>
        </div>

        <div>
            <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Tags (comma-separated)
            </label>
            <input
                type="text"
                name="tags"
                id="tags"
                placeholder="tag1, tag2, tag3"
                wire:model="tags"
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('tags') border-red-500 @enderror"
            >
            @error('tags')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Separate tags with commas. Each tag will be trimmed and stored separately.
                @if(in_array($type, ['planet_image', 'planet_video', 'avatar_image']) && !empty($autoExtractedTags))
                    <span class="block mt-1 text-space-primary font-medium">
                        ✨ Auto-extracted tags: {{ $autoExtractedTags }}
                    </span>
                @elseif(in_array($type, ['planet_image', 'planet_video']))
                    <span class="block mt-1 text-gray-400 dark:text-gray-500 italic">
                        Tags will be automatically extracted from your prompt when you type it.
                    </span>
                @elseif($type === 'avatar_image')
                    <span class="block mt-1 text-gray-400 dark:text-gray-500 italic">
                        Tags (man/woman) will be automatically extracted from names in your prompt.
                    </span>
                @endif
            </p>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Description (optional)
            </label>
            <textarea
                name="description"
                id="description"
                rows="3"
                maxlength="1000"
                wire:model="description"
                placeholder="Optional description for this resource..."
                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary @error('description') border-red-500 @enderror"
            ></textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end gap-4">
            <x-button href="{{ route('admin.resources.index') }}" variant="ghost" size="sm">
                Cancel
            </x-button>
            <x-button type="submit" variant="primary" size="sm">
                Generate Resource
            </x-button>
        </div>
    </form>
</div>
