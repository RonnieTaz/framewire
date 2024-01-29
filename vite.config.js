import {defineConfig} from "vite";
import react from '@vitejs/plugin-react'
import { resolve } from 'path';

export default defineConfig({
    plugins: [
        react()
    ],
    base: 'build',
    build: {
        manifest: 'manifest.json',
        copyPublicDir: false,
        rollupOptions: {
            input: {
                'js/app.jsx': resolve(__dirname, 'resources/js/app.jsx'),
                'styles/base.css': resolve(__dirname, 'resources/styles/base.css')
            },
            output: {
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name.includes('jsx')) {
                        const substring = '.jsx'
                        return chunkInfo.name.slice(0, chunkInfo.name.indexOf(substring)) + '.js'
                    }
                    return  chunkInfo.name
                }
            }
        },
        outDir: resolve(__dirname, 'public/build')
    }
})
