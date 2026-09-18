import React from 'react';
import { PageContainer } from '@ant-design/pro-components';
import { Card, Empty } from 'antd';

export default () => (
    <PageContainer title="服务配置">
        <Card>
            <Empty description="请到 WordPress 后台 → SunLyvo → 服务配置" />
        </Card>
    </PageContainer>
);