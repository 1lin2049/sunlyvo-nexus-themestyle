import React from 'react';
import { PageContainer, ProCard, StatisticCard } from '@ant-design/pro-components';
import { Row, Col, Button } from 'antd';

const { Statistic } = StatisticCard;

export default () => {
    return (
        <PageContainer title="仪表盘" subTitle="SunLyvo Nexus 平台概览">
            <Row gutter={16}>
                <Col span={6}>
                    <ProCard>
                        <Statistic title="商品总数" value={5} />
                    </ProCard>
                </Col>
                <Col span={6}>
                    <ProCard>
                        <Statistic title="订单总数" value={0} />
                    </ProCard>
                </Col>
                <Col span={6}>
                    <ProCard>
                        <Statistic title="用户总数" value={1} />
                    </ProCard>
                </Col>
                <Col span={6}>
                    <ProCard>
                        <Statistic title="今日收入" value={0} prefix="¥" />
                    </ProCard>
                </Col>
            </Row>

            <ProCard title="快速操作" style={{ marginTop: 16 }}>
                <Button type="primary" href="/admin/platform/template" style={{ marginRight: 12 }}>
                    模板管理
                </Button>
                <Button href="/admin/platform/settings" style={{ marginRight: 12 }}>
                    服务配置
                </Button>
                <Button href="/admin/platform/users">用户管理</Button>
            </ProCard>
        </PageContainer>
    );
};