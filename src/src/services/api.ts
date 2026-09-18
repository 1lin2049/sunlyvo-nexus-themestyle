import { request } from '@umijs/max';

const API_BASE = process.env.API_BASE || '/wp-json/slv/v1';

export interface ApiResponse<T> {
    success: boolean;
    data?: T;
    error?: string;
}

export async function get<T>(path: string, params?: Record<string, unknown>): Promise<T> {
    return request(`${API_BASE}${path}`, { method: 'GET', params });
}

export async function post<T>(path: string, data?: unknown): Promise<T> {
    return request(`${API_BASE}${path}`, { method: 'POST', data });
}