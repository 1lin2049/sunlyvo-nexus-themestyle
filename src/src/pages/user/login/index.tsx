import React from 'react';
import { LoginForm, ProFormText } from '@ant-design/pro-components';
import { UserOutlined, LockOutlined } from '@ant-design/icons';
import { App } from 'antd';
import { getBasePath } from '@/services/api';

export default () => {
    const { message } = App.useApp();
    const basePath = getBasePath();

    return (
        <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', minHeight: '100vh', background: 'linear-gradient(135deg, #f0f5ff, #e6f0ff)' }}>
            <LoginForm
                title="SunLyvo Nexus"
                subTitle="内容电商管理后台"
                onFinish={async (values) => {
                    try {
                        const form = new FormData();
                        form.append('log', values.username);
                        form.append('pwd', values.password);
                        form.append('wp-submit', 'Log In');
                        form.append('redirect_to', basePath);
                        form.append('testcookie', '1');
                        const res = await fetch('/wp-login.php', { method: 'POST', body: form, credentials: 'include' });
                        if (res.ok || res.redirected) {
                            message.success('登录成功');
                            window.location.href = basePath;
                        } else {
                            message.error('用户名或密码错误');
                        }
                    } catch {
                        message.error('登录失败');
                    }
                }}
            >
                <ProFormText name="username" fieldProps={{ size: 'large', prefix: <UserOutlined /> }} placeholder="用户名" rules={[{ required: true, message: '请输入用户名' }]} />
                <ProFormText.Password name="password" fieldProps={{ size: 'large', prefix: <LockOutlined /> }} placeholder="密码" rules={[{ required: true, message: '请输入密码' }]} />
            </LoginForm>
        </div>
    );
};
