<div id="screen-size-error" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999; background-color: rgb(17, 24, 39); min-height: 100vh; align-items: center; justify-content: center; padding: 1rem;" class="antialiased scanlines grain">
    <div class="max-w-4xl w-full mx-auto px-4 font-mono" style="position: relative; z-index: 1;">
        <!-- Terminal Header -->
        <div class="mb-6">
            <div class="text-sm text-space-primary dark:text-space-primary mb-2">
                <span class="text-gray-500 dark:text-gray-500">SYSTEM@STELLAR:~$</span> <span class="text-space-primary dark:text-space-primary">check_screen_size</span>
            </div>
            <div class="text-sm text-error dark:text-error mb-2">
                [ERROR] Terminal screen size not allowed
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-500">
                [INFO] Minimum screen width required: 1024px
            </div>
        </div>

        <!-- Terminal Interface -->
        <div class="bg-white dark:bg-surface-dark rounded-lg overflow-hidden terminal-border-simple scan-effect">
            <div class="p-8 text-center">
                <!-- Error Code -->
                <div class="mb-8">
                    <div class="text-8xl font-bold text-space-primary dark:text-space-primary text-glow-primary mb-4">
                        SCREEN
                    </div>
                    <div class="text-2xl text-gray-700 dark:text-gray-300 mb-2">
                        TERMINAL SIZE ERROR
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-500">
                        Your terminal window is too small to display the interface
                    </div>
                </div>

                <!-- Terminal Output -->
                <div class="mb-8 text-left">
                    <div class="text-sm text-gray-500 dark:text-gray-500 mb-4 space-y-2">
                        <div>
                            <span class="text-space-primary dark:text-space-primary">SYSTEM@SPACE-XPLORER:~$</span> 
                            <span class="text-space-secondary dark:text-space-secondary">check_display</span>
                        </div>
                        <div class="text-error dark:text-error">
                            [ERROR] Screen size not allowed: <span id="current-screen-size"></span>
                        </div>
                        <div class="text-gray-500 dark:text-gray-500">
                            [INFO] Required minimum dimensions:
                        </div>
                        <div class="text-gray-500 dark:text-gray-500">
                            • Width: 1024px minimum
                        </div>
                        <div class="text-gray-500 dark:text-gray-500">
                            • Height: 768px minimum
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <button 
                        onclick="location.reload()"
                        class="bg-space-primary hover:bg-space-primary-dark text-space-black font-bold py-3 px-6 rounded-lg focus:outline-none focus:ring-2 focus:ring-space-primary focus:ring-offset-2 transition-colors glow-primary hover:glow-primary font-mono text-sm"
                    >
                        > REFRESH_TERMINAL
                    </button>
                </div>

                <!-- Additional Info -->
                <div class="mt-8 pt-6 border-t border-gray-200 dark:border-border-dark">
                    <div class="text-xs text-gray-500 dark:text-gray-500">
                        [INFO] Please resize your browser window or use a larger screen
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const MIN_WIDTH = 1024;
        const MIN_HEIGHT = 768;
        
        function checkScreenSize() {
            const errorElement = document.getElementById('screen-size-error');
            const sizeDisplay = document.getElementById('current-screen-size');
            
            if (!errorElement) return;
            
            const width = window.innerWidth;
            const height = window.innerHeight;
            
            if (sizeDisplay) {
                sizeDisplay.textContent = `${width}x${height}px`;
            }
            
            if (width < MIN_WIDTH || height < MIN_HEIGHT) {
                errorElement.style.display = 'flex';
                errorElement.style.alignItems = 'center';
                errorElement.style.justifyContent = 'center';
                errorElement.style.position = 'fixed';
                errorElement.style.top = '0';
                errorElement.style.left = '0';
                errorElement.style.right = '0';
                errorElement.style.bottom = '0';
                errorElement.style.zIndex = '99999';
                errorElement.style.backgroundColor = 'rgb(17, 24, 39)';
                errorElement.style.minHeight = '100vh';
                errorElement.style.padding = '1rem';
                // Hide main content
                const mainContent = document.querySelector('main');
                if (mainContent) {
                    mainContent.style.display = 'none';
                }
                const terminalBar = document.querySelector('.fixed.bottom-0');
                if (terminalBar) {
                    terminalBar.style.display = 'none';
                }
                document.body.style.overflow = 'hidden';
            } else {
                errorElement.style.display = 'none';
                const mainContent = document.querySelector('main');
                if (mainContent) {
                    mainContent.style.display = '';
                }
                const terminalBar = document.querySelector('.fixed.bottom-0');
                if (terminalBar) {
                    terminalBar.style.display = '';
                }
                document.body.style.overflow = '';
            }
        }
        
        // Check on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', checkScreenSize);
        } else {
            checkScreenSize();
        }
        
        // Check on resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(checkScreenSize, 100);
        });
    })();
</script>

