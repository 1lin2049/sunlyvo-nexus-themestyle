import React, { useEffect, useState } from 'react';
import {
    PageContainer,
    ProCard,
    ProForm,
    ProFormSelect,
    ProFormSwitch,
    ProFormCheckbox,
} from '@ant-design/pro-components';
import { App, Spin, Tag, Typography, Space, Button } from 'antd';
import { CheckCircleFilled, StarFilled } from '@ant-design/icons';
import {
    getStyles,
    setSiteStyle,
    setPageTypeStyles,
    setStoreStylePolicy,
    type StyleMeta,
    type StylesResponse,
} from '@/services/style';

const { Text, Title } = Typography;

const StylePage: React.FC = () => {
    const { message } = App.useApp();
    const [data, setData] = useState<StylesResponse | null>(null);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    const load = async () => {
        setLoading(true);
        try {
            const res = await getStyles();
            setData(res);
        } catch (e) {
            message.error('加载风格失败');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { load(); }, []);

    if (loading) return <PageContainer><Spin /></PageContainer>;
    if (!data) return <PageContainer>无数据</PageContainer>;

    const { groups, current_style } = data;

    const handleSetSite = async (style: string) => {
        setSaving(true);
        try {
            await setSiteStyle(style);
            message.success('已切换全站默认风格');
            load();
        } catch (e) {
            message.error('保存失败');
        } finally {
            setSaving(false);
        }
    };

    const renderGroup = (title: string, items: Record<string, StyleMeta>) => (
        <ProCard title={title} bordered headerBordered style={{ marginBottom: 16 }}>
            <Space wrap size={16}>
                {Object.values(items).map((s) => (
                    <div
                        key={s.slug}
                        onClick={() => handleSetSite(s.slug)}
                        style={{
                            cursor: 'pointer',
                            border: s.slug === current_style
                                ? '2px solid #0066ff'
                                : '1px solid #e8e8e8',
                            borderRadius: 8,
                            padding: 16,
                            minWidth: 200,
                            position: 'relative',
                            transition: 'all .2s',
                        }}
                    >
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <Text strong>{s.label}</Text>
                            {s.slug === current_style && <CheckCircleFilled style={{ color: '#0066ff' }} />}
                        </div>
                        <div style={{ marginTop: 8 }}>
                            <Text type="secondary" style={{ fontSize: 12 }}>{s.slug}</Text>
                        </div>
                        <div style={{ marginTop: 8 }}>
                            {s.user_switchable && <Tag color="blue" icon={<StarFilled />}>用户可切换</Tag>}
                            {s.admin_switchable && !s.user_switchable && <Tag>管理员可设</Tag>}
                        </div>
                    </div>
                ))}
            </Space>
        </ProCard>
    );

    return (
        <PageContainer
            title="风格管理"
            subTitle="行业多风格配置，管理员可切换全站默认风格"
        >
            <ProCard title="当前状态" bordered headerBordered style={{ marginBottom: 16 }}>
                <Space size={24}>
                    <div>
                        <Text type="secondary">全站默认风格：</Text>
                        <Text strong>{groups.base[current_style]?.label || current_style}</Text>
                    </div>
                    <div>
                        <Text type="secondary">用户主题偏好：</Text>
                        <Text strong>{data.current_theme}</Text>
                    </div>
                </Space>
            </ProCard>

            {renderGroup('基础风格', groups.base)}
            {renderGroup('行业风格', groups.industry)}
            {renderGroup('辅助风格（无障碍）', groups.aux)}

            <ProCard title="按页面类型覆盖" bordered headerBordered style={{ marginBottom: 16 }}>
                <ProForm
                    onFinish={async (v) => {
                        await setPageTypeStyles(v as Record<string, string>);
                        message.success('已保存页面类型映射');
                    }}
                    submitter={{ searchConfig: { submitText: '保存' } }}
                >
                    <ProFormSelect
                        name="blog"
                        label="博客区"
                        options={Object.values(groups.industry).map((s) => ({ label: s.label, value: s.slug }))}
                        placeholder="不覆盖"
                    />
                    <ProFormSelect
                        name="shop"
                        label="商城"
                        options={Object.values(groups.industry).map((s) => ({ label: s.label, value: s.slug }))}
                        placeholder="不覆盖"
                    />
                    <ProFormSelect
                        name="knowledge"
                        label="知识区"
                        options={Object.values(groups.industry).map((s) => ({ label: s.label, value: s.slug }))}
                        placeholder="不覆盖"
                    />
                </ProForm>
            </ProCard>

            <ProCard title="商户/创作者独立风格策略" bordered headerBordered>
                <ProForm
                    onFinish={async (v: any) => {
                        await setStoreStylePolicy(!!v.allow, v.allowed || []);
                        message.success('已保存商户策略');
                    }}
                    submitter={{ searchConfig: { submitText: '保存' } }}
                >
                    <ProFormSwitch name="allow" label="允许商户独立设置店铺风格" />
                    <ProFormCheckbox.Group
                        name="allowed"
                        label="允许的风格范围"
                        options={Object.values(groups.industry).map((s) => ({ label: s.label, value: s.slug }))}
                    />
                </ProForm>
            </ProCard>
        </PageContainer>
    );
};

export default StylePage;