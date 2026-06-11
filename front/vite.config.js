import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: ['css/app.css', 'js/app.js'],
            publicDirectory: '../public',
            buildDirectory: 'build',
            hotFile: '../public/hot',
            refresh: [
                '../resources/views/**',
                'js/**',
                'css/**',
            ],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'js'),
            ziggy: path.resolve(__dirname, '../vendor/tightenco/ziggy/dist/index.esm.js'),
        },
    },
    server: {
        host: '0.0.0.0',
        port: 5173,
        hmr: {
            host: 'localhost',
        },
    },
});
