import React, { useEffect, useState } from 'react';
import { Table, Button, InputNumber, Empty, App, Space, Typography, Card } from 'antd';
import { DeleteOutlined, ShoppingCartOutlined } from '@ant-design/icons';
import { getCartItems, updateCartItem, removeCartItem } from '../shared/api';

const { Title } = Typography;

export default function CartPage() {
    const { message } = App.useApp();
    const [items, setItems] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    const load = async () => {
        setLoading(true);
        try {
            const res = await getCartItems();
            setItems(res.items || []);
        } catch {
            message.error('加载购物车失败');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { load(); }, []);

    const update = async (id: number, qty: number) => {
        await updateCartItem(id, qty);
        load();
    };

    const remove = async (id: number) => {
        await removeCartItem(id);
        message.success('已移除');
        load();
    };

    const total = items.reduce((s, i) => s + (i.subtotal || 0), 0);

    const columns = [
        { title: '商品', dataIndex: 'product_name', key: 'name' },
        {
            title: '单价', dataIndex: 'unit_price', key: 'price',
            render: (v: number) => `¥ ${v.toFixed(2)}`,
        },
        {
            title: '数量', key: 'qty',
            render: (_: any, r: any) => (
                <InputNumber min={1} value={r.quantity}
                    onChange={(v) => update(r.id, Number(v))} />
            ),
        },
        {
            title: '小计', dataIndex: 'subtotal', key: 'subtotal',
            render: (v: number) => <strong>¥ {v.toFixed(2)}</strong>,
        },
        {
            title: '操作', key: 'op',
            render: (_: any, r: any) => (
                <Button type="text" danger icon={<DeleteOutlined />} onClick={() => remove(r.id)} />
            ),
        },
    ];

    return (
        <Card>
            <Title level={3}><ShoppingCartOutlined /> 购物车</Title>
            {items.length === 0 && !loading ? (
                <Empty description="购物车为空" />
            ) : (
                <>
                    <Table rowKey="id" columns={columns} dataSource={items} loading={loading} pagination={false} />
                    <div style={{ marginTop: 24, textAlign: 'right' }}>
                        <Space size="large">
                            <span>合计：<strong style={{ fontSize: 24, color: '#0066ff' }}>¥ {total.toFixed(2)}</strong></span>
                            <Button type="primary" size="large" href="/checkout/">去结算</Button>
                        </Space>
                    </div>
                </>
            )}
        </Card>
    );
}