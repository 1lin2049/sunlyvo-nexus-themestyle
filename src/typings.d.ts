declare module '*.less';
declare module '*.css';
declare module '*.svg';
declare module '*.png';

declare global {
  interface Window {
    SLV_ADMIN_CONFIG?: {
      apiBase: string;
      adminUrl: string;
      homeUrl: string;
      locale: string;
      nonceEndpoint: string;
      version?: string;
    };
    SLV_THEME?: 'light' | 'dark';
  }
}

export {};