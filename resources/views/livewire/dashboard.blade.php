<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">
            Welcome back, {{ $user->name ?? 'Explorer' }}!
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            Discover your home planet and begin your journey through the cosmos.
        </p>
    </div>

    @if ($loading)
        <div class="flex justify-center items-center py-12">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-space-primary"></div>
        </div>
    @elseif ($error)
        <div class="bg-red-100 dark:bg-error-dark border border-red-400 dark:border-error text-red-700 dark:text-error-light px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ $error }}</span>
        </div>
    @elseif ($planet)
        <!-- Planet Card -->
        <div class="bg-white dark:bg-surface-dark shadow-lg rounded-lg overflow-hidden mb-8 border border-gray-200 dark:border-border-dark">
            <!-- Planet Header -->
            <div class="bg-gradient-to-r from-space-secondary to-space-primary px-8 py-6">
                <h2 class="text-3xl font-bold text-space-black mb-2">{{ $planet->name }}</h2>
                <p class="text-space-black/80 text-lg capitalize">{{ $planet->type }}</p>
            </div>

            <!-- Planet Description -->
            <div class="px-8 py-6 border-b border-gray-200 dark:border-border-dark">
                <p class="text-gray-700 dark:text-white text-lg leading-relaxed">
                    {{ $planet->description }}
                </p>
            </div>

            <!-- Planet Characteristics -->
            <div class="px-8 py-6">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Planet Characteristics</h3>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Size -->
                    <div class="bg-gray-50 dark:bg-surface-medium rounded-lg p-4 border border-gray-200 dark:border-border-dark">
                        <div class="mb-2">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Size</h4>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $planet->size }}</p>
                    </div>

                    <!-- Temperature -->
                    <div class="bg-gray-50 dark:bg-surface-medium rounded-lg p-4 border border-gray-200 dark:border-border-dark">
                        <div class="mb-2">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Temperature</h4>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $planet->temperature }}</p>
                    </div>

                    <!-- Atmosphere -->
                    <div class="bg-gray-50 dark:bg-surface-medium rounded-lg p-4 border border-gray-200 dark:border-border-dark">
                        <div class="mb-2">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Atmosphere</h4>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $planet->atmosphere }}</p>
                    </div>

                    <!-- Terrain -->
                    <div class="bg-gray-50 dark:bg-surface-medium rounded-lg p-4 border border-gray-200 dark:border-border-dark">
                        <div class="mb-2">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Terrain</h4>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $planet->terrain }}</p>
                    </div>

                    <!-- Resources -->
                    <div class="bg-gray-50 dark:bg-surface-medium rounded-lg p-4 border border-gray-200 dark:border-border-dark">
                        <div class="mb-2">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Resources</h4>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $planet->resources }}</p>
                    </div>

                    <!-- Type -->
                    <div class="bg-gray-50 dark:bg-surface-medium rounded-lg p-4 border border-gray-200 dark:border-border-dark">
                        <div class="mb-2">
                            <h4 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide">Type</h4>
                        </div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">{{ $planet->type }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <button class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg transition-colors">
                Explore More Planets
            </button>
            <a href="{{ route('profile') }}" class="bg-gray-200 hover:bg-gray-300 dark:bg-surface-medium dark:hover:bg-surface-dark text-gray-900 dark:text-white font-bold py-3 px-6 rounded-lg transition-colors border border-gray-300 dark:border-border-dark">
                View Profile
            </a>
        </div>
    @endif
</div>
