import React from 'react';
import ReactDOM from 'react-dom/client';
import { ConfigProvider, App as AntdApp } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import CartPage from '../pages/CartPage';

const mount = document.getElementById('slv-react-cart');
if (mount) {
    ReactDOM.createRoot(mount).render(
        <React.StrictMode>
            <ConfigProvider locale={zhCN} theme={{ token: { colorPrimary: '#0066ff' } }}>
                <AntdApp>
                    <CartPage />
                </AntdApp>
            </ConfigProvider>
        </React.StrictMode>
    );
}