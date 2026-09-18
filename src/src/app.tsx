import React, { useEffect, useState } from 'react';
import { ConfigProvider, App as AntdApp, Spin } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import enUS from 'antd/locale/en_US';
import type { RunTimeLayoutConfig } from '@umijs/max';

interface SLVAdminConfig {
    apiBase: string;
    adminUrl: string;
    homeUrl: string;
    locale: string;
}

declare global {
    interface Window {
        SLV_ADMIN_CONFIG?: SLVAdminConfig;
    }
}

export const layout: RunTimeLayoutConfig = () => {
    return {
        title: 'SunLyvo Nexus',
        logo: '/logo.svg',
        layout: 'side',
        fixSiderbar: true,
        fixedHeader: true,
        contentStyle: { padding: 24 },
        menu: { locale: false },
    };
};

const Root: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [ready, setReady] = useState(false);
    const [locale, setLocale] = useState(zhCN);

    useEffect(() => {
        const check = () => {
            if (window.SLV_ADMIN_CONFIG) {
                const lang = window.SLV_ADMIN_CONFIG.locale || 'zh-CN';
                setLocale(lang.startsWith('en') ? enUS : zhCN);
                setReady(true);
            } else {
                setTimeout(check, 50);
            }
        };
        check();
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