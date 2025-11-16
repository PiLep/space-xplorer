<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Server Error | Stellar</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&family=Share+Tech+Mono&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 dark:bg-space-black antialiased scanlines grain min-h-screen flex items-center justify-center">
    <div class="max-w-4xl mx-auto px-4 font-mono">
        <!-- Terminal Header -->
        <div class="mb-6">
            <div class="text-sm text-space-primary dark:text-space-primary mb-2">
                <span class="text-gray-500 dark:text-gray-500">SYSTEM@STELLAR:~$</span> <span class="text-space-primary dark:text-space-primary">system_status</span>
            </div>
            <div class="text-sm text-error dark:text-error mb-2">
                [CRITICAL] System malfunction detected
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-500">
                [WARNING] Core systems experiencing unexpected errors
            </div>
        </div>

        <!-- Terminal Interface -->
        <div class="bg-white dark:bg-surface-dark rounded-lg overflow-hidden terminal-border-simple scan-effect">
            <div class="p-8 text-center">
                <!-- Error Code -->
                <div class="mb-8">
                    <div class="text-8xl font-bold text-error dark:text-error text-glow-primary mb-4 pulse-glow">
                        500
                    </div>
                    <div class="text-2xl text-gray-700 dark:text-gray-300 mb-2">
                        SYSTEM ERROR
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-500">
                        An internal error has occurred in the navigation system
                    </div>
                </div>

                <!-- Terminal Output -->
                <div class="mb-8 text-left">
                    <div class="text-sm text-gray-500 dark:text-gray-500 mb-4 space-y-2">
                        <div>
                            <span class="text-space-primary dark:text-space-primary">SYSTEM@STELLAR-GAME:~$</span> 
                            <span class="text-space-secondary dark:text-space-secondary">diagnose_system</span>
                        </div>
                        <div class="text-error dark:text-error ml-8">
                            [CRITICAL] Internal server error detected
                        </div>
                        <div class="text-gray-500 dark:text-gray-500 ml-8">
                            [INFO] System diagnostics:
                        </div>
                        <div class="text-gray-500 dark:text-gray-500 ml-12">
                            • Core systems: [ERROR]
                        </div>
                        <div class="text-gray-500 dark:text-gray-500 ml-12">
                            • Navigation module: [OFFLINE]
                        </div>
                        <div class="text-gray-500 dark:text-gray-500 ml-12">
                            • Database connection: [CHECKING...]
                        </div>
                        <div class="text-warning dark:text-warning ml-8 mt-4">
                            [WARNING] Mission control has been notified
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a 
                        href="{{ route('home') }}" 
                        class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 transition-colors glow-primary hover:glow-primary font-mono text-sm"
                    >
                        > RETURN_TO_HOME
                    </a>
                    <button 
                        onclick="window.location.reload()" 
                        class="bg-transparent border-2 border-space-secondary text-space-secondary dark:text-space-secondary hover:bg-space-secondary hover:text-space-black dark:hover:text-space-black font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-space-secondary focus:ring-offset-2 transition-colors font-mono text-sm"
                    >
                        > RETRY_CONNECTION
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-border-dark">
                    <div class="text-xs text-gray-500 dark:text-gray-500">
                        [INFO] If the problem persists, please contact mission control with error code: 500
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

