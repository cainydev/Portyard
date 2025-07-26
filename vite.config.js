import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';

const host = 'portyard.local';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/filament/registry/theme.css',
                'resources/js/app.js',
                'resources/images/portyard.png'
            ],
            refresh: true,
        }),
    ],
});
