@props([
    'name' => '',
    'id' => null,
    'label' => null,
    'required' => false,
    'value' => null,
    'options' => [], // Array of ['value' => '', 'label' => ''] or simple array
    'placeholder' => null,
    'help' => null,
    'variant' => 'classic', // classic, terminal
])

@php
    $id = $id ?? $name;
    $value = old($name, $value ?? '');
    
    $baseInputClasses = 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 dark:text-white dark:bg-surface-medium dark:border-border-dark leading-tight focus:outline-none focus:ring-2 focus:ring-space-primary focus:border-space-primary';
    $errorInputClasses = 'border-red-500 dark:border-red-500';
    $labelClasses = 'block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2';
    $helpClasses = 'mt-1 text-sm text-gray-500 dark:text-gray-400';
    $errorClasses = 'mt-1 text-sm text-red-600 dark:text-red-400';
    
    $hasError = $errors->has($name);
    $inputClasses = $baseInputClasses . ($hasError ? ' ' . $errorInputClasses : '');
    
    if ($variant === 'terminal') {
        $inputClasses .= ' font-mono';
    }
@endphp

<div>
    @if($label)
        <label for="{{ $id }}" class="{{ $labelClasses }}">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select
        name="{{ $name }}"
        id="{{ $id }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => $inputClasses]) }}
    >
        @if($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @foreach($options as $option)
            @if(is_array($option))
                <option value="{{ $option['value'] }}" {{ $value == $option['value'] ? 'selected' : '' }}>
                    {{ $option['label'] }}
                </option>
            @else
                <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>
                    {{ $option }}
                </option>
            @endif
        @endforeach
        
        {{ $slot }}
    </select>
    
    @if($hasError)
        <p class="{{ $errorClasses }}">
            {{ $errors->first($name) }}
        </p>
    @endif
    
    @if($help && !$hasError)
        <p class="{{ $helpClasses }}">
            {{ $help }}
        </p>
    @endif
</div>




