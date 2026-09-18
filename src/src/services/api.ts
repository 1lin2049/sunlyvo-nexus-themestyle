import { request } from '@umijs/max';

/**
 * 运行时配置接口
 */
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

/**
 * 从运行时配置读 API Base
 *
 * 优先级：
 *   1. window.SLV_ADMIN_CONFIG.apiBase（部署时通过 config.js 注入）
 *   2. 相对路径 /wp-json/slv/v1（同域部署默认值）
 */
function getApiBase(): string {
    if (typeof window !== 'undefined' && window.SLV_ADMIN_CONFIG?.apiBase) {
        return window.SLV_ADMIN_CONFIG.apiBase;
    }
    return '/wp-json/slv/v1';
}

/**
 * 获取中台部署路径前缀（用于内部链接）
 *
 * 从 publicPath 推断，如 /admin/ 或 /
 */
export function getBasePath(): string {
    if (typeof window !== 'undefined' && window.SLV_ADMIN_CONFIG?.adminUrl) {
        try {
            return new URL(window.SLV_ADMIN_CONFIG.adminUrl).pathname;
        } catch {
            // ignore
        }
    }
    // 从 window.location.pathname 推断
    const path = window.location.pathname;
    const match = path.match(/^(\/[^/]*\/)/);
    return match ? match[1] : '/';
}

export interface ApiResponse<T> {
    success: boolean;
    data?: T;
    error?: string;
}

export async function apiGet<T>(path: string, params?: Record<string, unknown>): Promise<T> {
    return request(`${getApiBase()}${path}`, { method: 'GET', params });
}

export async function apiPost<T>(path: string, data?: unknown): Promise<T> {
    return request(`${getApiBase()}${path}`, { method: 'POST', data });
}