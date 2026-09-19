import React from 'react';
import { PageContainer, ProCard, ProForm, ProFormText, ProFormSelect, ProFormSwitch, ProFormDigit } from '@ant-design/pro-components';
import { App, Button, Space } from 'antd';
import { SaveOutlined, ReloadOutlined, LinkOutlined } from '@ant-design/icons';
import { getHomeUrl, joinUrl } from '@/services/api';

export default () => {
  const { message } = App.useApp();
  const wpSettingsUrl = joinUrl(getHomeUrl(), 'wp-admin/admin.php?page=slv-settings');

  return (
    <PageContainer
      title="平台设置"
      subTitle="配置平台全局参数、服务和行为"
      extra={[
        <Button
          key="wp-settings"
          icon={<LinkOutlined />}
          href={wpSettingsUrl}
          target="_blank"
        >
          WordPress 服务配置
        </Button>,
      ]}
    >
      <ProCard gutter={16} ghost>
        <ProCard title="基础设置" colSpan={12}>
          <ProForm
            onFinish={async () => {
              message.success('设置已保存（待接入）');
            }}
            submitter={{
              render: (props) => (
                <Space>
                  <Button type="primary" icon={<SaveOutlined />} onClick={() => props.submit()}>
                    保存
                  </Button>
                  <Button icon={<ReloadOutlined />} onClick={() => props.reset()}>
                    重置
                  </Button>
                </Space>
              ),
            }}
          >
            <ProFormText name="site_name" label="站点名称" initialValue="SunLyvo Nexus" />
            <ProFormText name="site_description" label="站点描述" />
            <ProFormSelect
              name="default_currency"
              label="默认货币"
              initialValue="CNY"
              options={[
                { label: '人民币 (CNY)', value: 'CNY' },
                { label: '美元 (USD)', value: 'USD' },
                { label: '欧元 (EUR)', value: 'EUR' },
              ]}
            />
            <ProFormSelect
              name="default_language"
              label="默认语言"
              initialValue="zh-CN"
              options={[
                { label: '简体中文', value: 'zh-CN' },
                { label: 'English', value: 'en-US' },
                { label: 'Deutsch', value: 'de-DE' },
              ]}
            />
          </ProForm>
        </ProCard>

        <ProCard title="商品设置" colSpan={12}>
          <ProForm
            onFinish={async () => {
              message.success('设置已保存（待接入）');
            }}
            submitter={{
              render: (props) => (
                <Space>
                  <Button type="primary" icon={<SaveOutlined />} onClick={() => props.submit()}>
                    保存
                  </Button>
                  <Button icon={<ReloadOutlined />} onClick={() => props.reset()}>
                    重置
                  </Button>
                </Space>
              ),
            }}
          >
            <ProFormDigit
              name="low_stock_threshold"
              label="库存预警阈值"
              initialValue={5}
              min={0}
            />
            <ProFormSwitch name="enable_reviews" label="启用商品评价" initialValue={true} />
            <ProFormSwitch name="enable_coupons" label="启用优惠券" initialValue={true} />
            <ProFormSwitch name="enable_wholesale" label="启用批发功能" initialValue={true} />
          </ProForm>
        </ProCard>

        <ProCard title="订单设置" colSpan={12}>
          <ProForm
            onFinish={async () => {
              message.success('设置已保存（待接入）');
            }}
            submitter={{
              render: (props) => (
                <Space>
                  <Button type="primary" icon={<SaveOutlined />} onClick={() => props.submit()}>
                    保存
                  </Button>
                  <Button icon={<ReloadOutlined />} onClick={() => props.reset()}>
                    重置
                  </Button>
                </Space>
              ),
            }}
          >
            <ProFormDigit
              name="order_auto_cancel_minutes"
              label="未支付自动取消（分钟）"
              initialValue={30}
              min={0}
            />
            <ProFormDigit
              name="order_auto_complete_days"
              label="自动确认收货（天）"
              initialValue={15}
              min={0}
            />
            <ProFormSwitch name="enable_guest_checkout" label="允许游客结账" initialValue={true} />
          </ProForm>
        </ProCard>

        <ProCard title="中台设置" colSpan={12}>
          <ProForm
            onFinish={async () => {
              message.success('设置已保存（待接入）');
            }}
            submitter={{
              render: (props) => (
                <Space>
                  <Button type="primary" icon={<SaveOutlined />} onClick={() => props.submit()}>
                    保存
                  </Button>
                  <Button icon={<ReloadOutlined />} onClick={() => props.reset()}>
                    重置
                  </Button>
                </Space>
              ),
            }}
          >
            <ProFormText name="admin_url" label="中台访问路径" initialValue="/app/" />
            <ProFormText name="admin_title" label="中台标题" initialValue="SunLyvo Nexus 管理后台" />
            <ProFormSelect
              name="admin_theme"
              label="中台主题"
              initialValue="light"
              options={[
                { label: '浅色', value: 'light' },
                { label: '深色', value: 'dark' },
              ]}
            />
          </ProForm>
        </ProCard>
      </ProCard>
    </PageContainer>
  );
};