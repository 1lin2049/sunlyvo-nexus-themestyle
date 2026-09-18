import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
    plugins: [react()],

    build: {
        outDir: 'dist',
        emptyOutDir: true,
        cssCodeSplit: true,
        sourcemap: false,
        minify: 'esbuild',
        target: 'es2020',

        rollupOptions: {
            input: {
                cart:     resolve(__dirname, 'src/entries/cart.tsx'),
                checkout: resolve(__dirname, 'src/entries/checkout.tsx'),
                account:  resolve(__dirname, 'src/entries/account.tsx'),
            },
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: 'shared/[name]-[hash].js',
                assetFileNames: '[name][extname]',
            },
        },
    },

    server: {
        port: 5173,
        proxy: {
            '/wp-json': {
                target: 'http://sunlyvo.com',
                changeOrigin: true,
            },
        },
    },
});