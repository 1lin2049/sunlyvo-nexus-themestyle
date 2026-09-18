import React, { useEffect, useState } from 'react';
import { Tabs, Card, Table, Empty, Descriptions, App, Spin } from 'antd';
import { UserOutlined, ShoppingOutlined } from '@ant-design/icons';
import { getOrders, type Order } from '../shared/api';
import { config } from '../shared/config';

export default function AccountPage() {
    const { message } = App.useApp();
    const [orders, setOrders] = useState<Order[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        getOrders()
            .then((res) => setOrders(res.data || []))
            .catch(() => message.error('加载订单失败'))
            .finally(() => setLoading(false));
    }, []);

    const orderCols = [
        { title: '订单号', dataIndex: 'order_number', key: 'order_number' },
        {
            title: '状态', dataIndex: 'status', key: 'status',
            render: (v: string) => {
                const map: Record<string, { text: string; color: string }> = {
                    pending:   { text: '待付款', color: '#fa8c16' },
                    paid:      { text: '已付款', color: '#00a854' },
                    shipped:   { text: '已发货', color: '#0066ff' },
                    completed: { text: '已完成', color: '#8c8c8c' },
                };
                const s = map[v] || { text: v, color: '#8c8c8c' };
                return <span style={{ color: s.color }}>{s.text}</span>;
            },
        },
        {
            title: '金额', dataIndex: 'total', key: 'total',
            render: (v: number) => `¥ ${(v || 0).toFixed(2)}`,
        },
        { title: '时间', dataIndex: 'created_at', key: 'created_at' },
    ];

    return (
        <Card>
            <Tabs
                items={[
                    {
                        key: 'orders',
                        label: <span><ShoppingOutlined /> 我的订单</span>,
                        children: loading ? (
                            <div style={{ textAlign: 'center', padding: 60 }}><Spin size="large" /></div>
                        ) : orders.length ? (
                            <Table rowKey="id" columns={orderCols} dataSource={orders} pagination={false} />
                        ) : (
                            <Empty description="暂无订单" />
                        ),
                    },
                    {
                        key: 'profile',
                        label: <span><UserOutlined /> 账户信息</span>,
                        children: (
                            <Descriptions column={1} bordered>
                                <Descriptions.Item label="用户 ID">{config.userId || '未登录'}</Descriptions.Item>
                                <Descriptions.Item label="语言">{config.locale}</Descriptions.Item>
                                <Descriptions.Item label="站点">{config.homeUrl}</Descriptions.Item>
                            </Descriptions>
                        ),
                    },
                ]}
            />
        </Card>
    );
}