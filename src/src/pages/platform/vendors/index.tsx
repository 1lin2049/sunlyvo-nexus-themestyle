import React, { useRef } from 'react';
import { PageContainer, ProTable, ProDescriptions } from '@ant-design/pro-components';
import type { ActionType, ProColumns } from '@ant-design/pro-components';
import { Tag, Button, Space, Drawer, App } from 'antd';
import { PlusOutlined, CheckOutlined, CloseOutlined, EyeOutlined } from '@ant-design/icons';
import { request } from '@umijs/max';
import { getApiBase } from '@/services/api';

interface VendorItem {
  id: number;
  store_name: string;
  store_slug: string;
  store_type: string;
  user_email: string;
  contact_phone: string;
  status: string;
  product_count: number;
  sales_count: number;
  created_at: string;
}

const STATUS_ENUM = {
  pending: { text: '待审核', status: 'Processing' as const },
  active: { text: '已通过', status: 'Success' as const },
  suspended: { text: '已暂停', status: 'Warning' as const },
  rejected: { text: '已拒绝', status: 'Error' as const },
};

export default () => {
  const actionRef = useRef<ActionType>();
  const [current, setCurrent] = React.useState<VendorItem | null>(null);
  const [drawerOpen, setDrawerOpen] = React.useState(false);
  const { message } = App.useApp();

  const columns: ProColumns<VendorItem>[] = [
    { title: 'ID', dataIndex: 'id', width: 80, search: false },
    { title: '店铺名称', dataIndex: 'store_name', width: 180, ellipsis: true },
    { title: '店铺标识', dataIndex: 'store_slug', width: 140, copyable: true },
    {
      title: '类型',
      dataIndex: 'store_type',
      width: 100,
      valueType: 'select',
      valueEnum: {
        vendor: '商户',
        creator: '创作者',
        station: '站长',
      },
    },
    { title: '联系邮箱', dataIndex: 'user_email', width: 200, ellipsis: true },
    {
      title: '状态',
      dataIndex: 'status',
      width: 110,
      valueType: 'select',
      valueEnum: STATUS_ENUM,
    },
    { title: '商品数', dataIndex: 'product_count', width: 90, search: false, sorter: true },
    { title: '销量', dataIndex: 'sales_count', width: 90, search: false, sorter: true },
    {
      title: '入驻时间',
      dataIndex: 'created_at',
      width: 160,
      valueType: 'dateTime',
      search: false,
      sorter: true,
    },
    {
      title: '操作',
      valueType: 'option',
      width: 200,
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
        record.status === 'pending' && (
          <a key="approve" style={{ color: '#52c41a' }} onClick={() => message.success('审核通过（待接入）')}>
            <CheckOutlined /> 通过
          </a>
        ),
        record.status === 'pending' && (
          <a key="reject" style={{ color: '#ff4d4f' }} onClick={() => message.error('已拒绝（待接入）')}>
            <CloseOutlined /> 拒绝
          </a>
        ),
      ].filter(Boolean),
    },
  ];

  return (
    <PageContainer
      title="商户管理"
      subTitle="管理平台入驻商户、店铺和审核流程"
      extra={[
        <Button key="create" type="primary" icon={<PlusOutlined />}>
          邀请入驻
        </Button>,
      ]}
    >
      <ProTable<VendorItem>
        actionRef={actionRef}
        rowKey="id"
        columns={columns}
        request={async (params, sort) => {
          try {
            const res = await request(`${getApiBase()}/vendors`, {
              method: 'GET',
              params: {
                page: params.current,
                per_page: params.pageSize,
                search: params.store_name || '',
                status: params.status || '',
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
        scroll={{ x: 1300 }}
      />

      <Drawer
        title="商户详情"
        width={520}
        open={drawerOpen}
        onClose={() => setDrawerOpen(false)}
      >
        {current && (
          <ProDescriptions column={1} title={current.store_name}>
            <ProDescriptions.Item label="ID">{current.id}</ProDescriptions.Item>
            <ProDescriptions.Item label="店铺标识">{current.store_slug}</ProDescriptions.Item>
            <ProDescriptions.Item label="类型">{current.store_type}</ProDescriptions.Item>
            <ProDescriptions.Item label="联系邮箱">{current.user_email}</ProDescriptions.Item>
            <ProDescriptions.Item label="联系电话">{current.contact_phone}</ProDescriptions.Item>
            <ProDescriptions.Item label="状态">
              <Tag color={STATUS_ENUM[current.status as keyof typeof STATUS_ENUM]?.status === 'Success' ? 'green' : 'orange'}>
                {STATUS_ENUM[current.status as keyof typeof STATUS_ENUM]?.text || current.status}
              </Tag>
            </ProDescriptions.Item>
            <ProDescriptions.Item label="商品数">{current.product_count}</ProDescriptions.Item>
            <ProDescriptions.Item label="销量">{current.sales_count}</ProDescriptions.Item>
            <ProDescriptions.Item label="入驻时间">{current.created_at}</ProDescriptions.Item>
          </ProDescriptions>
        )}
      </Drawer>
    </PageContainer>
  );
};