import React, { useEffect, useState } from 'react';
import { PageContainer, ProCard } from '@ant-design/pro-components';
import { App, Spin, Tag, Typography, Space } from 'antd';
import { CheckCircleFilled, StarFilled } from '@ant-design/icons';
import {
    getTemplates,
    setSiteTemplate,
    type TemplateMeta,
    type TemplatesResponse,
} from '@/services/template';

const { Text } = Typography;

const TemplatePage: React.FC = () => {
    const { message } = App.useApp();
    const [data, setData] = useState<TemplatesResponse | null>(null);
    const [loading, setLoading] = useState(true);

    const load = async () => {
        setLoading(true);
        try {
            const res = await getTemplates();
            setData(res);
        } catch (e) {
            message.error('加载模板失败');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { load(); }, []);

    if (loading) return <PageContainer><Spin /></PageContainer>;
    if (!data) return <PageContainer>无数据</PageContainer>;

    const { groups, current_template } = data;

    const handleSetSite = async (slug: string) => {
        try {
            await setSiteTemplate(slug);
            message.success('已切换全站默认模板');
            load();
        } catch (e) {
            message.error('保存失败');
        }
    };

    const renderGroup = (title: string, items: Record<string, TemplateMeta>) => (
        <ProCard title={title} bordered headerBordered style={{ marginBottom: 16 }}>
            <Space wrap size={16}>
                {Object.values(items).map((t) => (
                    <div
                        key={t.slug}
                        onClick={() => handleSetSite(t.slug)}
                        style={{
                            cursor: 'pointer',
                            border: t.is_active ? '2px solid #0066ff' : '1px solid #e8e8e8',
                            borderRadius: 8,
                            padding: 16,
                            minWidth: 220,
                            position: 'relative',
                            transition: 'all .2s',
                        }}
                    >
                        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                            <Text strong>{t.label}</Text>
                            {t.is_active && <CheckCircleFilled style={{ color: '#0066ff' }} />}
                        </div>
                        <div style={{ marginTop: 8 }}>
                            <Text type="secondary" style={{ fontSize: 12 }}>{t.description}</Text>
                        </div>
                        <div style={{ marginTop: 8 }}>
                            {t.user_switchable && <Tag color="blue" icon={<StarFilled />}>用户可切换</Tag>}
                            {t.admin_switchable && !t.user_switchable && <Tag>管理员可设</Tag>}
                        </div>
                    </div>
                ))}
            </Space>
        </ProCard>
    );

    return (
        <PageContainer title="模板管理" subTitle="行业模板配置">
            <ProCard title="当前状态" bordered headerBordered style={{ marginBottom: 16 }}>
                <Space size={24}>
                    <div>
                        <Text type="secondary">当前模板：</Text>
                        <Text strong>{groups.base[current_template]?.label || current_template}</Text>
                    </div>
                    <div>
                        <Text type="secondary">主题模式：</Text>
                        <Text strong>{data.current_theme}</Text>
                    </div>
                </Space>
            </ProCard>

            {renderGroup('基础模板', groups.base)}
            {renderGroup('行业模板', groups.industry)}
            {renderGroup('辅助模板', groups.aux)}
        </PageContainer>
    );
};

export default TemplatePage;