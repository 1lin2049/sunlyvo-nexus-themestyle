import React, { useState } from 'react';
import { Steps, Form, Input, Button, Card, Result, App, Space } from 'antd';
import { getCartItems, addCartItem } from '../shared/api';

export default function CheckoutPage() {
    const { message } = App.useApp();
    const [step, setStep] = useState(0);
    const [done, setDone] = useState(false);
    const [form] = Form.useForm();

    const submit = async () => {
        try {
            const values = await form.validateFields();
            const cfg = (window as any).SLV_CONFIG || {};
            const res = await fetch(cfg.restUrl + '/checkout', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-WP-Nonce': cfg.nonce },
                body: JSON.stringify(values),
            });
            const data = await res.json();
            if (data.success) {
                setDone(true);
            } else {
                message.error(data.error || '下单失败');
            }
        } catch {
            message.error('请检查表单');
        }
    };

    if (done) {
        return (
            <Result
                status="success"
                title="订单提交成功"
                subTitle="我们已收到您的订单，稍后会有邮件通知"
                extra={[
                    <Button type="primary" key="home" href="/">返回首页</Button>,
                    <Button key="orders" href="/my-account/">查看订单</Button>,
                ]}
            />
        );
    }

    return (
        <Card>
            <Steps current={step} style={{ marginBottom: 32 }}
                items={[{ title: '收货信息' }, { title: '支付方式' }, { title: '完成' }]} />
            <Form form={form} layout="vertical">
                <Form.Item name="name" label="姓名" rules={[{ required: true }]}>
                    <Input placeholder="请输入姓名" />
                </Form.Item>
                <Form.Item name="email" label="邮箱" rules={[{ required: true, type: 'email' }]}>
                    <Input placeholder="your@email.com" />
                </Form.Item>
                <Form.Item name="phone" label="电话">
                    <Input />
                </Form.Item>
                <Form.Item name="address" label="收货地址" rules={[{ required: true }]}>
                    <Input.TextArea rows={3} />
                </Form.Item>
                <Button type="primary" size="large" block onClick={submit}>
                    提交订单
                </Button>
            </Form>
        </Card>
    );
}