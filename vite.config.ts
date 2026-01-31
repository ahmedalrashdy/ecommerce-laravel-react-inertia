import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import path from 'path';
export default defineConfig({
    // server: {
    //     host: '0.0.0.0',
    //     watch: {
    //         usePolling: true,
    //     },
    //     cors: true,
    //     hmr: {
    //         host: "192.168.8.169",
    //         clientPort: 5173,
    //     },
    //     port: 5173,
    // },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.tsx'],
            ssr: 'resources/js/ssr.tsx',
            refresh: true,
        }),
        react({
            babel: {
                plugins: ['babel-plugin-react-compiler'],
            },
        }),
        tailwindcss(),
        wayfinder({
            formVariants: true,
        }),
    ],
    esbuild: {
        jsx: 'automatic',
    },
    resolve: {
        alias: {
            '@styles': path.resolve(__dirname, 'resources/css'),
        }
    },
    build: {

        rollupOptions: {
            output: {
                manualChunks(id) {
                    if (id.includes('lucide-react')) return 'icons';
                },
            },
        },
    }
});
