@props(['type' => 'image', 'label' => null])

@php
    $defaultLabel = match($type) {
        'image' => 'SCANNING_IMAGE',
        'video' => 'SCANNING_VIDEO',
        'avatar' => 'SCANNING_AVATAR',
        default => 'SCANNING',
    };
    $displayLabel = $label ?? $defaultLabel;
@endphp

<div class="relative flex h-full w-full items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 overflow-hidden">
    <!-- Animated scan lines -->
    <div class="absolute inset-0 opacity-20">
        <div class="scan-line h-full w-full bg-gradient-to-b from-transparent via-green-400 to-transparent animate-scan"></div>
    </div>
    
    <!-- Grid pattern -->
    <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(rgba(0, 255, 136, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 255, 136, 0.1) 1px, transparent 1px); background-size: 20px 20px;"></div>
    
    <!-- Center content -->
    <div class="relative z-10 flex flex-col items-center justify-center gap-4 p-8 text-center">
        <!-- Animated scanning icon -->
        <div class="relative">
            <div class="h-16 w-16 rounded-full border-4 border-green-400 border-t-transparent animate-spin"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="h-8 w-8 rounded-full border-2 border-green-400 animate-pulse"></div>
            </div>
        </div>
        
        <!-- Text -->
        <div class="font-mono text-sm uppercase tracking-wider text-green-400">
            <div class="mb-1">{{ $displayLabel }}</div>
            <div class="text-xs text-gray-400">PLEASE_WAIT...</div>
        </div>
        
        <!-- Progress dots -->
        <div class="flex gap-1">
            <div class="h-1 w-1 rounded-full bg-green-400 animate-pulse" style="animation-delay: 0s;"></div>
            <div class="h-1 w-1 rounded-full bg-green-400 animate-pulse" style="animation-delay: 0.2s;"></div>
            <div class="h-1 w-1 rounded-full bg-green-400 animate-pulse" style="animation-delay: 0.4s;"></div>
        </div>
    </div>
    
    <!-- Corner brackets -->
    <div class="absolute top-2 left-2 h-4 w-4 border-l-2 border-t-2 border-green-400"></div>
    <div class="absolute top-2 right-2 h-4 w-4 border-r-2 border-t-2 border-green-400"></div>
    <div class="absolute bottom-2 left-2 h-4 w-4 border-l-2 border-b-2 border-green-400"></div>
    <div class="absolute bottom-2 right-2 h-4 w-4 border-r-2 border-b-2 border-green-400"></div>
</div>

@push('styles')
<style>
    @keyframes scan {
        0% {
            transform: translateY(-100%);
        }
        100% {
            transform: translateY(100%);
        }
    }
    
    .animate-scan {
        animation: scan 2s linear infinite;
    }
</style>
@endpush

