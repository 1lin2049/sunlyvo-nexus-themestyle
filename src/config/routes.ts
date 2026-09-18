export default [
    {
        path: '/',
        redirect: '/dashboard',
    },
    {
        path: '/user',
        layout: false,
        routes: [
            { name: '登录', path: '/user/login', component: './user/login' },
            { name: '注册', path: '/user/register', component: './user/register' },
        ],
    },
    {
        name: '商业化验证',
        path: '/dashboard',
        icon: 'DashboardOutlined',
        component: './platform/dashboard',
    },
    {
        name: '平台管理',
        path: '/platform',
        icon: 'SettingOutlined',
        routes: [
            { name: '用户', path: '/platform/users', component: './platform/users' },
            { name: '商户', path: '/platform/vendors', component: './platform/vendors' },
            { name: '订单', path: '/platform/orders', component: './platform/orders' },
            { name: '商品', path: '/platform/products', component: './platform/products' },
            { name: '服务配置', path: '/platform/settings', component: './platform/settings' },
            { name: '模板管理', path: '/platform/template', component: './platform/template' },
        ],
    },
    {
        name: '站长中心',
        path: '/station',
        icon: 'GlobalOutlined',
        routes: [
            { name: '仪表盘', path: '/station/dashboard', component: './station/dashboard' },
            { name: '商户管理', path: '/station/vendors', component: './station/vendors' },
            { name: '收益', path: '/station/earnings', component: './station/earnings' },
        ],
    },
    {
        name: '商户中心',
        path: '/vendor',
        icon: 'ShopOutlined',
        routes: [
            { name: '仪表盘', path: '/vendor/dashboard', component: './vendor/dashboard' },
            { name: '产品', path: '/vendor/products', component: './vendor/products' },
            { name: '订单', path: '/vendor/orders', component: './vendor/orders' },
        ],
    },
    {
        name: '创作者中心',
        path: '/creator',
        icon: 'EditOutlined',
        routes: [
            { name: '仪表盘', path: '/creator/dashboard', component: './creator/dashboard' },
            { name: '内容', path: '/creator/content', component: './creator/content' },
            { name: '合集', path: '/creator/collections', component: './creator/collections' },
        ],
    },
];