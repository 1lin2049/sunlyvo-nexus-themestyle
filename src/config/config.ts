import { defineConfig } from '@umijs/max';
import routes from './routes';

const publicPath = process.env.PUBLIC_PATH || '/';
const apiBase = process.env.API_BASE || '/wp-json/slv/v1';

export default defineConfig({
    title: 'SunLyvo Nexus',
    favicons: ['/favicon.ico'],

    antd: {
        theme: {
            token: {
                colorPrimary: '#0066ff',
                borderRadius: 8,
                fontSize: 14,
            },
        },
    },

    access: {},
    model: {},
    initialState: {},
    request: {},

    locale: {
        default: 'zh-CN',
        antd: true,
        baseNavigator: true,
        baseSeparator: '-',
    },

    layout: {
        title: 'SunLyvo Nexus',
        locale: true,
        layout: 'side',
        siderWidth: 220,
    },

    routes,

    npmClient: 'pnpm',
    utoopack: {},
    hash: true,
    history: { type: 'browser' },
    publicPath,
    base: publicPath,
    outputPath: 'dist',

    proxy: {
        '/wp-json': {
            target: 'http://sunlyvo.com',
            changeOrigin: true,
        },
    },

    theme: {
        'root-entry-name': 'variable',
    },

    define: {
        'process.env.API_BASE': apiBase,
    },
});