import { defineConfig } from '@umijs/max';

export default defineConfig({
    title: 'SunLyvo Nexus',
    antd: {},
    locale: { default: 'zh-CN', antd: true, baseNavigator: true },
    npmClient: 'pnpm',
    hash: true,
    // 多入口：每个嵌页一个 bundle
    mfsu: false,
    publicPath: '/wp-content/themes/sunlyvo-nexus/assets/js/react/dist/',
    outputPath: 'dist',
    // 挂载点：URL 参数指定 entry
    routes: [
        { path: '/cart', component: './entries/cart' },
        { path: '/checkout', component: './entries/checkout' },
        { path: '/account', component: './entries/account' },
    ],
    proxy: {
        '/wp-json': { target: 'http://sunlyvo.com', changeOrigin: true },
    },
});