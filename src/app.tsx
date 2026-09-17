import React from 'react';
import { ConfigProvider, App as AntdApp } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import { SLV_THEMES } from '@/config/theme';
import { useDensity } from '@/hooks/useDensity';

export const Root: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [density] = useDensity();

    return (
        <ConfigProvider theme={SLV_THEMES[density]} locale={zhCN}>
            <AntdApp>{children}</AntdApp>
        </ConfigProvider>
    );
};