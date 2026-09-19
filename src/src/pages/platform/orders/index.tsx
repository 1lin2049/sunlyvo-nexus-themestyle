import React, { useRef } from 'react';
import { PageContainer, ProTable, ProDescriptions } from '@ant-design/pro-components';
import type { ActionType, ProColumns } from '@ant-design/pro-components';
import { Tag, Space, Drawer, Statistic, Card, Row, Col, App } from 'antd';
import { EyeOutlined } from '@ant-design/icons';
import { request } from '@umijs/max';
import { getApiBase } from '@/services/api';

interface OrderItem {
  id: number;
  order_number: string;
  customer_name: string;
  customer_email: string;
  total: number;
  currency: string;
  status: string;
  payment_status: string;
  created_at: string;
}

const ORDER_STATUS = {
  pending: { text: '待支付', status: 'Warning' as const },
  processing: { text: '处理中', status: 'Processing' as const },
  completed: { text: '已完成', status: 'Success' as const },
  cancelled: { text: '已取消', status: 'Default' as const },
  refunded: { text: '已退款', status: 'Error' as const },
};

const PAYMENT_STATUS = {
  pending: { text: '待支付', status: 'Warning' as const },
  paid: { text: '已支付', status: 'Success' as const },
  failed: { text: '失败', status: 'Error' as const },
  refunded: { text: '已退款', status: 'Default' as const },
};

export default () => {
  const actionRef = useRef<ActionType>();
  const [current, setCurrent] = React.useState<OrderItem | null>(null);
  const [drawerOpen, setDrawerOpen] = React.useState(false);
  const { message } = App.useApp();

  const columns: ProColumns<OrderItem>[] = [
    {
      title: '订单号',
      dataIndex: 'order_number',
      width: 180,
      copyable: true,
      fixed: 'left',
    },
    { title: '客户', dataIndex: 'customer_name', width: 120, ellipsis: true },
    {
      title: '邮箱',
      dataIndex: 'customer_email',
      width: 200,
      ellipsis: true,
      search: false,
    },
    {
      title: '金额',
      dataIndex: 'total',
      width: 120,
      search: false,
      sorter: true,
      render: (_, r) => `${r.currency} ${Number(r.total).toFixed(2)}`,
    },
    {
      title: '订单状态',
      dataIndex: 'status',
      width: 110,
      valueType: 'select',
      valueEnum: ORDER_STATUS,
    },
    {
      title: '支付状态',
      dataIndex: 'payment_status',
      width: 110,
      valueType: 'select',
      valueEnum: PAYMENT_STATUS,
    },
    {
      title: '下单时间',
      dataIndex: 'created_at',
      width: 160,
      valueType: 'dateTime',
      search: false,
      sorter: true,
    },
    {
      title: '操作',
      valueType: 'option',
      width: 100,
      fixed: 'right',
      render: (_, record) => [
        <a
          key="view"
          onClick={() => {
            setCurrent(record);
            setDrawerOpen(true);
          }}
        >
          <EyeOutlined /> 详情
        </a>,
      ],
    },
  ];

  return (
    <PageContainer title="订单管理" subTitle="管理平台所有订单、支付和退款">
      <Row gutter={16} style={{ marginBottom: 16 }}>
        <Col span={6}>
          <Card><Statistic title="今日订单" value={0} /></Card>
        </Col>
        <Col span={6}>
          <Card><Statistic title="今日收入" value={0} prefix="¥" /></Card>
        </Col>
        <Col span={6}>
          <Card><Statistic title="待处理" value={0} /></Card>
        </Col>
        <Col span={6}>
          <Card><Statistic title="累计订单" value={0} /></Card>
        </Col>
      </Row>

      <ProTable<OrderItem>
        actionRef={actionRef}
        rowKey="id"
        columns={columns}
        request={async (params, sort) => {
          try {
            const res = await request(`${getApiBase()}/orders`, {
              method: 'GET',
              params: {
                page: params.current,
                per_page: params.pageSize,
                search: params.order_number || params.customer_email || '',
                status: params.status || '',
                payment_status: params.payment_status || '',
                orderby: sort ? Object.keys(sort)[0] : 'created_at',
                order: sort ? Object.values(sort)[0] : 'desc',
              },
            });
            return {
              data: res?.data || [],
              success: res?.success !== false,
              total: res?.total || 0,
            };
          } catch {
            return { data: [], success: false, total: 0 };
          }
        }}
        pagination={{ pageSize: 20 }}
        search={{ labelWidth: 'auto' }}
        options={{ density: true, fullScreen: true, reload: true, setting: true }}
        scroll={{ x: 1100 }}
      />

      <Drawer
        title="订单详情"
        width={600}
        open={drawerOpen}
        onClose={() => setDrawerOpen(false)}
      >
        {current && (
          <ProDescriptions column={1} title={current.order_number}>
            <ProDescriptions.Item label="订单号">{current.order_number}</ProDescriptions.Item>
            <ProDescriptions.Item label="客户">{current.customer_name}</ProDescriptions.Item>
            <ProDescriptions.Item label="邮箱">{current.customer_email}</ProDescriptions.Item>
            <ProDescriptions.Item label="金额">
              {current.currency} {Number(current.total).toFixed(2)}
            </ProDescriptions.Item>
            <ProDescriptions.Item label="订单状态">
              <Tag>{ORDER_STATUS[current.status as keyof typeof ORDER_STATUS]?.text || current.status}</Tag>
            </ProDescriptions.Item>
            <ProDescriptions.Item label="支付状态">
              <Tag>{PAYMENT_STATUS[current.payment_status as keyof typeof PAYMENT_STATUS]?.text || current.payment_status}</Tag>
            </ProDescriptions.Item>
            <ProDescriptions.Item label="下单时间">{current.created_at}</ProDescriptions.Item>
          </ProDescriptions>
        )}
      </Drawer>
    </PageContainer>
  );
};