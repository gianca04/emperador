import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Permite que sea accesible desde la red
        port: 8181, // Puerto de Vite
        strictPort: true, // Usa siempre este puerto
        hmr: {
            host: '192.168.18.31' // Reemplaza con la IP de tu máquina
        }
    }
});
