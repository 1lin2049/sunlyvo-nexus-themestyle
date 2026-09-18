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