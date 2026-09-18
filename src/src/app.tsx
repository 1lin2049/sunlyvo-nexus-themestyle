import React, { useEffect } from 'react';
import { ConfigProvider, App as AntdApp, Spin } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import enUS from 'antd/locale/en_US';
import type { RunTimeLayoutConfig } from '@umijs/max';
import { history } from '@umijs/max';

export const layout: RunTimeLayoutConfig = () => {
    return {
        title: 'SunLyvo Nexus',
        logo: '/logo.svg',
        layout: 'side',
        fixSiderbar: true,
        fixedHeader: true,
        contentStyle: { padding: 24 },
        menu: { locale: false },
        onPageChange: () => {
            // 未登录跳转（可选）
            const isLogin = window.location.pathname.includes('/user/login');
            if (!isLogin && !window.SLV_ADMIN_CONFIG) {
                // config 未加载，等待
            }
        },
    };
};

const Root: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [ready, setReady] = React.useState(false);
    const [locale, setLocale] = React.useState(zhCN);

    useEffect(() => {
        // 等待 config.js 加载
        const checkConfig = () => {
            if (window.SLV_ADMIN_CONFIG) {
                const lang = window.SLV_ADMIN_CONFIG.locale || 'zh-CN';
                setLocale(lang.startsWith('en') ? enUS : zhCN);
                setReady(true);
            } else {
                setTimeout(checkConfig, 50);
            }
        };
        checkConfig();
    }, []);

    if (!ready) {
        return (
            <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', minHeight: '100vh' }}>
                <Spin size="large" tip="加载配置中..." />
            </div>
        );
    }

    return (
        <ConfigProvider locale={locale} theme={{ token: { colorPrimary: '#0066ff', borderRadius: 8 } }}>
            <AntdApp>{children}</AntdApp>
        </ConfigProvider>
    );
};

export function rootContainer(container: React.ReactNode) {
    return <Root>{container}</Root>;
}