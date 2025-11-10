@props(['type' => 'image', 'label' => null])

@php
    $defaultLabel = match ($type) {
        'image' => 'SCANNING_PLANETARY_SYSTEM',
        'video' => 'SCANNING_PLANETARY_SYSTEM',
        'avatar' => 'SCANNING_PILOT_PROFIL',
        default => 'SCANNING',
    };
    $displayLabel = $label ?? $defaultLabel;
@endphp

<div
    class="relative flex h-full w-full items-center justify-center overflow-hidden bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
    <!-- Animated scan lines -->
    <div class="absolute inset-0 opacity-20">
        <div class="scan-line animate-scan h-full w-full bg-gradient-to-b from-transparent via-green-400 to-transparent">
        </div>
    </div>

    <!-- Grid pattern -->
    <div
        class="absolute inset-0 opacity-10"
        style="background-image: linear-gradient(rgba(0, 255, 136, 0.1) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 255, 136, 0.1) 1px, transparent 1px); background-size: 20px 20px;"
    ></div>

    <!-- Center content -->
    @if ($type === 'avatar')
        <!-- Compact layout for avatar -->
        <div class="relative z-10 flex items-center justify-center p-2">
            <!-- Animated scanning icon - smaller for avatar -->
            <div class="relative">
                <div class="h-8 w-8 animate-spin rounded-full border-2 border-green-400 border-t-transparent"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="h-4 w-4 animate-pulse rounded-full border border-green-400"></div>
                </div>
            </div>
        </div>
    @else
        <!-- Full layout for image/video -->
        <div class="relative z-10 flex flex-col items-center justify-center gap-4 p-8 text-center">
            <!-- Animated scanning icon -->
            <div class="relative">
                <div class="h-16 w-16 animate-spin rounded-full border-4 border-green-400 border-t-transparent"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="h-8 w-8 animate-pulse rounded-full border-2 border-green-400"></div>
                </div>
            </div>

            <!-- Text -->
            <div class="font-mono text-sm uppercase tracking-wider">
                <div class="text-space-primary dark:text-space-primary mb-1 animate-pulse">{{ $displayLabel }}</div>
                <div class="text-xs text-gray-400">PLEASE_WAIT...</div>
            </div>

            <!-- Progress dots -->
            <div class="flex gap-1">
                <div
                    class="h-1 w-1 animate-pulse rounded-full bg-green-400"
                    style="animation-delay: 0s;"
                ></div>
                <div
                    class="h-1 w-1 animate-pulse rounded-full bg-green-400"
                    style="animation-delay: 0.2s;"
                ></div>
                <div
                    class="h-1 w-1 animate-pulse rounded-full bg-green-400"
                    style="animation-delay: 0.4s;"
                ></div>
            </div>
        </div>
    @endif


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
