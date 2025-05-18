import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/filament/registry/theme.css',
                'resources/js/app.js',
                'resources/images/logo.png'
            ],
            refresh: true,
        }),
    ],
});
