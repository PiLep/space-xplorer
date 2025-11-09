@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'label' => '',
    'placeholder' => '',
    'value' => null,
    'wireModel' => null,
    'disabled' => false,
    'required' => false,
    'autofocus' => false,
    'variant' => 'classic', // 'classic' or 'terminal'
    'marginBottom' => 'mb-4', // mb-4, mb-6, etc.
    'helpText' => null, // For read-only fields
    'errorField' => null, // Field name for error checking (defaults to name)
])

@php
    $fieldId = $id ?? $name;
    $errorFieldName = $errorField ?? $name;
    $inputClasses = '';
    $labelClasses = '';
    
    if ($variant === 'terminal') {
        // Terminal style: border-b-2, font-mono, transparent background
        $inputClasses = 'w-full bg-transparent border-b-2 border-gray-300 dark:border-border-dark focus:border-space-primary dark:focus:border-space-primary text-gray-900 dark:text-white py-2 px-0 focus:outline-none font-mono text-sm transition-colors';
    } else {
        // Classic style: rounded border, shadow
        $inputClasses = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary';
        
        if ($disabled) {
            $inputClasses .= ' text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-surface-medium border-gray-300 dark:border-border-dark cursor-not-allowed';
        }
    }
    
    $labelClasses = 'block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2';
@endphp

<div class="{{ $marginBottom }}">
    @if($variant === 'terminal' && $label)
        <!-- Terminal prompt style -->
        <div class="text-sm text-gray-500 dark:text-gray-500 mb-2">
            <span class="text-space-primary dark:text-space-primary">SYSTEM@SPACE-XPLORER:~$</span> 
            <span class="text-space-secondary dark:text-space-secondary">{{ strtolower(str_replace(' ', '_', $label)) }}</span>
        </div>
    @elseif($label)
        <!-- Classic label style -->
        <label for="{{ $fieldId }}" class="{{ $labelClasses }}">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        @if($fieldId) id="{{ $fieldId }}" @endif
        @if($name) name="{{ $name }}" @endif
        @if($wireModel) wire:model="{{ $wireModel }}" @endif
        @if($value !== null) value="{{ $value }}" @endif
        @if($placeholder) placeholder="{{ $placeholder }}" @endif
        @if($disabled) disabled @endif
        @if($required) required aria-required="true" @endif
        @if($autofocus) autofocus @endif
        @error($errorFieldName)
            aria-invalid="true"
            aria-describedby="{{ $fieldId }}-error"
        @enderror
        class="{{ $inputClasses }} @error($errorFieldName) border-error dark:border-error @enderror"
        {{ $attributes }}
    >

    @if($helpText)
        <p class="text-gray-500 dark:text-gray-400 text-xs italic mt-1">{{ $helpText }}</p>
    @endif

    @error($errorFieldName)
        @if($variant === 'terminal')
            <div id="{{ $fieldId }}-error" class="mt-2 text-xs text-error dark:text-error font-semibold" role="alert">
                [ERROR] {{ $message }}
            </div>
        @else
            <p id="{{ $fieldId }}-error" class="text-error dark:text-error text-xs font-semibold mt-1" role="alert">{{ $message }}</p>
        @endif
    @enderror
</div>

