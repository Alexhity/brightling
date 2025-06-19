import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/main.css',
                'resources/css/admin.css',
                'resources/css/auth.css',
                'resources/css/components/header.css',
                'resources/css/components/footer.css', // Добавьте эту строку
                'resources/css/components/footerdash.css', // Добавьте эту строку
                'resources/css/components/headerdash.css', // Добавьте эту строку
                'resources/js/app.js'
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
