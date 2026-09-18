import { defineConfig } from '@umijs/max';
import routes from './routes';

const publicPath = process.env.PUBLIC_PATH || '/';

export default defineConfig({
    title: 'SunLyvo Nexus',
    favicons: [`${publicPath}favicon.ico`],

    antd: {
        theme: {
            token: { colorPrimary: '#0066ff', borderRadius: 8, fontSize: 14 },
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

    // proxy 目标也从环境变量读
    proxy: process.env.WP_PROXY_TARGET ? {
        '/wp-json': {
            target: process.env.WP_PROXY_TARGET,
            changeOrigin: true,
        },
        '/wp-login.php': {
            target: process.env.WP_PROXY_TARGET,
            changeOrigin: true,
        },
    } : undefined,

    theme: { 'root-entry-name': 'variable' },

    // 注入 HTML 占位符，供 config.js 动态填值
    headScripts: [
        { src: `${publicPath}config.js` },
    ],
});