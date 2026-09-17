import { defineConfig } from '@umijs/max';
import routes from './config/routes';

export default defineConfig({
    // ─── 基础配置
    title: 'SunLyvo Nexus',
    favicons: ['/favicon.ico'],

    // ─── Ant Design 6
    antd: {
        // Ant Design 6 默认启用 CSS 变量模式
        theme: {
            token: {
                colorPrimary: '#0066ff',
                borderRadius: 8,
                fontSize: 14,
            },
        },
    },

    // ─── 访问控制
    access: {},

    // ─── 数据流
    model: {},

    // ─── 初始化数据
    initialState: {},

    // ─── 请求
    request: {},

    // ─── 国际化
    locale: {
        default: 'zh-CN',
        antd: true,
        baseNavigator: true,
        baseSeparator: '-',
    },

    // ─── 布局
    layout: {
        title: 'SunLyvo Nexus',
        locale: true,
        layout: 'side',
        siderWidth: 220,
    },

    // ─── 路由
    routes,

    // ─── 构建
    npmClient: 'pnpm',
    utoopack: {},
    hash: true,
    history: { type: 'browser' },
    publicPath: '/',

    // ─── 代理（开发环境）
    proxy: {
        '/wp-json': {
            target: 'http://sunlyvo.com',
            changeOrigin: true,
        },
    },

    // ─── 主题
    theme: {
        'root-entry-name': 'variable',
    },
});