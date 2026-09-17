import { ConfigProvider, App as AntdApp } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import { useDensity } from '@/hooks/useDensity';
import { SLV_THEMES } from '@/config/theme';
import type { RunTimeLayoutConfig } from '@umijs/max';

export const layout: RunTimeLayoutConfig = () => {
    return {
        title: 'SunLyvo Nexus',
        logo: '/logo.svg',
        layout: 'side',
        fixSiderbar: true,
        fixedHeader: true,
        contentStyle: { padding: 24 },
    };
};

export function rootContainer(container: React.ReactNode) {
    return <Root>{container}</Root>;
}

const Root: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [density] = useDensity();
    return (
        <ConfigProvider theme={SLV_THEMES[density]} locale={zhCN}>
            <AntdApp>{children}</AntdApp>
        </ConfigProvider>
    );
};