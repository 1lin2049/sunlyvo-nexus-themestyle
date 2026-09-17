import React from 'react';
import ReactDOM from 'react-dom/client';
import { ConfigProvider, App as AntdApp } from 'antd';
import zhCN from 'antd/locale/zh_CN';
import CheckoutPage from '../pages/CheckoutPage';

const mount = document.getElementById('slv-react-checkout');
if (mount) {
    ReactDOM.createRoot(mount).render(
        <ConfigProvider locale={zhCN}>
            <AntdApp><CheckoutPage /></AntdApp>
        </ConfigProvider>
    );
}