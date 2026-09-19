import React, { useRef } from 'react';
import { PageContainer, ProTable, ProForm, ProFormText, ProFormSelect, ProFormSwitch } from '@ant-design/pro-components';
import type { ActionType, ProColumns } from '@ant-design/pro-components';
import { Tag, Button, Space, Popconfirm, App } from 'antd';
import { PlusOutlined, EditOutlined, DeleteOutlined } from '@ant-design/icons';
import { request } from '@umijs/max';
import { getApiBase } from '@/services/api';

interface UserItem {
  id: number;
  user_login: string;
  user_email: string;
  display_name: string;
  roles: string[];
  registered: string;
  status: number;
}

const ROLE_OPTIONS = [
  { label: '超级管理员', value: 'super_admin' },
  { label: '区域管理员', value: 'region_admin' },
  { label: '站长', value: 'station_master' },
  { label: '商户', value: 'vendor' },
  { label: '商户员工', value: 'vendor_staff' },
  { label: '创作者', value: 'creator' },
  { label: '企业管理员', value: 'company_admin' },
  { label: '企业采购员', value: 'company_buyer' },
  { label: '企业查看者', value: 'company_viewer' },
  { label: '批发客户', value: 'wholesale_customer' },
  { label: '待审核批发客户', value: 'pending_wholesale' },
  { label: '普通用户', value: 'customer' },
  { label: '订阅者', value: 'subscriber' },
];

const ROLE_COLORS: Record<string, string> = {
  super_admin: 'red',
  region_admin: 'volcano',
  station_master: 'orange',
  vendor: 'blue',
  creator: 'purple',
  company_admin: 'cyan',
  customer: 'default',
};

export default () => {
  const actionRef = useRef<ActionType>();
  const { message } = App.useApp();

  const columns: ProColumns<UserItem>[] = [
    { title: 'ID', dataIndex: 'id', width: 80, search: false },
    {
      title: '用户名',
      dataIndex: 'user_login',
      width: 140,
      copyable: true,
    },
    {
      title: '显示名称',
      dataIndex: 'display_name',
      width: 140,
    },
    {
      title: '邮箱',
      dataIndex: 'user_email',
      width: 200,
      copyable: true,
      ellipsis: true,
    },
    {
      title: '角色',
      dataIndex: 'roles',
      width: 180,
      search: false,
      render: (_, record) => (
        <Space size={4} wrap>
          {(record.roles || []).map((role) => (
            <Tag key={role} color={ROLE_COLORS[role] || 'default'}>
              {ROLE_OPTIONS.find((r) => r.value === role)?.label || role}
            </Tag>
          ))}
        </Space>
      ),
    },
    {
      title: '注册时间',
      dataIndex: 'registered',
      width: 160,
      valueType: 'dateTime',
      search: false,
      sorter: true,
    },
    {
      title: '状态',
      dataIndex: 'status',
      width: 100,
      valueType: 'select',
      valueEnum: {
        0: { text: '正常', status: 'Success' },
        1: { text: '待验证', status: 'Processing' },
        2: { text: '已禁用', status: 'Error' },
      },
    },
    {
      title: '操作',
      valueType: 'option',
      width: 160,
      fixed: 'right',
      render: (_, record) => [
        <a key="edit" onClick={() => message.info(`编辑用户 #${record.id}（待接入）`)}>
          <EditOutlined /> 编辑
        </a>,
        <Popconfirm
          key="delete"
          title="确定删除此用户？"
          onConfirm={() => message.info(`删除用户 #${record.id}（待接入）`)}
        >
          <a style={{ color: '#ff4d4f' }}>
            <DeleteOutlined /> 删除
          </a>
        </Popconfirm>,
      ],
    },
  ];

  return (
    <PageContainer
      title="用户管理"
      subTitle="管理平台所有注册用户、角色和权限"
      extra={[
        <Button
          key="create"
          type="primary"
          icon={<PlusOutlined />}
          onClick={() => message.info('新建用户（待接入）')}
        >
          新建用户
        </Button>,
      ]}
    >
      <ProTable<UserItem>
        actionRef={actionRef}
        rowKey="id"
        columns={columns}
        request={async (params, sort, filter) => {
          try {
            const res = await request(`${getApiBase()}/users`, {
              method: 'GET',
              params: {
                page: params.current,
                per_page: params.pageSize,
                search: params.user_login || params.display_name || '',
                role: params.roles || '',
                status: params.status,
                orderby: sort ? Object.keys(sort)[0] : 'registered',
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
        pagination={{ pageSize: 20, showSizeChanger: true }}
        search={{ labelWidth: 'auto' }}
        options={{ density: true, fullScreen: true, reload: true, setting: true }}
        toolBarRender={() => []}
        scroll={{ x: 1200 }}
        dateFormatter="string"
      />
    </PageContainer>
  );
};