import { request } from '@umijs/max';

export interface SLVAdminConfig {
    apiBase: string;
    adminUrl: string;
    homeUrl: string;
    locale: string;
}

declare global {
    interface Window {
        SLV_ADMIN_CONFIG?: SLVAdminConfig;
    }
}

function getApiBase(): string {
    if (typeof window !== 'undefined' && window.SLV_ADMIN_CONFIG?.apiBase) {
        return window.SLV_ADMIN_CONFIG.apiBase;
    }
    return '/wp-json/slv/v1';
}

export function getBasePath(): string {
    if (typeof window !== 'undefined' && window.SLV_ADMIN_CONFIG?.adminUrl) {
        try {
            return new URL(window.SLV_ADMIN_CONFIG.adminUrl).pathname;
        } catch {
            /* ignore */
        }
    }
    const match = window.location.pathname.match(/^(\/[^/]*\/)/);
    return match ? match[1] : '/';
}

export function getHomeUrl(): string {
    return window.SLV_ADMIN_CONFIG?.homeUrl || '/';
}

export async function apiGet<T>(path: string, params?: Record<string, unknown>): Promise<T> {
    return request(`${getApiBase()}${path}`, { method: 'GET', params });
}

export async function apiPost<T>(path: string, data?: unknown): Promise<T> {
    return request(`${getApiBase()}${path}`, { method: 'POST', data });
}