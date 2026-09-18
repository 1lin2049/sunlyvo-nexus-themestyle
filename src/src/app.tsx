import React from 'react';
import { ConfigProvider, App as AntdApp } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import type { RunTimeLayoutConfig } from '@umijs/max';

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

export function rootContainer(container: React.ReactNode) {
    return (
        <ConfigProvider
            locale={zhCN}
            theme={{ token: { colorPrimary: '#0066ff', borderRadius: 8 } }}
        >
            <AntdApp>{container}</AntdApp>
        </ConfigProvider>
    );
}