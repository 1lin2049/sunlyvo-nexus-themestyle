import { request } from '@umijs/max';

function getApiBase(): string {
    const cfg = (window as any).SLV_ADMIN_CONFIG;
    if (cfg && cfg.apiBase) return cfg.apiBase;
    return '/wp-json/slv/v1';
}

export function getBasePath(): string {
    const cfg = (window as any).SLV_ADMIN_CONFIG;
    if (cfg && cfg.adminUrl) {
        try { return new URL(cfg.adminUrl).pathname; } catch { /* ignore */ }
    }
    const match = window.location.pathname.match(/^(\/[^/]*\/)/);
    return match ? match[1] : '/';
}

export function getHomeUrl(): string {
    const cfg = (window as any).SLV_ADMIN_CONFIG;
    const home = cfg && cfg.homeUrl ? cfg.homeUrl : '/';
    return home.endsWith('/') ? home : home + '/';
}

export function joinUrl(base: string, path: string): string {
    const b = base.endsWith('/') ? base.slice(0, -1) : base;
    const p = path.startsWith('/') ? path.slice(1) : path;
    return b + '/' + p;
}

export async function apiGet<T>(path: string, params?: Record<string, unknown>): Promise<T> {
    return request(getApiBase() + path, { method: 'GET', params });
}

export async function apiPost<T>(path: string, data?: unknown): Promise<T> {
    return request(getApiBase() + path, { method: 'POST', data });
}
