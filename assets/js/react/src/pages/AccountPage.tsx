import React, { useEffect, useState } from 'react';
import { Tabs, Card, Table, Empty, Descriptions, App } from 'antd';
import { getOrders, updateProfile } from '../shared/api';

export default function AccountPage() {
    const { message } = App.useApp();
    const [orders, setOrders] = useState<any[]>([]);

    useEffect(() => {
        getOrders().then((res) => setOrders(res.data || [])).catch(() => {});
    }, []);

    const orderCols = [
        { title: '订单号', dataIndex: 'order_number' },
        { title: '状态', dataIndex: 'status' },
        { title: '金额', dataIndex: 'total', render: (v: number) => `¥ ${v?.toFixed(2) || 0}` },
        { title: '时间', dataIndex: 'created_at' },
    ];

    return (
        <Card>
            <Tabs
                items={[
                    {
                        key: 'orders',
                        label: '我的订单',
                        children: orders.length ? (
                            <Table rowKey="id" columns={orderCols} dataSource={orders} pagination={false} />
                        ) : <Empty description="暂无订单" />,
                    },
                    {
                        key: 'profile',
                        label: '账户信息',
                        children: (
                            <Descriptions column={1} bordered>
                                <Descriptions.Item label="用户 ID">{(window as any).SLV_CONFIG?.userId}</Descriptions.Item>
                                <Descriptions.Item label="语言">{(window as any).SLV_CONFIG?.locale}</Descriptions.Item>
                            </Descriptions>
                        ),
                    },
                ]}
            />
        </Card>
    );
}