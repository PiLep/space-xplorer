@props([
    'show' => false,
    'title' => '',
    'maxWidth' => 'md', // sm, md, lg, xl
    'variant' => 'standard', // standard, confirmation, form
    'closeable' => true,
])

@php
    $maxWidthClasses = [
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
    ];
    
    $maxWidthClass = $maxWidthClasses[$maxWidth] ?? $maxWidthClasses['md'];
    
    // Si x-show est présent dans les attributs, on utilise Alpine.js pour le contrôle
    $useAlpine = $attributes->has('x-show');
    $shouldRender = $show || $useAlpine;
@endphp

@if($shouldRender)
<div class="fixed inset-0 bg-black bg-opacity-50 dark:bg-black/50 flex items-center justify-center z-50" 
     @if($closeable && !$attributes->has('x-show'))
     x-data="{ show: @js($show) }"
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @endif
     role="dialog" 
     aria-labelledby="modal-title-{{ md5($title) }}"
     aria-modal="true"
     {{ $attributes }}>
    <div class="bg-surface-dark dark:bg-surface-dark border border-border-dark dark:border-border-dark rounded-lg p-6 {{ $maxWidthClass }} w-full terminal-border-simple mx-4 {{ $variant === 'form' ? 'max-h-[90vh] overflow-y-auto' : '' }}"
         @if($closeable && !$attributes->has('x-show'))
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @endif>
        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 id="modal-title-{{ md5($title) }}" class="text-2xl font-bold text-white font-mono">
                @if($variant === 'confirmation')
                    CONFIRMATION
                @else
                    {{ strtoupper($title) }}
                @endif
            </h2>
            @if($closeable)
                <button @click="show = false" 
                        class="text-gray-400 hover:text-white transition-colors focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black rounded"
                        aria-label="Fermer">
                    <span class="font-mono text-2xl leading-none">×</span>
                </button>
            @endif
        </div>
        
        <!-- Content -->
        <div class="mb-6" id="modal-description-{{ md5($title) }}">
            {{ $slot }}
        </div>
        
        <!-- Footer / Actions -->
        @if(isset($footer))
            <div class="flex justify-end gap-4">
                {{ $footer }}
            </div>
        @elseif($variant === 'confirmation')
            <div class="flex justify-end gap-4">
                <button @click="show = false" 
                        class="px-4 py-2 rounded text-gray-400 hover:text-white transition-colors font-mono focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 focus:ring-offset-space-black">
                    > CANCEL
                </button>
                <button class="bg-error hover:bg-error-dark text-white font-bold px-4 py-2 rounded transition-colors font-mono focus:outline-none focus:ring-2 focus:ring-error focus:ring-offset-2 focus:ring-offset-space-black">
                    > CONFIRM
                </button>
            </div>
        @endif
    </div>
</div>
@endif

