import { defineConfig } from '@umijs/max';
import routes from './config/routes';

const isProd = process.env.NODE_ENV === 'production';
const publicPath = isProd ? '/app/' : '/';

export default defineConfig({
    title: 'SunLyvo Nexus',
    publicPath,
    base: publicPath,
    outputPath: 'dist',
    hash: true,
    history: { type: 'browser' },
    routes,
    npmClient: 'pnpm',
    antd: {
        theme: {
            token: { colorPrimary: '#0066ff', borderRadius: 8, fontSize: 14 },
        },
    },
    access: {},
    model: {},
    initialState: {},
    request: {},
    locale: { default: 'zh-CN', antd: true, baseNavigator: true, baseSeparator: '-' },
    layout: { title: 'SunLyvo Nexus', locale: true, layout: 'side', siderWidth: 220 },
    utoopack: {},
    theme: { 'root-entry-name': 'variable' },
    headScripts: [{ src: `${publicPath}config.js` }],
});
