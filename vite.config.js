import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/main.css',
                'resources/css/components/header.css', // Добавьте эту строку
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
