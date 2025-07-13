import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';

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
    server: {
        host: '0.0.0.0',
        hmr: { host: 'portyard.local' },
        https: {
            key: fs.readFileSync(`./traefik/certs/portyard.local+2-key.pem`),
            cert: fs.readFileSync(`./traefik/certs/portyard.local+2.pem`),
        },
        port: 5173,
        strictPort: true,
    },
});
