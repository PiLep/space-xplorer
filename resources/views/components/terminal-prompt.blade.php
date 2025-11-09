@props([
    'command' => '',
    'prompt' => 'SYSTEM@SPACE-XPLORER:~$',
])

<div class="text-sm text-space-primary dark:text-space-primary mb-2">
    <span class="text-gray-500 dark:text-gray-500">{{ $prompt }}</span>
    @if($command)
        <span class="text-space-primary dark:text-space-primary">{{ $command }}</span>
    @endif
</div>

