import React from 'react';
import ReactDOM from 'react-dom/client';
import { ConfigProvider, App as AntdApp } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import CheckoutPage from '../pages/CheckoutPage';

const mount = document.getElementById('slv-react-checkout');
if (mount) {
    ReactDOM.createRoot(mount).render(
        <React.StrictMode>
            <ConfigProvider locale={zhCN} theme={{ token: { colorPrimary: '#0066ff' } }}>
                <AntdApp>
                    <CheckoutPage />
                </AntdApp>
            </ConfigProvider>
        </React.StrictMode>
    );
}