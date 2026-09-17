import { request } from '@umijs/max';

const API = '/wp-json/slv/v1';

export interface StyleMeta {
    slug: string;
    label: string;
    css: string;
    user_switchable: boolean;
    admin_switchable: boolean;
}

export interface StyleGroups {
    base: Record<string, StyleMeta>;
    industry: Record<string, StyleMeta>;
    aux: Record<string, StyleMeta>;
}

export interface StylesResponse {
    current_style: string;
    current_theme: string;
    groups: StyleGroups;
    all: string[];
}

export async function getStyles(): Promise<StylesResponse> {
    return request(`${API}/styles`, { method: 'GET' });
}

export async function setSiteStyle(style: string) {
    return request(`${API}/site/style`, {
        method: 'POST',
        data: { style },
    });
}

export async function setPageTypeStyles(mapping: Record<string, string>) {
    return request(`${API}/page-type/style`, {
        method: 'POST',
        data: { mapping },
    });
}

export async function setStoreStylePolicy(allow: boolean, allowed: string[]) {
    return request(`${API}/store/style-policy`, {
        method: 'POST',
        data: { allow_store_style: allow, allowed_styles: allowed },
    });
}