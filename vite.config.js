import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ command, mode }) => {
    const isProduction = command === 'build' || mode === 'production';
    
    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js', 'resources/js/landing.js', 'resources/js/admin/universe-map.js', 'resources/js/admin/system-map.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
        // Server configuration only for development
        ...(!isProduction && {
            server: {
                host: '0.0.0.0',
                port: 5173,
                // Let Laravel Vite plugin handle HMR host automatically
                // It will detect the correct host based on APP_URL
                watch: {
                    usePolling: true,
                    interval: 300,
                },
            },
        }),
        // Production build optimizations
        ...(isProduction && {
            build: {
                rollupOptions: {
                    output: {
                        manualChunks: undefined,
                    },
                },
            },
        }),
    };
});
