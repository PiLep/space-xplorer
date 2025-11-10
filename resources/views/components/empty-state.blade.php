@props([
    'title' => 'No items found',
    'description' => null,
])

<div class="text-center py-12">
    @if(isset($icon))
        <div class="flex justify-center mb-4">
            {{ $icon }}
        </div>
    @endif
    
    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
        {{ $title }}
    </h3>
    
    @if($description)
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            {{ $description }}
        </p>
    @endif
    
    @if(isset($action))
        <div class="flex justify-center">
            {{ $action }}
        </div>
    @endif
</div>

