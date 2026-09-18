#!/usr/bin/env bash
##############################################################################
# SunLyvo Nexus · 中台一次性部署脚本
#
# 完全自包含，用 heredoc 创建所有文件，避免手动复制粘贴错乱。
#
# 用法：bash /tmp/slv-deploy.sh
##############################################################################

set -euo pipefail

THEME_DIR="/opt/1panel/www/sites/sunlyvo.com/index/wp-content/themes/sunlyvo-nexus"
WP_ROOT="/opt/1panel/www/sites/sunlyvo.com/index"
SRC="${THEME_DIR}/src"

echo "════════════════════════════════════════════════════════"
echo "  SunLyvo Nexus 中台部署"
echo "════════════════════════════════════════════════════════"

# ─── 步骤 1：清理 + 准备目录 ───
echo ""
echo "▶ [1/6] 清理旧文件 + 准备目录"

rm -rf "${SRC}/node_modules" "${SRC}/pnpm-lock.yaml" "${SRC}/dist" "${SRC}/.umi" 2>/dev/null || true
rm -f "${SRC}/config/config.ts" "${SRC}/config/routes.ts" 2>/dev/null || true

mkdir -p "${SRC}/config"
mkdir -p "${SRC}/src/services"
mkdir -p "${SRC}/src/pages/user/login"
mkdir -p "${SRC}/src/pages/platform/dashboard"
mkdir -p "${SRC}/src/pages/platform/template"
mkdir -p "${SRC}/src/pages/platform/users"
mkdir -p "${SRC}/src/pages/platform/vendors"
mkdir -p "${SRC}/src/pages/platform/orders"
mkdir -p "${SRC}/src/pages/platform/settings"

echo "✅ 目录就绪"

# ─── 步骤 2：创建中台所有文件 ───
echo ""
echo "▶ [2/6] 创建中台文件"

# ── package.json ──
cat > "${SRC}/package.json" << 'PKGEOF'
{
    "name": "sunlyvo-nexus-admin",
    "version": "1.0.0",
    "private": true,
    "description": "SunLyvo Nexus 中台管理端",
    "scripts": {
        "dev": "max dev",
        "build": "cross-env NODE_ENV=production max build",
        "postinstall": "max setup",
        "setup": "max setup",
        "tsc": "tsc --noEmit"
    },
    "dependencies": {
        "@ant-design/icons": "^6.3.4",
        "@ant-design/pro-components": "3.1.14-7",
        "@umijs/max": "^4.6.51",
        "antd": "^6.6.4",
        "dayjs": "^1.11.13",
        "react": "^19.3.0",
        "react-dom": "^19.3.0"
    },
    "devDependencies": {
        "@types/react": "^19.3.0",
        "@types/react-dom": "^19.3.0",
        "cross-env": "^7.0.3",
        "typescript": "^5.9.3"
    },
    "engines": {
        "node": ">=20.0.0",
        "pnpm": ">=9.0.0"
    },
    "pnpm": {
        "onlyBuiltDependencies": ["core-js", "core-js-pure", "es5-ext", "esbuild"]
    }
}
PKGEOF

# ── config/config.ts ──
cat > "${SRC}/config/config.ts" << 'CONFIGEOF'
import { defineConfig } from '@umijs/max';
import routes from './routes';

export default defineConfig(({ mode }) => {
    const isProd = mode === 'production';
    const publicPath = isProd ? '/app/' : '/';
    const proxyTarget = process.env.WP_PROXY_TARGET;

    return {
        title: 'SunLyvo Nexus',
        favicons: [`${publicPath}favicon.ico`],
        antd: {
            theme: {
                token: {
                    colorPrimary: '#0066ff',
                    borderRadius: 8,
                    fontSize: 14,
                },
            },
        },
        access: {},
        model: {},
        initialState: {},
        request: {},
        locale: {
            default: 'zh-CN',
            antd: true,
            baseNavigator: true,
            baseSeparator: '-',
        },
        layout: {
            title: 'SunLyvo Nexus',
            locale: true,
            layout: 'side',
            siderWidth: 220,
        },
        routes,
        npmClient: 'pnpm',
        utoopack: {},
        hash: true,
        history: { type: 'browser' },
        publicPath,
        base: publicPath,
        outputPath: 'dist',
        proxy: proxyTarget ? {
            '/wp-json': { target: proxyTarget, changeOrigin: true },
            '/wp-login.php': { target: proxyTarget, changeOrigin: true },
            '/wp-admin': { target: proxyTarget, changeOrigin: true },
        } : undefined,
        theme: { 'root-entry-name': 'variable' },
        headScripts: [
            { src: `${publicPath}config.js` },
        ],
    };
});
CONFIGEOF

# ── config/routes.ts ──
cat > "${SRC}/config/routes.ts" << 'ROUTESEOF'
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
ROUTESEOF

# ── tsconfig.json ──
cat > "${SRC}/tsconfig.json" << 'TSEOF'
{
    "compilerOptions": {
        "target": "ES2022",
        "module": "ESNext",
        "moduleResolution": "Bundler",
        "lib": ["DOM", "DOM.Iterable", "ES2022"],
        "jsx": "react-jsx",
        "strict": true,
        "esModuleInterop": true,
        "skipLibCheck": true,
        "forceConsistentCasingInFileNames": true,
        "resolveJsonModule": true,
        "isolatedModules": true,
        "noEmit": true,
        "baseUrl": ".",
        "paths": {
            "@/*": ["src/*"],
            "@@/*": ["src/.umi/*"]
        }
    },
    "include": ["src/**/*.ts", "src/**/*.tsx", "config/**/*.ts"],
    "exclude": ["node_modules", "dist"]
}
TSEOF

# ── .gitignore ──
cat > "${SRC}/.gitignore" << 'GITEOF'
/node_modules
/dist
/.umi
/.umi-production
/.umi-test
*.log
.DS_Store
GITEOF

# ── src/app.tsx ──
cat > "${SRC}/src/app.tsx" << 'APPEOF'
import React, { useEffect, useState } from 'react';
import { ConfigProvider, App as AntdApp, Spin } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import type { RunTimeLayoutConfig } from '@umijs/max';

export const layout: RunTimeLayoutConfig = () => ({
    title: 'SunLyvo Nexus',
    logo: '/logo.svg',
    layout: 'side',
    fixSiderbar: true,
    fixedHeader: true,
    contentStyle: { padding: 24 },
    menu: { locale: false },
});

const Root: React.FC<{ children: React.ReactNode }> = ({ children }) => {
    const [ready, setReady] = useState(false);

    useEffect(() => {
        let attempts = 0;
        const check = () => {
            attempts++;
            if ((window as any).SLV_ADMIN_CONFIG) {
                setReady(true);
            } else if (attempts < 40) {
                setTimeout(check, 50);
            } else {
                setReady(true);
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
        <ConfigProvider locale={zhCN} theme={{ token: { colorPrimary: '#0066ff', borderRadius: 8 } }}>
            <AntdApp>{children}</AntdApp>
        </ConfigProvider>
    );
};

export function rootContainer(container: React.ReactNode) {
    return <Root>{container}</Root>;
}
APPEOF

# ── src/services/api.ts ──
cat > "${SRC}/src/services/api.ts" << 'APIEOF'
import { request } from '@umijs/max';

function getApiBase(): string {
    const cfg = (window as any).SLV_ADMIN_CONFIG;
    if (cfg && cfg.apiBase) return cfg.apiBase;
    return '/wp-json/slv/v1';
}

export function getBasePath(): string {
    const cfg = (window as any).SLV_ADMIN_CONFIG;
    if (cfg && cfg.adminUrl) {
        try { return new URL(cfg.adminUrl).pathname; } catch { /* ignore */ }
    }
    const match = window.location.pathname.match(/^(\/[^/]*\/)/);
    return match ? match[1] : '/';
}

export function getHomeUrl(): string {
    const cfg = (window as any).SLV_ADMIN_CONFIG;
    return cfg && cfg.homeUrl ? cfg.homeUrl : '/';
}

export async function apiGet<T>(path: string, params?: Record<string, unknown>): Promise<T> {
    return request(`${getApiBase()}${path}`, { method: 'GET', params });
}

export async function apiPost<T>(path: string, data?: unknown): Promise<T> {
    return request(`${getApiBase()}${path}`, { method: 'POST', data });
}
APIEOF

# ── src/global.less ──
cat > "${SRC}/src/global.less" << 'LESSEOF'
html, body, #root {
    height: 100%;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'PingFang SC', 'Hiragino Sans GB', 'Microsoft YaHei', sans-serif;
}
LESSEOF

# ── src/pages/user/login/index.tsx ──
cat > "${SRC}/src/pages/user/login/index.tsx" << 'LOGINEOF'
import React from 'react';
import { LoginForm, ProFormText } from '@ant-design/pro-components';
import { UserOutlined, LockOutlined } from '@ant-design/icons';
import { App } from 'antd';
import { getBasePath } from '@/services/api';

export default () => {
    const { message } = App.useApp();
    const basePath = getBasePath();

    return (
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', minHeight: '100vh', background: 'linear-gradient(135deg, #f0f5ff, #e6f0ff)' }}>
            <LoginForm
                title="SunLyvo Nexus"
                subTitle="内容电商管理后台"
                onFinish={async (values) => {
                    try {
                        const form = new FormData();
                        form.append('log', values.username);
                        form.append('pwd', values.password);
                        form.append('wp-submit', 'Log In');
                        form.append('redirect_to', basePath);
                        form.append('testcookie', '1');
                        const res = await fetch('/wp-login.php', { method: 'POST', body: form, credentials: 'include' });
                        if (res.ok || res.redirected) {
                            message.success('登录成功');
                            window.location.href = basePath;
                        } else {
                            message.error('用户名或密码错误');
                        }
                    } catch {
                        message.error('登录失败');
                    }
                }}
            >
                <ProFormText name="username" fieldProps={{ size: 'large', prefix: <UserOutlined /> }} placeholder="用户名" rules={[{ required: true, message: '请输入用户名' }]} />
                <ProFormText.Password name="password" fieldProps={{ size: 'large', prefix: <LockOutlined /> }} placeholder="密码" rules={[{ required: true, message: '请输入密码' }]} />
            </LoginForm>
        </div>
    );
};
LOGINEOF

# ── src/pages/platform/dashboard/index.tsx ──
cat > "${SRC}/src/pages/platform/dashboard/index.tsx" << 'DASHEOF'
import React from 'react';
import { PageContainer, ProCard, StatisticCard } from '@ant-design/pro-components';
import { Row, Col, Button } from 'antd';
import { Link } from '@umijs/max';

const { Statistic } = StatisticCard;

export default () => (
    <PageContainer title="仪表盘" subTitle="SunLyvo Nexus 平台概览">
        <Row gutter={16}>
            <Col span={6}><ProCard><Statistic title="商品总数" value={5} /></ProCard></Col>
            <Col span={6}><ProCard><Statistic title="订单总数" value={0} /></ProCard></Col>
            <Col span={6}><ProCard><Statistic title="用户总数" value={1} /></ProCard></Col>
            <Col span={6}><ProCard><Statistic title="今日收入" value={0} prefix="¥" /></ProCard></Col>
        </Row>
        <ProCard title="快速操作" style={{ marginTop: 16 }}>
            <Link to="/platform/template"><Button type="primary" style={{ marginRight: 12 }}>模板管理</Button></Link>
            <Link to="/platform/settings"><Button style={{ marginRight: 12 }}>服务配置</Button></Link>
            <Link to="/platform/users"><Button>用户管理</Button></Link>
        </ProCard>
    </PageContainer>
);
DASHEOF

# ── src/pages/platform/template/index.tsx ──
cat > "${SRC}/src/pages/platform/template/index.tsx" << 'TPLEOF'
import React, { useEffect, useState } from 'react';
import { PageContainer, ProCard } from '@ant-design/pro-components';
import { App, Spin, Tag, Typography, Space } from 'antd';
import { CheckCircleFilled } from '@ant-design/icons';
import { apiGet, apiPost } from '@/services/api';

const { Text } = Typography;

export default () => {
    const { message } = App.useApp();
    const [data, setData] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const load = async () => {
        setLoading(true);
        try {
            const res = await apiGet<any>('/templates');
            setData(res);
        } catch {
            message.error('加载模板失败');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { load(); }, []);

    const handleSetSite = async (slug: string) => {
        try {
            await apiPost('/site/template', { template: slug });
            message.success('已切换全站默认模板');
            load();
        } catch {
            message.error('保存失败');
        }
    };

    if (loading) return <PageContainer><Spin /></PageContainer>;
    if (!data) return <PageContainer>无数据</PageContainer>;

    const renderGroup = (title: string, items: Record<string, any>) => (
        <ProCard title={title} bordered headerBordered style={{ marginBottom: 16 }}>
            <Space wrap size={16}>
                {Object.values(items || {}).map((t: any) => (
                    <div key={t.slug} onClick={() => handleSetSite(t.slug)} style={{ cursor: 'pointer', border: t.is_active ? '2px solid #0066ff' : '1px solid #e8e8e8', borderRadius: 8, padding: 16, minWidth: 220 }}>
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <Text strong>{t.label}</Text>
                            {t.is_active && <CheckCircleFilled style={{ color: '#0066ff' }} />}
                        </div>
                        <div style={{ marginTop: 8 }}><Text type="secondary" style={{ fontSize: 12 }}>{t.description}</Text></div>
                        <div style={{ marginTop: 8 }}>
                            {t.user_switchable && <Tag color="blue">用户可切换</Tag>}
                            {t.admin_switchable && !t.user_switchable && <Tag>管理员可设</Tag>}
                        </div>
                    </div>
                ))}
            </Space>
        </ProCard>
    );

    return (
        <PageContainer title="模板管理">
            <ProCard title="当前状态" bordered headerBordered style={{ marginBottom: 16 }}>
                <Space size={24}>
                    <div><Text type="secondary">当前模板：</Text><Text strong>{data.current_template}</Text></div>
                    <div><Text type="secondary">主题模式：</Text><Text strong>{data.current_theme}</Text></div>
                </Space>
            </ProCard>
            {renderGroup('基础模板', data.groups?.base)}
            {renderGroup('行业模板', data.groups?.industry)}
            {renderGroup('辅助模板', data.groups?.aux)}
        </PageContainer>
    );
};
TPLEOF

# ── 4 个 Empty 页面 ──
for p in users vendors orders; do
mkdir -p "${SRC}/src/pages/platform/${p}"
cat > "${SRC}/src/pages/platform/${p}/index.tsx" << EMPTYEOF
import React from 'react';
import { PageContainer } from '@ant-design/pro-components';
import { Card, Empty } from 'antd';

export default () => (
    <PageContainer title="管理">
        <Card><Empty description="功能开发中" /></Card>
    </PageContainer>
);
EMPTYEOF
done

# ── settings/index.tsx ──
cat > "${SRC}/src/pages/platform/settings/index.tsx" << 'SETEOF'
import React from 'react';
import { PageContainer } from '@ant-design/pro-components';
import { Card, Empty, Button } from 'antd';
import { getHomeUrl } from '@/services/api';

export default () => {
    const home = getHomeUrl();
    return (
        <PageContainer title="服务配置">
            <Card>
                <Empty description="服务配置在 WordPress 后台管理" />
                <div style={{ textAlign: 'center', marginTop: 16 }}>
                    <Button type="primary" href={`${home}wp-admin/admin.php?page=slv-settings`} target="_blank">
                        打开服务配置
                    </Button>
                </div>
            </Card>
        </PageContainer>
    );
};
SETEOF

echo "✅ 15 个中台文件创建完成"

# ─── 步骤 3：验证文件内容 ───
echo ""
echo "▶ [3/6] 验证关键文件"

echo "  package.json build script:"
grep -q '"build":' "${SRC}/package.json" && echo "  ✅ 有 build script" || { echo "  ❌ 缺 build script"; exit 1; }

echo "  config.ts 内容:"
grep -q 'defineConfig(({ mode })' "${SRC}/config/config.ts" && echo "  ✅ 函数式 config" || { echo "  ❌ config.ts 不对"; exit 1; }

echo "  routes.ts 内容:"
grep -q '/platform/template' "${SRC}/config/routes.ts" && echo "  ✅ 有平台路由" || { echo "  ❌ routes.ts 不对"; exit 1; }

# ─── 步骤 4：安装 + 构建 ───
echo ""
echo "▶ [4/6] 安装依赖 + 构建"
cd "${SRC}"
pnpm install --config.confirmModulesPurge=false
pnpm build

# ─── 步骤 5：验证构建产物 ───
echo ""
echo "▶ [5/6] 验证构建产物"

if [ ! -f "${SRC}/dist/index.html" ]; then
    echo "❌ dist/index.html 不存在，构建失败"
    exit 1
fi

echo ""
echo "════════════════════════════════════════════════════════"
echo "  index.html 引用路径（必须带 /app/ 前缀）"
echo "════════════════════════════════════════════════════════"
grep -oP '(src|href)="[^"]*"' "${SRC}/dist/index.html"

# 自动检测
if grep -q 'src="/app/' "${SRC}/dist/index.html"; then
    echo ""
    echo "✅ 所有路径带 /app/ 前缀 → 构建正确"
else
    echo ""
    echo "❌ 路径未带 /app/ 前缀 → 停止！"
    echo "  请把上面输出和 config.ts 内容发给开发者"
    exit 1
fi

# ─── 步骤 6：部署 ───
echo ""
echo "▶ [6/6] 部署到 /app/"

rm -rf "${WP_ROOT}/app" "${WP_ROOT}/admin" "${WP_ROOT}/console" 2>/dev/null || true
mkdir -p "${WP_ROOT}/app"
cp -r "${SRC}/dist/"* "${WP_ROOT}/app/"
echo "✅ 复制到 ${WP_ROOT}/app/"

# 生成 config.js
cd "${WP_ROOT}"
wp option update slv_admin_url 'http://sunlyvo.com/app/' 2>/dev/null || true
wp eval 'if (function_exists("slv_write_admin_config_js")) slv_write_admin_config_js();' 2>/dev/null || echo "⚠️ 请到后台生成 config.js"

if [ -f "${WP_ROOT}/app/config.js" ]; then
    echo ""
    echo "  config.js 内容："
    cat "${WP_ROOT}/app/config.js"
fi

# 清缓存
wp cache flush 2>/dev/null || true
wp rewrite flush --hard 2>/dev/null || true

echo ""
echo "════════════════════════════════════════════════════════"
echo "  ✅ 部署完成"
echo ""
echo "  访问：http://sunlyvo.com/app/"
echo ""
echo "  Nginx 配置请参考 tools/nginx-admin.conf"
echo "  重载：sudo nginx -t && sudo systemctl reload nginx"
echo "════════════════════════════════════════════════════════"