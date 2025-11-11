<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Access Forbidden | Stellar</title>
    
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
                <span class="text-gray-500 dark:text-gray-500">SYSTEM@STELLAR:~$</span> <span class="text-space-primary dark:text-space-primary">request_access</span>
            </div>
            <div class="text-sm text-error dark:text-error mb-2">
                [DENIED] Access authorization failed
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-500">
                [INFO] Insufficient clearance level for this sector
            </div>
        </div>

        <!-- Terminal Interface -->
        <div class="bg-white dark:bg-surface-dark rounded-lg overflow-hidden terminal-border-simple scan-effect">
            <div class="p-8 text-center">
                <!-- Error Code -->
                <div class="mb-8">
                    <div class="text-8xl font-bold text-warning dark:text-warning text-glow-primary mb-4">
                        403
                    </div>
                    <div class="text-2xl text-gray-700 dark:text-gray-300 mb-2">
                        ACCESS DENIED
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-500">
                        You don't have permission to access this location
                    </div>
                </div>

                <!-- Terminal Output -->
                <div class="mb-8 text-left">
                    <div class="text-sm text-gray-500 dark:text-gray-500 mb-4 space-y-2">
                        <div>
                            <span class="text-space-primary dark:text-space-primary">SYSTEM@SPACE-XPLORER:~$</span> 
                            <span class="text-space-secondary dark:text-space-secondary">check_clearance</span>
                        </div>
                        <div class="text-error dark:text-error ml-8">
                            [DENIED] Access level insufficient
                        </div>
                        <div class="text-gray-500 dark:text-gray-500 ml-8">
                            [INFO] Security check results:
                        </div>
                        <div class="text-gray-500 dark:text-gray-500 ml-12">
                            • User clearance: [VERIFIED]
                        </div>
                        <div class="text-error dark:text-error ml-12">
                            • Required clearance: [HIGHER LEVEL REQUIRED]
                        </div>
                        <div class="text-gray-500 dark:text-gray-500 ml-12">
                            • Location: {{ request()->path() }}
                        </div>
                        <div class="text-warning dark:text-warning ml-8 mt-4">
                            [WARNING] Unauthorized access attempts are logged
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
                    @guest
                    <a 
                        href="{{ route('login') }}" 
                        class="bg-transparent border-2 border-space-secondary text-space-secondary dark:text-space-secondary hover:bg-space-secondary hover:text-space-black dark:hover:text-space-black font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-space-secondary focus:ring-offset-2 transition-colors font-mono text-sm"
                    >
                        > AUTHENTICATE
                    </a>
                    @endguest
                </div>

                <!-- Additional Info -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-border-dark">
                    <div class="text-xs text-gray-500 dark:text-gray-500">
                        [INFO] If you believe you should have access, please contact your administrator
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

