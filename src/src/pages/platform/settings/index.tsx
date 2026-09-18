import React from 'react';
import { PageContainer } from '@ant-design/pro-components';
import { Card, Empty, Button } from 'antd';
import { getHomeUrl, joinUrl } from '@/services/api';

export default () => {
    const settingsUrl = joinUrl(getHomeUrl(), 'wp-admin/admin.php?page=slv-settings');

    return (
        <PageContainer title="服务配置">
            <Card>
                <Empty description="服务配置在 WordPress 后台管理" />
                <div style={{ textAlign: 'center', marginTop: 16 }}>
                    <Button type="primary" href={settingsUrl} target="_blank">
                        打开服务配置
                    </Button>
                </div>
            </Card>
        </PageContainer>
    );
};
