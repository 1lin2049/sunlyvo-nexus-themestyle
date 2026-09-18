import { request } from '@umijs/max';

const API = '/wp-json/slv/v1';

export interface TemplateMeta {
    slug: string;
    label: string;
    description: string;
    user_switchable: boolean;
    admin_switchable: boolean;
    is_active: boolean;
}

export interface TemplateGroups {
    base: Record<string, TemplateMeta>;
    industry: Record<string, TemplateMeta>;
    aux: Record<string, TemplateMeta>;
}

export interface TemplatesResponse {
    current_template: string;
    current_theme: string;
    groups: TemplateGroups;
    all: string[];
}

export async function getTemplates(): Promise<TemplatesResponse> {
    return request(`${API}/templates`, { method: 'GET' });
}

export async function setSiteTemplate(template: string) {
    return request(`${API}/site/template`, {
        method: 'POST',
        data: { template },
    });
}

export async function setStoreTemplate(storeId: number, template: string) {
    return request(`${API}/store/${storeId}/template`, {
        method: 'POST',
        data: { template },
    });
}