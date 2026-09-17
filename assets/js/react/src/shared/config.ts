export interface SLVConfig {
    restUrl: string;
    nonce: string;
    homeUrl: string;
    userId: number;
    locale: string;
}

declare global {
    interface Window {
        SLV_CONFIG?: SLVConfig;
    }
}

export const config: SLVConfig = window.SLV_CONFIG || {
    restUrl: '/wp-json/slv/v1',
    nonce: '',
    homeUrl: '/',
    userId: 0,
    locale: 'zh-CN',
};