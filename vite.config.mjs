import 'dotenv/config'
import { defineConfig, normalizePath } from 'vite'
import PluginCritical from 'rollup-plugin-critical'

const craftEnvIsDev = (String)(process.env.ENVIRONMENT ?? process.env.CRAFT_ENVIRONMENT).toLowerCase().includes('dev');

let http_auth = {};

if (process.env.HTTP_AUTHENTICATION_USER && process.env.HTTP_AUTHENTICATION_PASSWORD) {
    http_auth.user = process.env.HTTP_AUTHENTICATION_USER;
    http_auth.pass = process.env.HTTP_AUTHENTICATION_PASSWORD;
}

// https://vitejs.dev/config/
export default defineConfig((configEnv) => ({
    base: configEnv.command === 'serve' ? '' : '/dist/',
    publicDir: './src/public',
    build: {
        emptyOutDir: true,
        manifest: true,
        outDir: './web/dist/',
        rollupOptions: {
            input: {
                main: './src/js/main.js',
                styles: './src/css/styles.css'
            },
            output: {
                globals: {
                    Alpine: 'Alpine',
                }
            }
        }
    },
    assetsInclude: ['./src/inline-assets/**/*'],
    plugins: [
        PluginCritical({
            criticalUrl: process.env.PRIMARY_SITE_URL,
            criticalBase: './web/dist/criticalcss',
            criticalPages: [
                { uri: '/?criticalcss', template: 'homepage/_entry' },
            ],
            criticalConfig: {
                dimensions: [
                    { width: 320, height: 480 },
                    { width: 768, height: 1024 },
                    { width: 1280, height: 960 },
                    { width: 1920, height: 1080 },
                ],
                request: {
                    https: {
                        rejectUnauthorized: false
                    }
                }
            }
        }),
    ],
    server: {
        origin: 'http://localhost:5173',
        port: 5173,
        host: '0.0.0.0',
        strictPort: true,
        hmr: {
            port: 5173
        },
        cors: true
    },
}));