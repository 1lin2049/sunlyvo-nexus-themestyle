export default [
    { path: '/', redirect: '/dashboard' },
    {
        path: '/user',
        layout: false,
        routes: [
            { name: '登录', path: '/user/login', component: './user/login' },
        ],
    },
    {
        name: '仪表盘',
        path: '/dashboard',
        icon: 'DashboardOutlined',
        component: './platform/dashboard',
    },
    {
        name: '平台管理',
        path: '/platform',
        icon: 'SettingOutlined',
        routes: [
            { name: '模板管理', path: '/platform/template', component: './platform/template' },
            { name: '用户管理', path: '/platform/users', component: './platform/users' },
            { name: '商户管理', path: '/platform/vendors', component: './platform/vendors' },
            { name: '订单管理', path: '/platform/orders', component: './platform/orders' },
            { name: '服务配置', path: '/platform/settings', component: './platform/settings' },
        ],
    },
];