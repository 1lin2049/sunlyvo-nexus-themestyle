import React, { useState } from 'react';
import { Steps, Form, Input, Button, Card, Result, App, Space, Radio } from 'antd';
import { createOrder, type CheckoutData } from '../shared/api';

export default function CheckoutPage() {
    const { message } = App.useApp();
    const [step, setStep] = useState(0);
    const [done, setDone] = useState(false);
    const [orderId, setOrderId] = useState<number | null>(null);
    const [form] = Form.useForm();

    const submit = async () => {
        try {
            const values = await form.validateFields();
            setStep(1);
            const res = await createOrder(values as CheckoutData);
            if (res.success && res.order_id) {
                setOrderId(res.order_id);
                setStep(2);
                setDone(true);
            } else {
                message.error(res.error || '下单失败');
                setStep(0);
            }
        } catch {
            message.error('请检查表单填写');
            setStep(0);
        }
    };

    if (done) {
        return (
            <Result
                status="success"
                title="订单提交成功"
                subTitle={`订单号：${orderId}，我们会尽快为您处理`}
                extra={[
                    <Button type="primary" key="home" href="/">返回首页</Button>,
                    <Button key="orders" href="/my-account/">查看订单</Button>,
                ]}
            />
        );
    }

    return (
        <Card>
            <Steps
                current={step}
                style={{ marginBottom: 32 }}
                items={[{ title: '填写信息' }, { title: '提交中' }, { title: '完成' }]}
            />
            <Form form={form} layout="vertical" style={{ maxWidth: 500 }}>
                <Form.Item name="name" label="姓名" rules={[{ required: true, message: '请输入姓名' }]}>
                    <Input placeholder="请输入姓名" size="large" />
                </Form.Item>
                <Form.Item name="email" label="邮箱" rules={[{ required: true, type: 'email', message: '请输入有效邮箱' }]}>
                    <Input placeholder="your@email.com" size="large" />
                </Form.Item>
                <Form.Item name="phone" label="电话">
                    <Input placeholder="选填" size="large" />
                </Form.Item>
                <Form.Item name="address" label="收货地址" rules={[{ required: true, message: '请输入收货地址' }]}>
                    <Input.TextArea rows={3} placeholder="省 / 市 / 区 / 详细地址" />
                </Form.Item>
                <Form.Item name="payment_method" label="支付方式" initialValue="stripe">
                    <Radio.Group>
                        <Space direction="vertical">
                            <Radio value="stripe">Stripe（信用卡）</Radio>
                            <Radio value="wechat">微信支付</Radio>
                            <Radio value="alipay">支付宝</Radio>
                        </Space>
                    </Radio.Group>
                </Form.Item>
                <Button type="primary" size="large" block onClick={submit} loading={step === 1}>
                    提交订单
                </Button>
            </Form>
        </Card>
    );
}